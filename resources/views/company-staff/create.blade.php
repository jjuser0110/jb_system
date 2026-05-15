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

                    {{-- USERNAME --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Username</label>

                        <input type="text"
                            name="username"
                            class="form-control"
                            value="{{ old('username', $companyStaff->user->username ?? '') }}">

                    </div>

                    {{-- NAME --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Name</label>

                        <input type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $companyStaff->user->name ?? '') }}">

                    </div>

                    {{-- EMAIL --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Email</label>

                        <input type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $companyStaff->user->email ?? '') }}">

                    </div>

                    {{-- PASSWORD --}}
                    @if(!isset($companyStaff))
                    <div class="col-md-6 mb-3">

                        <label class="form-label">Password</label>

                        <input type="password"
                            name="password"
                            class="form-control">

                    </div>
                    @endif

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