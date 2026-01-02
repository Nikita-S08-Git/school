<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Academic\Admission;
use App\Services\AdmissionService;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    protected $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function index(Request $request)
    {
        $admissions = Admission::with(['program', 'division'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('application_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admissions.index', compact('admissions'));
    }

    public function show(Admission $admission)
    {
        $admission->load(['program', 'division', 'documents']);
        return view('admissions.show', compact('admission'));
    }

    public function showApplyForm()
    {
        return view('admissions.apply');
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'program_id' => 'required|exists:programs,id',
            'division_id' => 'required|exists:divisions,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'mobile_number' => 'required|string|max:15',
            'email' => 'required|email|unique:admissions,email',
        ]);

        $admission = Admission::create($validated + [
            'status' => 'pending',
            'application_number' => 'APP' . date('Y') . str_pad(Admission::count() + 1, 4, '0', STR_PAD_LEFT)
        ]);

        return redirect()->route('admissions.show', $admission)
            ->with('success', 'Application submitted successfully!');
    }

    public function verify(Request $request, Admission $admission)
    {
        $admission->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Admission verified successfully.');
    }

    public function reject(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $admission->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Admission rejected.');
    }
}