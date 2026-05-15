@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Company Staff</span>
    </h4>

    <div class="card">

        {{-- HEADER --}}
        <div class="card-header flex-column flex-md-row">

            <div class="head-label">
                <h5 class="card-title mb-0">Company Staff Listing</h5>
            </div>

            <div class="dt-action-buttons text-end pt-3 pt-md-0">

                <a href="{{ route('company-staff.create') }}"
                   class="btn btn-primary"
                   onclick="showLoading()">

                    <i class="bx bx-plus me-1"></i>
                    Add Staff

                </a>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="card-datatable">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Phone</th>
                            <th>Register Date</th>
                            <th>Company</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($staffs as $staff)

                            <tr>

                                <td>{{ $staff->staff_name }}</td>

                                <td>{{ $staff->phone_number ?? '-' }}</td>

                                <td>{{ $staff->registered_date ?? '-' }}</td>

                                <td>
                                    {{ $staff->company->company_name ?? '-' }}
                                </td>

                                <td>

                                    <a href="{{ route('company-staff.edit', $staff) }}"
                                       class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('company-staff.destroy', $staff) }}"
                                          method="POST"
                                          style="display:inline-block">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this staff?')">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center">
                                    No staff found
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3 px-3 pb-3">
                {{ $staffs->links() }}
            </div>

        </div>

    </div>

</div>

@endsection