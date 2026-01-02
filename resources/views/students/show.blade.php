@extends('layouts.app')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
            <li class="breadcrumb-item active">{{ $student->first_name }} {{ $student->last_name }}</li>
        </ol>
    </nav>
    <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit Student
    </a>
</div>

<div class="row">
    <!-- Student Profile -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-body text-center">
                @if($student->photo_url)
                    <img src="{{ asset($student->photo_url) }}" class="rounded-circle mb-3" width="120" height="120">
                @else
                    <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                        <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                    </div>
                @endif
                
                <h4>{{ $student->first_name }} {{ $student->last_name }}</h4>
                @if($student->middle_name)
                    <p class="text-muted">{{ $student->middle_name }}</p>
                @endif
                
                <span class="badge bg-{{ $student->student_status === 'active' ? 'success' : 'secondary' }} mb-3">
                    {{ ucfirst($student->student_status) }}
                </span>
                
                <div class="row text-center">
                    <div class="col-6">
                        <strong>Admission No</strong><br>
                        <span class="text-muted">{{ $student->admission_number ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Roll No</strong><br>
                        <span class="text-muted">{{ $student->roll_number ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Details -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Date of Birth:</strong><br>
                        <span class="text-muted">{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender:</strong><br>
                        <span class="text-muted">{{ ucfirst($student->gender) }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Mobile Number:</strong><br>
                        <span class="text-muted">{{ $student->mobile_number ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-muted">{{ $student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Category:</strong><br>
                        <span class="text-muted">{{ strtoupper($student->category) }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Admission Date:</strong><br>
                        <span class="text-muted">{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Academic Information -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Program:</strong><br>
                    <span class="text-muted">{{ $student->program->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <strong>Division:</strong><br>
                    <span class="text-muted">{{ $student->division->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <strong>Academic Year:</strong><br>
                    <span class="text-muted">{{ $student->academic_year ?? 'N/A' }}</span>
                </div>
                <div class="mb-0">
                    <strong>Academic Session:</strong><br>
                    <span class="text-muted">{{ $student->academicSession->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guardians -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Guardians</h6>
            </div>
            <div class="card-body">
                @forelse($student->guardians as $guardian)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <strong>{{ $guardian->first_name }} {{ $guardian->last_name }}</strong>
                        <span class="badge bg-info ms-2">{{ ucfirst($guardian->relation) }}</span><br>
                        <small class="text-muted">
                            <i class="bi bi-telephone"></i> {{ $guardian->mobile_number }}<br>
                            @if($guardian->email)
                                <i class="bi bi-envelope"></i> {{ $guardian->email }}<br>
                            @endif
                            @if($guardian->occupation)
                                <i class="bi bi-briefcase"></i> {{ $guardian->occupation }}
                            @endif
                        </small>
                    </div>
                @empty
                    <p class="text-muted">No guardian information available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Documents -->
@if($student->documents->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Documents</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($student->signature_url)
                        <div class="col-md-6 text-center">
                            <strong>Signature</strong><br>
                            <img src="{{ asset($student->signature_url) }}" class="img-thumbnail mt-2" style="max-height: 100px;">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection