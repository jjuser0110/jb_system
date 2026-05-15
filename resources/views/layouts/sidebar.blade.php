@php
$currentRoute = request()->route()->getName();
$user = Auth::user();

/**
 * ROLES
 */
$isAdmin = $user && (
    $user->isAn('admin') ||
    $user->isAn('superadmin')
);

$isOwner = $user && $user->isAn('owner');

$isStaff = $user && $user->isAn('company_staff');

/**
 * DASHBOARD → ALL USERS
 */
$canAccessDashboard = $user && ($isAdmin || $isOwner || $isStaff);

/**
 * COMPANY MODULE → ADMIN + OWNER ONLY
 */
$canAccessCompanyModule = $user && ($isAdmin || $isOwner);

/**
 * CASE MODULE → ALL USERS
 */
$canAccessCaseModule = $user && ($isAdmin || $isOwner || $isStaff);

/**
 * USER MANAGEMENT → ADMIN ONLY
 */
$canManageUsers = $isAdmin;

/**
 * USER MODULE LIST
 */
$userModules = [
    'admin' => 'Admin',
    'owner' => 'Owner',
];
@endphp


<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- ================= BRAND ================= --}}
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('Shopowner.png') }}" alt="Logo" style="width:100%;" />
            </span>

            <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size:20px;">
                JB System
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- ================= DASHBOARD ================= --}}
        @if($canAccessDashboard)
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endif


        {{-- ================= COMPANY MODULE ================= --}}
        @if($canAccessCompanyModule)
        <li class="menu-item {{ Str::contains($currentRoute, 'company') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Company Module</div>
            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ Str::contains($currentRoute, 'companies') ? 'active' : '' }}">
                    <a href="{{ route('companies.index') }}" class="menu-link">
                        <div>Companies</div>
                    </a>
                </li>

                <li class="menu-item {{ Str::contains($currentRoute, 'company-staff') ? 'active' : '' }}">
                    <a href="{{ route('company-staff.index') }}" class="menu-link">
                        <div>Company Staff</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif


        {{-- ================= CASE MODULE (ALL ROLES) ================= --}}
        @if($canAccessCaseModule)
        <li class="menu-item {{ Str::contains($currentRoute, 'service-cases') || Str::contains($currentRoute, 'service.') ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-task"></i>
                <div>Case Module</div>
            </a>

            <ul class="menu-sub">

                {{-- CASE --}}
                <li class="menu-item {{ Str::contains($currentRoute, 'service-cases.') ? 'active' : '' }}">
                    <a href="{{ route('service-cases.index') }}" class="menu-link">
                        <div>Case</div>
                    </a>
                </li>

                {{-- SERVICE --}}
                <li class="menu-item {{ Str::contains($currentRoute, 'service.') ? 'active' : '' }}">
                    <a href="{{ route('services.index') }}" class="menu-link">
                        <div>Service</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif


        {{-- ================= USER MANAGEMENT (ADMIN ONLY) ================= --}}
        @if($canManageUsers)

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">User Management</span>
        </li>

        @php
            $isUserMenuActive = collect(array_keys($userModules))
                ->contains(fn($module) => Str::contains($currentRoute, $module));
        @endphp

        <li class="menu-item {{ $isUserMenuActive ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Users</div>
            </a>

            <ul class="menu-sub">
                @foreach($userModules as $route => $label)
                    <li class="menu-item {{ Str::contains($currentRoute, $route) ? 'active' : '' }}">
                        <a href="{{ route($route . '.index') }}" class="menu-link">
                            <div>{{ $label }}</div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        @endif

    </ul>
</aside>