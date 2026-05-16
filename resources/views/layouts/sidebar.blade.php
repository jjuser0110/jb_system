@php
$currentRoute = request()->route()->getName();
$user = Auth::user();

$canAccessDashboard = $user?->can('view-dashboard');

$canAccessCompanyModule = $user?->can('manage-company');

$canAccessCaseModule = $user?->can('manage-case');

$canManageUsers = $user?->can('manage-users');

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
        @can('view-dashboard')
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endcan


        {{-- ================= COMPANY MODULE ================= --}}
        @if($canAccessCompanyModule)

        <li class="menu-item {{ Str::contains($currentRoute, 'company') ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Company Module</div>
            </a>

            <ul class="menu-sub">

                @can('manage-company')
                <li class="menu-item {{ Str::contains($currentRoute, 'companies') ? 'active' : '' }}">
                    <a href="{{ route('companies.index') }}" class="menu-link">
                        <div>Companies</div>
                    </a>
                </li>
                @endcan

                @can('manage-company-staff')
                <li class="menu-item {{ Str::contains($currentRoute, 'company-staff') ? 'active' : '' }}">
                    <a href="{{ route('company-staff.index') }}" class="menu-link">
                        <div>Company Staff</div>
                    </a>
                </li>
                @endcan

            </ul>
        </li>
        @endif


        {{-- ================= CASE MODULE ================= --}}
        @if($canAccessCaseModule)

        <li class="menu-item {{ Str::contains($currentRoute, 'service-cases') || Str::contains($currentRoute, 'service.') ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-task"></i>
                <div>Case Module</div>
            </a>

            <ul class="menu-sub">

                @can('manage-case')
                <li class="menu-item {{ Str::contains($currentRoute, 'service-cases.') ? 'active' : '' }}">
                    <a href="{{ route('service-cases.index') }}" class="menu-link">
                        <div>Case</div>
                    </a>
                </li>
                @endcan
                @can('manage-case')
                <li class="menu-item {{ Str::contains($currentRoute, 'admin-manage-case.') ? 'active' : '' }}">
                    <a href="{{ route('admin.manage-case.index') }}" class="menu-link">
                        <div>Manage Case</div>
                    </a>
                </li>
                @endcan
                @can('manage-service')
                <li class="menu-item {{ Str::contains($currentRoute, 'service.') ? 'active' : '' }}">
                    <a href="{{ route('services.index') }}" class="menu-link">
                        <div>Service</div>
                    </a>
                </li>
                @endcan

            </ul>
        </li>
        @endif

        {{-- ================= USER MANAGEMENT ================= --}}
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