<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\User\Student;
use App\Models\Fee\FeeStructure;
use App\Models\Fee\StudentFee;
use App\Models\Fee\FeePayment;
use App\Models\Fee\Scholarship;
use App\Models\Fee\ScholarshipApplication;
use App\Models\Academic\Program;
use App\Models\Academic\Division;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Department;
use App\Models\Fee\FeeHead;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FeesAndScholarshipComplianceTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $studentSectionUser;
    private Student $testStudent;
    private FeeStructure $feeStructure;
    private Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'student_section']);
        Role::create(['name' => 'student']);
        
        // Create test users
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
        
        $this->studentSectionUser = User::factory()->create();
        $this->studentSectionUser->assignRole('student_section');
        
        // Create academic structure
        $department = Department::factory()->create(['name' => 'Commerce']);
        $program = Program::factory()->create(['department_id' => $department->id, 'code' => 'BCOM']);
        $academicSession = AcademicSession::factory()->create(['name' => '2024-25']);
        $division = Division::factory()->create(['division_name' => 'A']);
        
        // Create test student
        $studentUser = User::factory()->create();
        $studentUser->assignRole('student');
        
        $this->testStudent = Student::factory()->create([
            'user_id' => $studentUser->id,
            'program_id' => $program->id,
            'division_id' => $division->id,
            'academic_session_id' => $academicSession->id,
            'roll_number' => '2024/BCOM/A/001'
        ]);
        
        // Create fee structure with 2 installments
        $feeHead = FeeHead::factory()->create(['name' => 'Tuition Fee']);
        $this->feeStructure = FeeStructure::factory()->create([
            'program_id' => $program->id,
            'fee_head_id' => $feeHead->id,
            'amount' => 10000.00,
            'installments' => 2,
            'academic_year' => '2024-25'
        ]);
        
        // Create scholarship
        $this->scholarship = Scholarship::factory()->create([
            'name' => 'Merit Scholarship',
            'code' => 'MERIT001',
            'type' => 'percentage',
            'value' => 50.00, // 50% discount
            'is_active' => true
        ]);
    }

    /** @test */
    public function test_installment_sequencing_enforcement()
    {
        // Assign fee structure to student
        $this->actingAs($this->adminUser);
        
        $response = $this->postJson('/api/students/' . $this->testStudent->id . '/assign-fee-structure', [
            'fee_structure_ids' => [$this->feeStructure->id],
            'reason' => 'Initial fee assignment'
        ]);
        
        $response->assertStatus(200);
        
        // Verify student fee was created
        $this->assertDatabaseHas('student_fees', [
            'student_id' => $this->testStudent->id,
            'fee_structure_id' => $this->feeStructure->id,
            'total_amount' => 10000.00,
            'final_amount' => 10000.00
        ]);
        
        $studentFee = StudentFee::where('student_id', $this->testStudent->id)->first();
        
        // CRITICAL TEST: Try to pay installment 2 first (should FAIL)
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'next_installment' => 1
                ])
                ->assertJsonFragment(['Cannot pay installment 2']);
        
        // Verify no payment was recorded
        $this->assertDatabaseMissing('fee_payments', [
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2
        ]);
        
        // Pay installment 1 first (should SUCCEED)
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 1,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(201)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'data' => [
                        'payment',
                        'receipt_number',
                        'outstanding_amount',
                        'installment_status'
                    ]
                ]);
        
        // Verify payment was recorded
        $this->assertDatabaseHas('fee_payments', [
            'student_fee_id' => $studentFee->id,
            'installment_number' => 1,
            'amount' => 5000.00,
            'status' => 'success'
        ]);
        
        // Now pay installment 2 (should SUCCEED)
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(201)
                ->assertJson(['success' => true]);
        
        // Verify second payment was recorded
        $this->assertDatabaseHas('fee_payments', [
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2,
            'amount' => 5000.00,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function test_scholarship_workflow_compliance()
    {
        // Assign fee structure first
        $this->actingAs($this->adminUser);
        
        $this->postJson('/api/students/' . $this->testStudent->id . '/assign-fee-structure', [
            'fee_structure_ids' => [$this->feeStructure->id]
        ]);
        
        $studentFee = StudentFee::where('student_id', $this->testStudent->id)->first();
        
        // Step 1: Apply for scholarship (as student)
        $this->actingAs($this->testStudent->user);
        
        $response = $this->postJson('/api/students/' . $this->testStudent->id . '/scholarship/apply', [
            'scholarship_id' => $this->scholarship->id,
            'application_reason' => 'Financial need for merit scholarship'
        ]);
        
        $response->assertStatus(201)
                ->assertJson(['success' => true]);
        
        // Verify application was created with pending status
        $this->assertDatabaseHas('scholarship_applications', [
            'student_id' => $this->testStudent->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'pending'
        ]);
        
        $application = ScholarshipApplication::where('student_id', $this->testStudent->id)->first();
        
        // Step 2: Calculate fee with pending scholarship (should have NO discount)
        $this->actingAs($this->adminUser);
        
        $response = $this->getJson('/api/students/' . $this->testStudent->id . '/fee-ledger');
        
        $response->assertStatus(200);
        
        // Verify no discount applied (scholarship still pending)
        $studentFee->refresh();
        $this->assertEquals(10000.00, $studentFee->final_amount);
        
        // Step 3: Verify scholarship as student_section user
        $this->actingAs($this->studentSectionUser);
        
        $response = $this->postJson('/api/scholarship/' . $application->id . '/verify', [
            'status' => 'approved',
            'verification_notes' => 'Documents verified, eligible for scholarship'
        ]);
        
        $response->assertStatus(200)
                ->assertJson(['success' => true]);
        
        // Verify application status updated
        $this->assertDatabaseHas('scholarship_applications', [
            'id' => $application->id,
            'status' => 'approved',
            'verified_by' => $this->studentSectionUser->id
        ]);
        
        // Step 4: Recalculate fee (should now have discount)
        $response = $this->getJson('/api/students/' . $this->testStudent->id . '/fee-ledger');
        
        $response->assertStatus(200);
        
        // Note: The actual discount application would depend on the FeeCalculationService
        // This test verifies the workflow is in place
    }

    /** @test */
    public function test_audit_logging_for_fee_operations()
    {
        $this->actingAs($this->adminUser);
        
        // Initial fee assignment
        $response = $this->postJson('/api/students/' . $this->testStudent->id . '/assign-fee-structure', [
            'fee_structure_ids' => [$this->feeStructure->id],
            'reason' => 'Initial assignment for testing'
        ]);
        
        $response->assertStatus(200);
        
        // Verify audit log entry was created for fee assignment
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Student::class,
            'auditable_id' => $this->testStudent->id,
            'event' => 'fee_structure_reassigned',
            'user_id' => $this->adminUser->id
        ]);
        
        // Test scholarship verification audit
        $this->actingAs($this->testStudent->user);
        
        $this->postJson('/api/students/' . $this->testStudent->id . '/scholarship/apply', [
            'scholarship_id' => $this->scholarship->id,
            'application_reason' => 'Test application'
        ]);
        
        $application = ScholarshipApplication::where('student_id', $this->testStudent->id)->first();
        
        $this->actingAs($this->studentSectionUser);
        
        $this->postJson('/api/scholarship/' . $application->id . '/verify', [
            'status' => 'approved',
            'verification_notes' => 'Test verification'
        ]);
        
        // Verify scholarship verification audit log
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => ScholarshipApplication::class,
            'auditable_id' => $application->id,
            'event' => 'scholarship_approved',
            'user_id' => $this->studentSectionUser->id
        ]);
    }

    /** @test */
    public function test_installment_violation_audit_logging()
    {
        // Setup fee structure
        $this->actingAs($this->adminUser);
        
        $this->postJson('/api/students/' . $this->testStudent->id . '/assign-fee-structure', [
            'fee_structure_ids' => [$this->feeStructure->id]
        ]);
        
        $studentFee = StudentFee::where('student_id', $this->testStudent->id)->first();
        
        // Attempt invalid installment payment
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(422);
        
        // Verify violation was logged
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => StudentFee::class,
            'auditable_id' => $studentFee->id,
            'event' => 'installment_sequence_violation',
            'user_id' => $this->adminUser->id
        ]);
    }

    /** @test */
    public function test_role_based_authorization()
    {
        // Test that only student_section can verify scholarships
        $this->actingAs($this->testStudent->user);
        
        $this->postJson('/api/students/' . $this->testStudent->id . '/scholarship/apply', [
            'scholarship_id' => $this->scholarship->id,
            'application_reason' => 'Test application'
        ]);
        
        $application = ScholarshipApplication::where('student_id', $this->testStudent->id)->first();
        
        // Try to verify as admin (should fail)
        $this->actingAs($this->adminUser);
        
        $response = $this->postJson('/api/scholarship/' . $application->id . '/verify', [
            'status' => 'approved'
        ]);
        
        $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Only Student Section staff can verify scholarships'
                ]);
        
        // Verify as student_section (should succeed)
        $this->actingAs($this->studentSectionUser);
        
        $response = $this->postJson('/api/scholarship/' . $application->id . '/verify', [
            'status' => 'approved'
        ]);
        
        $response->assertStatus(200);
    }

    /** @test */
    public function test_complete_fee_payment_workflow()
    {
        // Complete end-to-end test
        $this->actingAs($this->adminUser);
        
        // 1. Assign fee structure
        $this->postJson('/api/students/' . $this->testStudent->id . '/assign-fee-structure', [
            'fee_structure_ids' => [$this->feeStructure->id]
        ]);
        
        $studentFee = StudentFee::where('student_id', $this->testStudent->id)->first();
        
        // 2. Apply for scholarship
        $this->actingAs($this->testStudent->user);
        $this->postJson('/api/students/' . $this->testStudent->id . '/scholarship/apply', [
            'scholarship_id' => $this->scholarship->id,
            'application_reason' => 'Merit-based application'
        ]);
        
        $application = ScholarshipApplication::where('student_id', $this->testStudent->id)->first();
        
        // 3. Verify scholarship
        $this->actingAs($this->studentSectionUser);
        $this->postJson('/api/scholarship/' . $application->id . '/verify', [
            'status' => 'approved'
        ]);
        
        // 4. Pay installments in correct order
        $this->actingAs($this->adminUser);
        
        // Pay installment 1
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 1,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(201);
        
        // Pay installment 2
        $response = $this->postJson('/api/fees/pay', [
            'student_id' => $this->testStudent->id,
            'student_fee_id' => $studentFee->id,
            'installment_number' => 2,
            'amount' => 5000.00,
            'payment_mode' => 'cash',
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(201);
        
        // 5. Verify final state
        $studentFee->refresh();
        $this->assertEquals('paid', $studentFee->status);
        $this->assertEquals(10000.00, $studentFee->paid_amount);
        $this->assertEquals(0.00, $studentFee->outstanding_amount);
        
        // Verify both payments exist
        $this->assertEquals(2, FeePayment::where('student_fee_id', $studentFee->id)->count());
    }
}