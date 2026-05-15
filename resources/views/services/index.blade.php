@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">
            Services
        </span>
    </h4>

    <div class="card">

        {{-- HEADER --}}
        <div class="card-header flex-column flex-md-row">

            <div class="head-label">
                <h5 class="card-title mb-0">
                    Service Listing
                </h5>
            </div>

            {{-- ADD BUTTON --}}
            <div class="dt-action-buttons text-end pt-3 pt-md-0">

                <div class="dt-buttons">

                    <a class="dt-button create-new btn btn-primary"
                        href="{{ route('services.create') }}">

                        <span>
                            <i class="bx bx-plus me-sm-1"></i>

                            <span class="d-none d-sm-inline-block">
                                Add New Service
                            </span>
                        </span>

                    </a>

                </div>

            </div>

        </div>

        {{-- TABLE --}}
        <div class="card-datatable text-nowrap">

            <table class="table table-bordered" id="mytable">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Service Name</th>

                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($services as $index => $service)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $service->name }}
                            </td>

                            <td>

                                {{-- EDIT --}}
                                <a href="{{ route('services.edit', $service) }}"
                                    class="me-2">

                                    <i class="fa-solid fa-pen-to-square"></i>

                                </a>

                                {{-- DELETE --}}
                                <a style="color:red;cursor:pointer"
                                    onclick="if(confirm('Delete this service?')){window.location.href='{{ route('services.destroy', $service) }}'}">

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

@section('scripts')

<script>

$(function () {

    $('#mytable').DataTable({
        responsive: true,
        pageLength: 10,
    });

});

</script>

@endsection