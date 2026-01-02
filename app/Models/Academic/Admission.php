<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use App\Models\User\Student;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'status',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'caste',
        'category',
        'aadhar_number',
        'mobile_number',
        'email',
        'current_address',
        'permanent_address',
        'program_id',
        'academic_year',
        'division_id',
        'academic_session_id',
        'tenth_board',
        'tenth_percentage',
        'twelfth_board',
        'twelfth_percentage',
        'student_id',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'application_fee',
        'application_fee_paid'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
        'tenth_percentage' => 'decimal:2',
        'twelfth_percentage' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'application_fee_paid' => 'boolean'
    ];

    // Relationships
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeByAcademicSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    // Status checks
    public function isApplied(): bool
    {
        return $this->status === 'applied';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isEnrolled(): bool
    {
        return $this->status === 'enrolled';
    }

    public function canBeVerified(): bool
    {
        return $this->isApplied() && $this->application_fee_paid;
    }

    public function canBeEnrolled(): bool
    {
        return $this->isVerified();
    }

    // Document verification status
    public function getRequiredDocumentsCount(): int
    {
        $required = ['tenth_marksheet', 'passport_photo', 'signature'];
        
        if ($this->category !== 'general') {
            $required[] = 'caste_certificate';
        }
        
        return count($required);
    }

    public function getVerifiedDocumentsCount(): int
    {
        return $this->documents()->where('is_verified', true)->count();
    }

    public function hasAllDocumentsVerified(): bool
    {
        return $this->getVerifiedDocumentsCount() >= $this->getRequiredDocumentsCount();
    }
}