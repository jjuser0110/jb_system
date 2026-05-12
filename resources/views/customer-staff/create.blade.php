@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('customer-staff.index') }}">
            Customer Staff /
        </a>

        @if(isset($customerStaff))
            Edit
        @else
            Create
        @endif
    </h4>

    <div class="card">

        <h5 class="card-header">
            Staff Details
        </h5>

        <div class="card-body">

            <form method="POST"
                @if(isset($customerStaff))
                    action="{{ route('customer-staff.update', $customerStaff) }}"
                @else
                    action="{{ route('customer-staff.store') }}"
                @endif
            >

                @csrf

                @if(isset($customerStaff))
                    @method('PUT')
                @endif

                <div class="row">

                    {{-- CUSTOMER --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Customer</label>

                        <select name="customer_id" class="form-control">

                            <option value="">Select Customer</option>

                            @foreach($customers as $customer)

                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $customerStaff->customer_id ?? '') == $customer->id ? 'selected' : '' }}>

                                    {{ $customer->customer_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- STAFF NAME --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Staff Name</label>

                        <input type="text"
                               name="staff_name"
                               class="form-control"
                               value="{{ old('staff_name', $customerStaff->staff_name ?? '') }}">

                    </div>

                    {{-- PHONE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Phone Number</label>

                        <input type="text"
                               name="phone_number"
                               class="form-control"
                               value="{{ old('phone_number', $customerStaff->phone_number ?? '') }}">

                    </div>

                    {{-- REGISTER DATE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Register Date</label>

                        <input type="date"
                               name="registered_date"
                               class="form-control"
                               value="{{ old('registered_date', $customerStaff->registered_date ?? '') }}">

                    </div>

                </div>

                <hr>

                <button class="btn btn-primary">

                    @if(isset($customerStaff))
                        Update
                    @else
                        Save
                    @endif

                </button>

            </form>

        </div>

    </div>

</div>

@endsection