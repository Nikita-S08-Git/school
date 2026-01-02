@php
    $user = Auth::user();
    $role = $user->roles->first()->name ?? 'student';
    
    $menuByRole = [
        'principal' => [
            ['name' => 'Dashboard', 'route' => 'principal.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Students', 'route' => 'principal.students', 'icon' => 'people-fill'],
            ['name' => 'Fee Management', 'route' => 'principal.fees', 'icon' => 'cash-stack'],
            ['name' => 'Staff', 'route' => 'principal.staff', 'icon' => 'person-badge'],
            ['name' => 'Reports', 'route' => 'principal.reports', 'icon' => 'graph-up'],
        ],
        'teacher' => [
            ['name' => 'Dashboard', 'route' => 'teacher.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Students', 'route' => 'teacher.students', 'icon' => 'people-fill'],
            ['name' => 'Attendance', 'route' => 'teacher.attendance', 'icon' => 'check-square'],
            ['name' => 'Assignments', 'route' => 'teacher.assignments', 'icon' => 'clipboard-check'],
        ],
        'student' => [
            ['name' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Profile', 'route' => 'student.profile', 'icon' => 'person'],
            ['name' => 'Fees', 'route' => 'student.fees', 'icon' => 'cash-stack'],
            ['name' => 'Attendance', 'route' => 'student.attendance', 'icon' => 'calendar-check'],
            ['name' => 'Library', 'route' => 'student.library', 'icon' => 'book'],
        ],
        'accounts_staff' => [
            ['name' => 'Dashboard', 'route' => 'accountant.dashboard', 'icon' => 'speedometer2'],
            ['name' => 'Fee Collection', 'route' => 'accountant.fees', 'icon' => 'cash-stack'],
            ['name' => 'Expenses', 'route' => 'accountant.expenses', 'icon' => 'receipt'],
            ['name' => 'Reports', 'route' => 'accountant.reports', 'icon' => 'graph-up'],
        ],
    ];
    
    $menuItems = $menuByRole[$role] ?? [];
@endphp

<!-- Desktop Sidebar -->
<div class="sidebar d-none d-md-flex flex-column text-white p-3">
    <div class="mb-4">
        <h5 class="d-flex align-items-center text-white">
            <i class="bi bi-mortarboard me-2"></i>
            School ERP
        </h5>
        <p class="small mb-0 text-white opacity-75">{{ ucfirst($role) }} Portal</p>
    </div>

    <hr class="my-3 border-white opacity-50">

    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white mb-3 text-decoration-none p-2 rounded {{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach

    <div class="mt-auto pt-4">
        <hr class="my-3 border-white opacity-50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-2"></i>
                Logout
            </button>
        </form>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform -translate-x-full"
     class="mobile-sidebar position-fixed top-0 start-0 text-white p-3"
     style="width: 250px; height: 100vh; z-index: 1050;">
    
    <button class="btn btn-light btn-sm mb-3" @click="sidebarOpen = false">
        <i class="bi bi-x me-1"></i> Close
    </button>

    @foreach($menuItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="d-flex align-items-center text-white d-block mb-3 p-2 rounded {{ request()->routeIs($item['route']) ? 'bg-white bg-opacity-20' : '' }}"
           @click="sidebarOpen = false">
            <i class="bi bi-{{ $item['icon'] }} me-2"></i>
            {{ $item['name'] }}
        </a>
    @endforeach
</div>

<!-- Mobile Overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="position-fixed inset-0 bg-black bg-opacity-50 d-md-none"
     style="z-index: 1040;"
     @click="sidebarOpen = false"></div>