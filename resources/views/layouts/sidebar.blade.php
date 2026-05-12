@php
$currentRoute = request()->route()->getName();

$user = Auth::user();

$canAccessSystem =
    $user &&
    (
        $user->isAn('superadmin') ||
        $user->isAn('admin') ||
        $user->isAn('customer_staff')
    );

$canManageUser =
    $user &&
    (
        $user->isAn('superadmin') ||
        $user->isAn('admin')
    );
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
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
        @if($canAccessSystem)
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endif



        {{-- ================= CUSTOMER ================= --}}
        @if($canAccessSystem)

        <li class="menu-item {{ Str::contains($currentRoute, 'customer') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Customer Module</div>
            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ Str::contains($currentRoute, 'customers') ? 'active' : '' }}">
                    <a href="{{ route('customers.index') }}" class="menu-link">
                        <div>Customers</div>
                    </a>
                </li>

                <li class="menu-item {{ Str::contains($currentRoute, 'customer-staff') ? 'active' : '' }}">
                    <a href="{{ route('customer-staff.index') }}" class="menu-link">
                        <div>Customer Staff</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif


        {{-- ================= ADMIN ONLY ================= --}}
        @if($canManageUser)

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Settings</span>
        </li>

        @php
            $userRoutes3 = ['admin'];

            $isUserActive3 = collect($userRoutes3)
                ->contains(fn($r) => Str::contains($currentRoute, $r));
        @endphp

        <li class="menu-item {{ $isUserActive3 ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Users</div>
            </a>

            <ul class="menu-sub">

                @foreach ($userRoutes3 as $role3)

                <li class="menu-item {{ Str::contains($currentRoute, $role3) ? 'active' : '' }}">
                    <a href="{{ route($role3 . '.index') }}" class="menu-link">
                        <div>{{ ucfirst($role3) }}</div>
                    </a>
                </li>

                @endforeach

            </ul>
        </li>

        @endif

    </ul>
</aside>