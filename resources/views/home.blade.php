@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Dashboard</span>
    </h4>

    <div class="row">

        {{-- TOTAL CASES --}}
        <div class="col-xl-3 col-sm-6 col-12 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div class="d-flex align-items-center gap-3">

                            <div class="avatar">

                                <span class="avatar-initial bg-label-primary rounded-circle">
                                    <i class="bx bx-briefcase fs-4"></i>
                                </span>

                            </div>

                            <div class="card-info">

                                <small class="text-muted">
                                    Total Cases
                                </small>

                                <h5 class="mb-0">
                                    {{ $totalCases }}
                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- PENDING --}}
        <div class="col-xl-3 col-sm-6 col-12 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div class="d-flex align-items-center gap-3">

                            <div class="avatar">

                                <span class="avatar-initial bg-label-warning rounded-circle">
                                    <i class="bx bx-time fs-4"></i>
                                </span>

                            </div>

                            <div class="card-info">

                                <small class="text-muted">
                                    Pending
                                </small>

                                <h5 class="mb-0">
                                    {{ $pendingCases }}
                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- IN PROGRESS --}}
        <div class="col-xl-3 col-sm-6 col-12 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div class="d-flex align-items-center gap-3">

                            <div class="avatar">

                                <span class="avatar-initial bg-label-info rounded-circle">
                                    <i class="bx bx-loader-circle fs-4"></i>
                                </span>

                            </div>

                            <div class="card-info">

                                <small class="text-muted">
                                    In Progress
                                </small>

                                <h5 class="mb-0">
                                    {{ $inProgressCases }}
                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- COMPLETED --}}
        <div class="col-xl-3 col-sm-6 col-12 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div class="d-flex align-items-center gap-3">

                            <div class="avatar">

                                <span class="avatar-initial bg-label-success rounded-circle">
                                    <i class="bx bx-check-circle fs-4"></i>
                                </span>

                            </div>

                            <div class="card-info">

                                <small class="text-muted">
                                    Completed
                                </small>

                                <h5 class="mb-0">
                                    {{ $completedCases }}
                                </h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- SECOND ROW --}}
    <div class="row">

        {{-- REVENUE --}}
        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total Revenue
                            </small>

                            <h3 class="mb-1 mt-2">
                                RM {{ number_format($totalRevenue, 2) }}
                            </h3>

                        </div>

                        <div class="avatar">

                            <span class="avatar-initial bg-label-success rounded-circle">
                                <i class="bx bx-dollar-circle fs-4"></i>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- PAID --}}
        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Paid Cases
                            </small>

                            <h3 class="mb-1 mt-2">
                                {{ $paidCases }}
                            </h3>

                        </div>

                        <div class="avatar">

                            <span class="avatar-initial bg-label-primary rounded-circle">
                                <i class="bx bx-wallet fs-4"></i>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- UNPAID --}}
        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Unpaid Cases
                            </small>

                            <h3 class="mb-1 mt-2">
                                {{ $unpaidCases }}
                            </h3>

                        </div>

                        <div class="avatar">

                            <span class="avatar-initial bg-label-danger rounded-circle">
                                <i class="bx bx-error-circle fs-4"></i>
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- RECENT CASES --}}
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Recent Service Cases
            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Payment</th>
                        <th>Submitted</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($recentCases as $case)

                        <tr>

                            <td>
                                #{{ $case->id }}
                            </td>

                            <td>
                                {{ $case->service->name ?? '-' }}
                            </td>

                            <td>

                                @php
                                    $statusColor = match($case->status) {
                                        'pending' => 'warning',
                                        'inprogress' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp

                                <span class="badge bg-label-{{ $statusColor }}">
                                    {{ ucfirst($case->status) }}
                                </span>

                            </td>

                            <td>
                                RM {{ number_format($case->price ?? 0, 2) }}
                            </td>

                            <td>

                                @if($case->is_paid)

                                    <span class="badge bg-label-success">
                                        Paid
                                    </span>

                                @else

                                    <span class="badge bg-label-danger">
                                        Unpaid
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $case->submit_datetime?->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">
                                No service cases found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@section('page-js')
@endsection

@section('scripts')
@endsection