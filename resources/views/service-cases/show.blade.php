@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-4">
        <h4>
            Service Case Details
        </h4>

        <a href="{{ route('service-cases.index') }}"
            class="btn btn-secondary">

            Back

        </a>
    </div>

    <div class="row">

        {{-- CASE INFO --}}
        <div class="col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">Case Information</h5>
                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>
                            <th width="180">Order Number</th>
                            <td>{{ $serviceCase->order_number ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Company</th>
                            <td>{{ $serviceCase->company->company_name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Staff</th>
                            <td>{{ $serviceCase->user->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $serviceCase->status)) }}</td>
                        </tr>

                        <tr>
                            <th>Duration</th>
                            <td>{{ $serviceCase->duration ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Price</th>
                            <td>RM {{ number_format($serviceCase->price ?? 0, 2) }}</td>
                        </tr>

                        <tr>
                            <th>Payment</th>
                            <td>
                                @if($serviceCase->is_paid)
                                    <span class="badge bg-success">PAID</span>
                                @else
                                    <span class="badge bg-danger">UNPAID</span>
                                @endif
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- DATE INFO --}}
        <div class="col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">Timeline</h5>
                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>
                            <th width="180">Submitted</th>
                            <td>
                                {{ optional($serviceCase->submit_datetime)->format('d/m/Y h:i A') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Accepted</th>
                            <td>
                                {{ $serviceCase->accepted_at ? \Carbon\Carbon::parse($serviceCase->accepted_at)->format('d/m/Y h:i A') : '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Completed</th>
                            <td>
                                {{ $serviceCase->completed_at ? \Carbon\Carbon::parse($serviceCase->completed_at)->format('d/m/Y h:i A') : '-' }}
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- DESCRIPTION --}}
        <div class="col-md-12 mb-4">

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">Description</h5>
                </div>

                <div class="card-body">

                    {!! nl2br(e($serviceCase->description ?? '-')) !!}

                </div>

            </div>

        </div>

        {{-- REMARK --}}
        <div class="col-md-12 mb-4">

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">Remark</h5>
                </div>

                <div class="card-body">

                    {!! nl2br(e($serviceCase->remark ?? '-')) !!}

                </div>

            </div>

        </div>

        {{-- PHOTOS --}}
        @if($serviceCase->getMedia('photos')->count())

            <div class="col-md-12">

                <div class="card">

                    <div class="card-header">
                        <h5 class="mb-0">Photo Gallery</h5>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            @foreach($serviceCase->getMedia('photos') as $photo)

                                <div class="col-md-3 mb-3">

                                    <a href="{{ $photo->getUrl() }}"
                                        target="_blank">

                                        <img src="{{ $photo->getUrl() }}"
                                            class="img-fluid rounded border"
                                            alt="Service Case Photo">

                                    </a>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection