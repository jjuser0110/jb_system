@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">
            companies
        </span>
    </h4>

    <div class="card">

        <div class="card-header flex-column flex-md-row">

            <div class="head-label">
                <h5 class="card-title mb-0">
                    Company Listing
                </h5>
            </div>

            <div class="dt-action-buttons text-end pt-3 pt-md-0">

                <div class="dt-buttons">

                    <a
                        class="dt-button create-new btn btn-primary"
                        href="{{ route('companies.create') }}"
                    >
                        <span>
                            <i class="bx bx-plus me-sm-1"></i>

                            <span class="d-none d-sm-inline-block">
                                Add New Company
                            </span>
                        </span>
                    </a>

                </div>

            </div>

        </div>


        {{-- SEARCH + SHOW --}}
        <div class="d-flex justify-content-between align-items-center mb-3 px-3">

            {{-- SHOW ENTRIES --}}
            <form method="GET" class="d-flex align-items-center">

                <label class="me-2">
                    Show
                </label>

                <select
                    name="per_page"
                    class="form-select form-select-sm w-auto me-2"
                    onchange="this.form.submit()"
                >
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>
                        10
                    </option>

                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>
                        20
                    </option>

                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>
                        50
                    </option>

                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>
                        100
                    </option>

                </select>

                <span>
                    entries
                </span>

                <input
                    type="hidden"
                    name="search"
                    value="{{ request('search') }}"
                >

            </form>


            {{-- SEARCH --}}
            <form method="GET" class="d-flex align-items-center">

                <label class="me-2">
                    Search:
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control form-control-sm"
                    placeholder="Search company..."
                >

                <input
                    type="hidden"
                    name="per_page"
                    value="{{ request('per_page', 10) }}"
                >

            </form>

        </div>


        {{-- TABLE --}}
        <div class="card-datatable">

            <div class="table-responsive" style="max-height: 600px; overflow:auto;">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>
                                Company Name
                            </th>

                            <th>
                                Register Date
                            </th>

                            <th>
                                Contact No
                            </th>

                            <th>
                                Role ID
                            </th>

                            <th width="120">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($companies as $company)

                        <tr>

                            <td>
                                <a href="{{ route('companies.edit', $company) }}">
                                    {{ $company->company_name }}
                                </a>
                            </td>

                            <td>
                                {{ $company->register_date ?? '-' }}
                            </td>

                            <td>
                                {{ $company->contact_no ?? '-' }}
                            </td>

                            <td>
                                {{ $company->role->name ?? '-' }}
                            </td>

                            <td>

                                <div class="btn-group">

                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                    >
                                        Action
                                    </button>

                                    <ul class="dropdown-menu">

                                        <li>

                                            <a
                                                class="dropdown-item"
                                                href="{{ route('companies.edit', $company) }}"
                                            >
                                                Edit
                                            </a>

                                        </li>

                                        <li>

                                            <a
                                                class="dropdown-item text-danger"
                                                href="#"
                                                onclick="confirmDelete({{ $company->id }})"
                                            >
                                                Delete
                                            </a>

                                            <form
                                                id="delete-form-{{ $company->id }}"
                                                action="{{ route('companies.destroy', $company->id) }}"
                                                method="POST"
                                                style="display:none;"
                                            >

                                                @csrf
                                                @method('DELETE')

                                            </form>

                                        </li>

                                    </ul>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5" class="text-center">
                                No records found
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">

                <div>

                    Showing
                    {{ $companies->firstItem() ?? 0 }}
                    to
                    {{ $companies->lastItem() ?? 0 }}

                    of

                    {{ $companies->total() }}
                    entries

                </div>

                <div>
                    {{ $companies->withQueryString()->links() }}
                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')

<script>

function confirmDelete(id)
{
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'

    }).then((result) => {

        if (result.isConfirmed) {

            document.getElementById('delete-form-' + id).submit();

        }

    });
}

</script>

@endsection