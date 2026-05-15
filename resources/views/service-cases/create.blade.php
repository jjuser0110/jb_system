@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="card">

    <h5 class="card-header">
        {{ isset($serviceCase) ? 'Edit Case' : 'Create Case' }}
    </h5>

    <div class="card-body">

        <form method="POST"
            enctype="multipart/form-data"
            action="{{ isset($serviceCase)
                ? route('service-cases.update', $serviceCase->id)
                : route('service-cases.store') }}">

            @csrf

            @if(isset($serviceCase))
                @method('PUT')
            @endif

            <div class="row">

                {{-- COMPANY --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company</label>

                    <div class="form-control bg-light">
                        {{ $companies->first()->company_name ?? '' }}
                    </div>
                </div>

                {{-- STAFF (AUTO LOGIN) --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Staff</label>

                    @php
                        $user = auth()->user();
                    @endphp

                    <input type="hidden"
                        name="company_staff_id"
                        value="{{ $serviceCase->company_staff_id ?? ($user->companyStaff->id ?? '') }}">

                    <div class="form-control bg-light">
                        {{ $user->name }}
                    </div>
                </div>

                {{-- DATETIME --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Submit Datetime</label>

                    <input type="datetime-local"
                        name="submit_datetime"
                        class="form-control"
                        value="{{ isset($serviceCase)
                            ? \Carbon\Carbon::parse($serviceCase->submit_datetime)->format('Y-m-d\TH:i')
                            : '' }}">
                </div>

                {{-- SERVICE --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service</label>

                    <select name="service_id" class="form-control mb-2">
                        <option value="">Select Existing Service</option>

                        @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                {{ isset($serviceCase) && $serviceCase->service_id == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="text-center my-2">
                        <small>OR</small>
                    </div>

                    <input type="text"
                        name="new_service_name"
                        class="form-control"
                        placeholder="Type new service if not listed">
                </div>

                {{-- PHOTO --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Upload Photo</label>

                    <input type="file"
                        name="photo"
                        accept="image/*"
                        capture="environment"
                        class="form-control">

                    @if(isset($serviceCase) && $serviceCase->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $serviceCase->photo) }}"
                                style="width:120px; border-radius:8px;">
                        </div>
                    @endif
                </div>

            </div>

            <button class="btn btn-primary">
                {{ isset($serviceCase) ? 'Update Case' : 'Save Case' }}
            </button>

        </form>

    </div>

</div>

</div>

@endsection