@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('page-title', 'Teacher Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-people fa-2x mb-2"></i>
                <h5>My Students</h5>
                <p class="mb-0">120</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-book fa-2x mb-2"></i>
                <h5>Subjects</h5>
                <p class="mb-0">3</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check fa-2x mb-2"></i>
                <h5>Classes Today</h5>
                <p class="mb-0">4</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-check fa-2x mb-2"></i>
                <h5>Assignments</h5>
                <p class="mb-0">8</p>
            </div>
        </div>
    </div>
</div>
@endsection