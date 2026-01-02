@extends('layouts.app')
@section('title', 'Library Dashboard')
@section('page-title', 'Library Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-book fa-2x mb-2"></i>
                <h5>Total Books</h5>
                <p class="mb-0">5,000</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-bookmark fa-2x mb-2"></i>
                <h5>Issued Books</h5>
                <p class="mb-0">250</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-clock fa-2x mb-2"></i>
                <h5>Overdue</h5>
                <p class="mb-0">15</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-people fa-2x mb-2"></i>
                <h5>Members</h5>
                <p class="mb-0">800</p>
            </div>
        </div>
    </div>
</div>
@endsection