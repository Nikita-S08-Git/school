<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Models\User\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $students = Student::with(['program', 'division'])
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%");
                });
            })
            ->paginate(20);

        return view('dashboard.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load(['program', 'division', 'guardians', 'documents']);
        return view('students.show', compact('student'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'program_id' => 'required|exists:programs,id',
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'admission_date' => 'required|date',
        ]);

        $student = Student::create($validated);
        
        return redirect()->route('students.show', $student)
            ->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'program_id' => 'required|exists:programs,id',
            'division_id' => 'required|exists:divisions,id',
        ]);

        $student->update($validated);
        
        return redirect()->route('students.show', $student)
            ->with('success', 'Student updated successfully.');
    }
}