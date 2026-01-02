@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Student Dashboard')

@section('content')
@php
    $student = auth()->user()->student ?? null;
@endphp

@if($student)
<!-- Student Profile Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($student->photo_url)
                            <img src="{{ asset($student->photo_url) }}" class="rounded-circle" width="80" height="80" alt="Student Photo">
                        @else
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h4>{{ $student->first_name }} {{ $student->last_name }}</h4>
                        <p class="mb-1"><strong>Admission No:</strong> {{ $student->admission_number ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Program:</strong> {{ $student->program->name ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Division:</strong> {{ $student->division->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check fa-2x mb-2"></i>
                <h5>Attendance</h5>
                <p class="mb-0">85%</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-credit-card fa-2x mb-2"></i>
                <h5>Fees</h5>
                <p class="mb-0">Paid</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-book fa-2x mb-2"></i>
                <h5>Subjects</h5>
                <p class="mb-0">6 Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-trophy fa-2x mb-2"></i>
                <h5>Grade</h5>
                <p class="mb-0">A</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-check"></i> View Attendance
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="bi bi-credit-card"></i> Fee Details
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="bi bi-clock"></i> Timetable
                    </a>
                    <a href="#" class="btn btn-outline-warning">
                        <i class="bi bi-file-text"></i> Study Materials
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Recent Announcements</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Exam Schedule Released</strong><br>
                    <small class="text-muted">Mid-term examinations will begin from January 15th, 2025.</small>
                </div>
                <div class="mb-3">
                    <strong>Library Hours Extended</strong><br>
                    <small class="text-muted">Library will remain open until 8 PM during exam period.</small>
                </div>
                <div class="mb-3">
                    <strong>Fee Payment Reminder</strong><br>
                    <small class="text-muted">Next installment due on January 31st, 2025.</small>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-warning">
    <h5>Profile Incomplete</h5>
    <p>Your student profile is not set up yet. Please contact the administration office.</p>
</div>
@endif
@endsection