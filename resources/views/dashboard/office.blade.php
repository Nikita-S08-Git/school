@extends('layouts.app')
@section('title', 'Office Dashboard')
@section('page-title', 'Office Staff Dashboard')
@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-person-plus fa-2x mb-2"></i>
                <h5>New Admissions</h5>
                <p class="mb-0">{{ \App\Models\Academic\Admission::where('status', 'pending')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-people fa-2x mb-2"></i>
                <h5>Total Students</h5>
                <p class="mb-0">{{ \App\Models\Academic\Student::count() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-file-text fa-2x mb-2"></i>
                <h5>Documents</h5>
                <p class="mb-0">25</p>
            </div>
        </div>
    </div>
</div>
@endsection