<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School ERP System')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #495057;
        }
        .main-content {
            min-height: 100vh;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">School ERP</h5>
                    </div>
                    
                    <ul class="nav flex-column">
                        @php
                            $role = auth()->check() ? (auth()->user()->roles->first()->name ?? 'student') : 'student';
                        @endphp
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(in_array($role, ['principal', 'office']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard.students') ? 'active' : '' }}" href="{{ route('dashboard.students') }}">
                                <i class="bi bi-people"></i> Students
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">
                                <i class="bi bi-person-plus"></i> Admissions
                            </a>
                        </li>
                        @endif
                        
                        @if($role === 'student')
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-check"></i> Attendance
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-credit-card"></i> Fees
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item mt-auto">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                    
                    <!-- User Profile Section -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle d-flex align-items-center px-3 py-2" type="button" data-bs-toggle="dropdown" style="border-radius: 25px;">
                            <i class="bi bi-person-circle fs-5 me-2"></i>
                            <div class="text-start">
                                <div class="fw-semibold text-white">{{ auth()->user()->name ?? 'User' }}</div>
                                <small class="text-light opacity-75">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Role') }}</small>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 200px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle fs-3 me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                                        <small class="text-muted">{{ auth()->user()->email ?? 'user@example.com' }}</small>
                                    </div>
                                </div>
                            </li>
                            <li><a class="dropdown-item py-2" href="#" onclick="editProfile()"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProfile() {
            // You can redirect to profile edit page or open a modal
            alert('Edit Profile functionality - to be implemented');
            // window.location.href = '/profile/edit';
        }
    </script>
    @stack('scripts')
</body>
</html>