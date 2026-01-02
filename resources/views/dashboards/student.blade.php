@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div x-data="studentDashboard()" x-init="init()">
    <!-- Loading State -->
    <div x-show="loading" class="d-flex justify-content-center align-items-center" style="height: 400px;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error" class="alert alert-danger" role="alert" x-text="error"></div>

    <!-- Dashboard Content -->
    <div x-show="!loading && !error">
        <h1 class="h2 mb-4">Student Dashboard</h1>

        <!-- Student Profile Card -->
        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap align-items-center">
                <img src="https://via.placeholder.com/100" alt="Student" 
                     class="rounded-circle me-4 mb-3 mb-md-0" 
                     style="width: 100px; height: 100px; object-fit: cover;">

                <div class="flex-grow-1">
                    <h5 class="mb-1" x-text="studentData.name || '{{ Auth::user()->name }}'"></h5>
                    <p class="mb-1 text-muted">
                        Admission No: <strong x-text="studentData.admission_no || 'STU-001'"></strong>
                    </p>
                    <p class="mb-1 text-muted">
                        Class: <strong x-text="studentData.class || 'Class 1'"></strong> | 
                        Section: <strong x-text="studentData.section || 'A'"></strong>
                    </p>
                    <p class="mb-0 text-muted">
                        Email: <span x-text="studentData.email || '{{ Auth::user()->email }}'"></span> | 
                        Phone: <span x-text="studentData.phone || 'N/A'"></span>
                    </p>
                </div>

                <div class="mt-3 mt-md-0">
                    <span class="badge bg-success px-3 py-2">Active Student</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-calendar-check fs-2 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Attendance</h6>
                                <h4 class="mb-0" x-text="stats.attendance + '%'">85%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-cash-stack fs-2 text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Fees Paid</h6>
                                <h4 class="mb-0" x-text="'₹' + stats.feesPaid.toLocaleString()">₹15,000</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Pending Fees</h6>
                                <h4 class="mb-0" x-text="'₹' + stats.pendingFees.toLocaleString()">₹5,000</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-book fs-2 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Books Issued</h6>
                                <h4 class="mb-0" x-text="stats.booksIssued">3</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-week fs-1 text-primary mb-3"></i>
                        <h5>View Timetable</h5>
                        <p class="text-muted">Check your class schedule</p>
                        <a href="{{ route('student.timetable') }}" class="btn btn-primary">View Timetable</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack fs-1 text-success mb-3"></i>
                        <h5>Pay Fees</h5>
                        <p class="text-muted">Pay pending fees online</p>
                        <a href="{{ route('student.fees') }}" class="btn btn-success">Pay Now</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-info mb-3"></i>
                        <h5>Download Documents</h5>
                        <p class="text-muted">Access your certificates</p>
                        <a href="{{ route('student.documents') }}" class="btn btn-info">Download</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <template x-for="activity in recentActivities" :key="activity.id">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i :class="activity.icon" class="fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0" x-text="activity.title"></h6>
                            <small class="text-muted" x-text="activity.description"></small>
                        </div>
                        <small class="text-muted" x-text="activity.date"></small>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function studentDashboard() {
    return {
        loading: true,
        error: null,
        studentData: {},
        stats: {
            attendance: 85,
            feesPaid: 15000,
            pendingFees: 5000,
            booksIssued: 3
        },
        recentActivities: [
            {
                id: 1,
                icon: 'bi bi-cash-stack text-success',
                title: 'Fee Payment Received',
                description: 'Monthly fee payment of ₹2,500 received',
                date: '2 days ago'
            },
            {
                id: 2,
                icon: 'bi bi-book text-primary',
                title: 'Book Issued',
                description: 'Mathematics Textbook issued from library',
                date: '1 week ago'
            },
            {
                id: 3,
                icon: 'bi bi-calendar-check text-info',
                title: 'Attendance Marked',
                description: 'Present in all classes today',
                date: '1 day ago'
            }
        ],

        init() {
            this.fetchStudentData();
        },

        async fetchStudentData() {
            try {
                // In a real implementation, this would fetch from Laravel controller
                // For now, using mock data
                setTimeout(() => {
                    this.studentData = {
                        name: '{{ Auth::user()->name }}',
                        admission_no: 'STU-{{ Auth::id() }}',
                        class: 'Class 10',
                        section: 'A',
                        email: '{{ Auth::user()->email }}',
                        phone: '9876543210'
                    };
                    this.loading = false;
                }, 1000);
            } catch (error) {
                this.error = 'Failed to load student data';
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection