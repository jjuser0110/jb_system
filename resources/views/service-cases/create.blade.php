@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="card">

    <h5 class="card-header">
        {{ isset($serviceCase) ? 'Edit Case' : 'Create Case' }}
    </h5>

    <div class="card-body">

        <form method="POST"
              enctype="multipart/form-data"
              action="{{ isset($serviceCase)
                  ? route('service-cases.update', $serviceCase->id)
                  : route('service-cases.store') }}">

            @csrf

            @if(isset($serviceCase))
                @method('PUT')
            @endif

            @php
                $isAdmin =
                    auth()->user()->isAn('admin') ||
                    auth()->user()->isAn('owner') ||
                    auth()->user()->isAn('superadmin');
            @endphp

            <div class="row">

                {{-- COMPANY --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">Company</label>

                    @if($isAdmin)

                        <select name="company_id" class="form-control">

                            @foreach($companies as $company)

                                <option value="{{ $company->id }}"
                                    {{ isset($serviceCase) && $serviceCase->company_id == $company->id ? 'selected' : '' }}>

                                    {{ $company->company_name }}

                                </option>

                            @endforeach

                        </select>

                    @else

                        <input type="hidden"
                               name="company_id"
                               value="{{ $companies->first()->id ?? '' }}">

                        <div class="form-control bg-light">
                            {{ $companies->first()->company_name ?? '' }}
                        </div>

                    @endif

                </div>

                {{-- DATETIME --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">Submit Datetime</label>

                    <input type="datetime-local"
                           name="submit_datetime"
                           class="form-control"
                           value="{{ old(
                                'submit_datetime',
                                isset($serviceCase)
                                    ? \Carbon\Carbon::parse($serviceCase->submit_datetime)->format('Y-m-d\TH:i')
                                    : now()->format('Y-m-d\TH:i')
                           ) }}">
                </div>

                {{-- DESCRIPTION --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">Description</label>

                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              required>{{ old('description', $serviceCase->description ?? '') }}</textarea>

                </div>

                {{-- PHOTO --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">Upload Photo</label>

                    <input type="file"
                            name="photos[]"
                            multiple
                            accept="image/*"
                            class="form-control">

                            @if(isset($serviceCase) && $serviceCase->getMedia('photos')->count())

                            <div class="mt-3">

                                <label class="form-label fw-bold">
                                    Uploaded Photos
                                </label>

                                <ul>

                                    @foreach($serviceCase->getMedia('photos') as $photo)

                                        <li>

                                            <a href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#photoModal{{ $photo->id }}">

                                                {{ $photo->file_name }}

                                            </a>

                                        </li>

                                        <!-- Modal -->
                                        <div class="modal fade"
                                            id="photoModal{{ $photo->id }}"
                                            tabindex="-1">

                                            <div class="modal-dialog modal-dialog-centered modal-xl">

                                                <div class="modal-content">

                                                    <div class="modal-header">

                                                        <h5 class="modal-title">
                                                            {{ $photo->file_name }}
                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>

                                                    </div>

                                                    <div class="modal-body text-center">

                                                        <img src="{{ $photo->getUrl() }}"
                                                            class="img-fluid rounded">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </ul>

                            </div>

                            @endif

                </div>

            </div>

            <button class="btn btn-primary">
                {{ isset($serviceCase) ? 'Update Case' : 'Save Case' }}
            </button>

        </form>

    </div>

</div>

</div>

@endsection