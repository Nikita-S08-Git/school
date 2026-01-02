<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['program', 'division', 'academicSession'])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('roll_number', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%");
                });
            })
            ->paginate(25);

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function show($id)
    {
        $student = Student::with(['program', 'division', 'academicSession', 'guardians', 'user'])
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'mobile_number' => 'nullable|string|max:15',
            'program_id' => 'required|integer',
            'division_id' => 'required|integer',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'academic_year' => 'required|string|in:FY,SY,TY',
            'academic_session_id' => 'required|integer',
            'admission_date' => 'required|date',
            'category' => 'required|string|in:general,obc,sc,st'
        ]);

        // Generate admission number
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastStudent ? (intval(substr($lastStudent->admission_number, -4)) + 1) : 1;
        $admissionNumber = $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Generate roll number based on division and academic year
        $rollNumber = $validated['academic_year'] . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        // Add generated and default values
        $studentData = array_merge($validated, [
            'admission_number' => $admissionNumber,
            'roll_number' => $rollNumber,
            'student_status' => 'active',
            'user_id' => auth()->id() ?? 1
        ]);

        $student = Student::create($studentData);
        $student->load(['program', 'division', 'academicSession']);
        
        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'mobile_number' => 'nullable|string|max:15',
            'program_id' => 'required|integer',
            'division_id' => 'required|integer',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'academic_year' => 'required|string|in:FY,SY,TY',
            'academic_session_id' => 'required|integer',
            'admission_date' => 'required|date',
            'category' => 'required|string|in:general,obc,sc,st'
        ]);

        $student->update($validated);
        $student->load(['program', 'division', 'academicSession']);
        
        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }
}
