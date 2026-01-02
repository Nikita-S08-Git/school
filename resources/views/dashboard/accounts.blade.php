@extends('layouts.app')
@section('title', 'Accounts Dashboard')
@section('page-title', 'Accounts Dashboard')
@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-credit-card fa-2x mb-2"></i>
                <h5>Fee Collection</h5>
                <p class="mb-0">₹2,50,000</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle fa-2x mb-2"></i>
                <h5>Pending Fees</h5>
                <p class="mb-0">₹50,000</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-receipt fa-2x mb-2"></i>
                <h5>Receipts</h5>
                <p class="mb-0">150</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="bi bi-cash fa-2x mb-2"></i>
                <h5>Expenses</h5>
                <p class="mb-0">₹75,000</p>
            </div>
        </div>
    </div>
</div>
@endsection