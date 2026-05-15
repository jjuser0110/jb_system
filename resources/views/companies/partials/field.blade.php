<div class="row">

    {{-- COMPANY NAME --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Company Name
        </label>

        <input
            type="text"
            name="company_name"
            class="form-control @error('company_name') is-invalid @enderror"
            value="{{ old('company_name', $company->company_name ?? '') }}"
        >

        @error('company_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- REGISTER DATE --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Register Date
        </label>

        <input
            type="date"
            name="register_date"
            class="form-control @error('register_date') is-invalid @enderror"
            value="{{ old('register_date', $company->register_date ?? '') }}"
        >

        @error('register_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- CONTACT --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Contact No
        </label>

        <input
            type="text"
            name="contact_no"
            class="form-control @error('contact_no') is-invalid @enderror"
            value="{{ old('contact_no', $company->contact_no ?? '') }}"
        >

        @error('contact_no')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>