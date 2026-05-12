@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('customers.index') }}">
            Customers /
        </a>

        @if (isset($customer))
            {{ $customer->customer_name }}
        @else
            Create
        @endif
    </h4>

    <div class="card">

        <h5 class="card-header">
            Customer Details
        </h5>

        <div class="card-body">

            <form
                @if (isset($customer))
                    action="{{ route('customers.update', $customer) }}"
                @else
                    action="{{ route('customers.store') }}"
                @endif
                method="POST"
            >

                @csrf

                @if (isset($customer))
                    @method('PUT')
                @endif

                @include('customers.partials.field')

                <hr>

                <button type="submit" class="btn btn-primary">

                    @if (isset($customer))
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