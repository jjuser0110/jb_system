@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">

        <h5 class="card-header">
            {{ isset($service) ? 'Edit Service' : 'Create Service' }}
        </h5>

        <div class="card-body">

            <form method="POST"
                action="{{ isset($service)
                    ? route('services.update', $service->id)
                    : route('services.store') }}">

                @csrf

                @if(isset($service))
                    @method('PUT')
                @endif

                <div class="mb-3">

                    <label class="form-label">
                        Service Name
                    </label>

                    <input type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $service->name ?? '') }}"
                        placeholder="Enter service name">

                    @error('name')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

                <button class="btn btn-primary">

                    {{ isset($service) ? 'Update Service' : 'Save Service' }}

                </button>

            </form>

        </div>

    </div>

</div>

@endsection