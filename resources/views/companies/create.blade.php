@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('companies.index') }}">
            Companies /
        </a>

        @if (isset($company))
            {{ $company->company_name }}
        @else
            Create
        @endif
    </h4>

    <div class="card">

        <h5 class="card-header">
            Company Details
        </h5>

        <div class="card-body">

            <form
                @if (isset($company))
                    action="{{ route('companies.update', $company) }}"
                @else
                    action="{{ route('companies.store') }}"
                @endif
                method="POST"
            >

                @csrf

                @if (isset($company))
                    @method('PUT')
                @endif

                @include('companies.partials.field')

                <hr>

                <button type="submit" class="btn btn-primary">

                    @if (isset($company))
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