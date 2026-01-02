@extends('layouts.app')

@section('title', 'Principal Dashboard')
@section('page-title', 'Principal Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User\Student::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pending Admissions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Academic\Admission::where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-plus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Programs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Academic\Program::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Staff Members</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::whereHas('roles', function($q) { $q->whereIn('name', ['teacher', 'office', 'librarian']); })->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-badge fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-primary">
                        <i class="bi bi-people"></i> Manage Students
                    </a>
                    <a href="{{ route('admissions.index') }}" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Review Admissions
                    </a>
                    <a href="#" class="btn btn-info">
                        <i class="bi bi-calendar-check"></i> View Attendance
                    </a>
                    <a href="#" class="btn btn-warning">
                        <i class="bi bi-credit-card"></i> Fee Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Admissions</h6>
            </div>
            <div class="card-body">
                @php
                    $recentAdmissions = \App\Models\Academic\Admission::latest()->take(5)->get();
                @endphp
                
                @forelse($recentAdmissions as $admission)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $admission->first_name }} {{ $admission->last_name }}</strong><br>
                            <small class="text-muted">{{ $admission->program_name ?? 'N/A' }}</small>
                        </div>
                        <span class="badge bg-{{ $admission->status === 'pending' ? 'warning' : ($admission->status === 'verified' ? 'success' : 'danger') }}">
                            {{ ucfirst($admission->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted">No recent admissions</p>
                @endforelse
                
                <div class="text-center mt-3">
                    <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endpush
@endsection