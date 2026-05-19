@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">
            Service Case
        </span>
    </h4>

    <div class="card">

        {{-- HEADER --}}
        <div class="card-header flex-column flex-md-row">

            <div class="head-label">
                <h5 class="card-title mb-0">
                    Service Case Listing
                </h5>
            </div>

            {{-- ADD BUTTON --}}
            <div class="dt-action-buttons text-end pt-3 pt-md-0">

                <div class="dt-buttons">

                    <a class="dt-button create-new btn btn-primary"
                        href="{{ route('service-cases.create') }}"
                        onclick="showLoading()">

                        <span>
                            <i class="bx bx-plus me-sm-1"></i>

                            <span class="d-none d-sm-inline-block">
                                Add New Record
                            </span>
                        </span>

                    </a>

                </div>

            </div>

        </div>
        {{-- FILTER --}}
<div class="card-body border-bottom">

    <form method="GET">

        <div class="row g-3 align-items-end">

            {{-- DATE FROM --}}
            <div class="col-md-2">
                <label class="form-label">
                    Date From
                </label>

                <input type="date"
                    name="date_from"
                    class="form-control"
                    value="{{ $dateFrom }}">
            </div>

            {{-- DATE TO --}}
            <div class="col-md-2">
                <label class="form-label">
                    Date To
                </label>

                <input type="date"
                    name="date_to"
                    class="form-control"
                    value="{{ $dateTo }}">
            </div>
            {{-- COMPANY --}}
            <div class="col-md-3">

                <label class="form-label">
                    Company
                </label>

                <select name="company_id"
                    class="form-select">

                    <option value="">
                        All Company
                    </option>

                    @foreach($companies as $company)

                        <option value="{{ $company->id }}"
                            {{ request('company_id') == $company->id ? 'selected' : '' }}>

                            {{ $company->company_name }}

                        </option>

                    @endforeach

                </select>

            </div>
            {{-- SEARCH BUTTON --}}
            <div class="col-md-3">

                <button class="btn btn-primary">
                    Filter
                </button>

                <a href="{{ route('service-cases.index') }}"
                    class="btn btn-secondary">
                    Reset
                </a>

            </div>
            <div class="col-md-2">
                <a href="{{ route('service-cases.export', request()->all()) }}"
                    class="btn btn-success">
                    Export Excel
                </a>
            </div>
        </div>

        {{-- STATUS BUTTONS --}}
        <div class="mt-4 d-flex flex-wrap gap-2">

                    <a href="{{ route('service-cases.index') }}"
                        class="btn btn-dark">
                        All
                    </a>

                    <a href="{{ route('service-cases.index', [
                        'status' => 'pending',
                        'date_from' => request('date_from'),
                        'date_to' => request('date_to'),
                    ]) }}"
                        class="btn btn-warning">

                        Pending
                    </a>

                    <a href="{{ route('service-cases.index', [
                        'status' => 'in_progress',
                        'date_from' => request('date_from'),
                        'date_to' => request('date_to'),
                    ]) }}"
                        class="btn btn-info">

                        In Progress
                    </a>

                    <a href="{{ route('service-cases.index', [
                        'status' => 'cancel',
                        'date_from' => request('date_from'),
                        'date_to' => request('date_to'),
                    ]) }}"
                        class="btn btn-danger">

                        Cancel
                    </a>

                    <a href="{{ route('service-cases.index', [
                        'status' => 'complete',
                        'date_from' => request('date_from'),
                        'date_to' => request('date_to'),
                    ]) }}"
                        class="btn btn-success">

                        Complete
                    </a>

                </div>

            </form>

        </div>
        {{-- TABLE --}}
        <div class="card-datatable text-nowrap table-responsive">

            <table class="dt-column-search table table-bordered"
                id="mytable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Company</th>
                        <th>Company Staff</th>
                        <th>Service</th>
                        <th>Photo</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Submitted</th>
                        <th>Completed</th>
                        <th>Price</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceCases as $index => $row)
                        @php
                            $days = now()->diffInDays($row->submit_datetime);
                            if($days <= 2){
                                $durationColor = 'success';
                            }elseif($days <= 4){
                                $durationColor = 'warning';
                            }else{
                                $durationColor = 'danger';
                            }
                        @endphp
                        <tr>
                            {{-- NO --}}
                            <td>
                                {{ $index + 1 }}
                            </td>
                            {{-- COMPANY --}}
                            <td>
                                {{ $row->companyStaff->company->company_name ?? '-' }}
                            </td>
                            {{-- COMPANY STAFF --}}
                            <td>
                                {{ $row->companyStaff->user->name ?? '-' }}
                            </td>
                            {{-- SERVICE --}}
                            <td>
                                {{ $row->service->name ?? '-' }}
                            </td>
                            {{-- PHOTO --}}
                            <td>
                                @if($row->photo)
                                    <img src="{{ asset('storage/' . $row->photo) }}"
                                        width="80"
                                        class="img-thumbnail">
                                @else
                                    -
                                @endif
                            </td> 
                            {{-- STATUS --}}
                            <td>
                                @if($row->status == 'pending')
                                    <span class="badge bg-warning">
                                        Pending
                                    </span>
                                @elseif($row->status == 'complete')
                                    <span class="badge bg-success">
                                        Complete
                                    </span>
                                @elseif($row->status == 'reject')
                                    <span class="badge bg-danger">
                                        Reject
                                    </span>
                                @elseif($row->status == 'cancel')
                                    <span class="badge bg-secondary">
                                        Cancel
                                    </span>
                                @endif
                            </td>
                            {{-- DURATION --}}
                            <td>
                                <span class="badge bg-{{ $durationColor }}">
                                    {{ $days }} Day(s)
                                </span>
                            </td>
                            {{-- SUBMIT DATETIME --}}
                            <td>
                                {{ \Carbon\Carbon::parse($row->submit_datetime)->format('d/m/Y h:i A') }}
                            </td>
                            {{-- COMPLETE DATETIME --}}
                            <td>
                                @if($row->completed_at)
                                    {{ \Carbon\Carbon::parse($row->completed_at)->format('d/m/Y h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                RM {{ number_format($row->price, 2) }}
                            </td>
                            <td>
                                <a href="{{ route('service-cases.toggle-payment', $row) }}">
                                    @if($row->is_paid)
                                        <span class="badge bg-success">
                                            PAID
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            UNPAID
                                        </span>
                                    @endif
                                </a>
                            </td>
                            {{-- ACTION --}}
                            <td>

                                {{-- EDIT --}}
                                <a href="{{ route('service-cases.edit', $row) }}"
                                    onclick="showLoading()"
                                    class="me-2">

                                    <i class="fa-solid fa-pen-to-square"></i>

                                </a>

                                {{-- DELETE --}}
                                <a style="color:red;cursor:pointer"
                                    onclick="if(confirm('Are you sure you want to delete?')){showLoading();window.location.href='{{ route('service-cases.destroy',$row) }}'}">

                                    <i class="fa-solid fa-trash"></i>

                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@section('page-js')
@endsection

@section('scripts')

<script>

$(function () {

    $('#mytable').DataTable({
        responsive: false,
        scrollX: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
    });

});

</script>

@endsection