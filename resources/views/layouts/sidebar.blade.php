@php  
$currentRoute = request()->route()->getName();

$user = Auth::user();
$role = optional($user)->role_id;

$isAdmin = in_array($role, [1, 2]);
$isAgent = $role == 8;
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
        @if($isAdmin || $isAgent)
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : ''}}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Papan Pemuka</div>
            </a>
        </li>
        @endif


        {{-- ================= SENARAI HITAM ================= --}}
        @if($isAdmin || $isAgent)
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Penapisan</span>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'blacklist.search') ? 'active' : ''}}">
            <a href="{{ route('blacklist.search.form') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-search"></i>
                <div>Semak IC</div>
            </a>
        </li>

        @endif


        {{-- ================= ADMIN ONLY ================= --}}
        @if($isAdmin)

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tetapan</span>
        </li>

        @php
            $userRoutes3 = ['admin'];
            $isUserActive3 = collect($userRoutes3)
                ->contains(fn($r) => Str::contains($currentRoute, $r));
        @endphp

        <li class="menu-item {{ $isUserActive3 ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Pengguna</div>
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


        {{-- ================= PELANGGAN ================= --}}
        @if($isAdmin || $isAgent)
        <li class="menu-item {{ Str::contains($currentRoute, 'customer.index') ? 'active' : ''}}">
            <a href="{{ route('customer.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div>Senarai Hitam Pelanggan</div>
            </a>
        </li>
        @endif


        {{-- ================= SEBAB ================= --}}
        @if($isAdmin || $isAgent)
        <li class="menu-item {{ Str::contains($currentRoute, 'reasons.index') ? 'active' : ''}}">
            <a href="{{ route('reasons.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div>Sebab</div>
            </a>
        </li>
        @endif

    </ul>
</aside>