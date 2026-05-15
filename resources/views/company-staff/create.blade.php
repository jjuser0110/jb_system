@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('company-staff.index') }}">
            Company Staff /
        </a>

        @if(isset($companyStaff))
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
                @if(isset($companyStaff))
                    action="{{ route('company-staff.update', $companyStaff) }}"
                @else
                    action="{{ route('company-staff.store') }}"
                @endif
            >

                @csrf

                @if(isset($companyStaff))
                    @method('PUT')
                @endif

                <div class="row">

                    {{-- COMPANY --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Company</label>

                        <select name="company_id" class="form-control">

                            <option value="">Select Company</option>

                            @foreach($companies as $company)

                                <option value="{{ $company->id }}"
                                    {{ old('company_id', $companyStaff->company_id ?? '') == $company->id ? 'selected' : '' }}>

                                    {{ $company->company_name }}

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
                               value="{{ old('staff_name', $companyStaff->staff_name ?? '') }}">

                    </div>

                    {{-- PHONE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Phone Number</label>

                        <input type="text"
                               name="phone_number"
                               class="form-control"
                               value="{{ old('phone_number', $companyStaff->phone_number ?? '') }}">

                    </div>

                    {{-- REGISTER DATE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Register Date</label>

                        <input type="date"
                               name="registered_date"
                               class="form-control"
                               value="{{ old('registered_date', $companyStaff->registered_date ?? '') }}">

                    </div>

                </div>

                <hr>

                <button class="btn btn-primary">

                    @if(isset($companyStaff))
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