@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('owner.index') }}">Owner /</a>
        @if (isset($owner)) Edit @else Create @endif
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">

                <h5 class="card-header">Owner Details</h5>

                <div class="card-body">

                    <form class="row g-3"
                          enctype="multipart/form-data"
                          @if (isset($owner))
                              method="post" action="{{ route('owner.update', $owner) }}"
                          @else
                              method="post" action="{{ route('owner.store') }}"
                          @endif
                          onsubmit="showLoading()">

                        @csrf

                        @if(isset($owner))
                            @method('PUT')
                        @endif

                        {{-- NAME --}}
                        <div class="col-md-7">
                            <label class="form-label">Owner Name</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="Owner Name"
                                   name="name"
                                   value="{{ $owner->name ?? '' }}"
                                   required />
                        </div>

                        {{-- USERNAME --}}
                        <div class="col-md-7">
                            <label class="form-label">Owner Username</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="username"
                                   name="username"
                                   value="{{ $owner->username ?? '' }}"
                                   required
                                   @if(isset($owner)) readonly @endif />
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-7">
                            <label class="form-label">Email</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="Email"
                                   name="email"
                                   value="{{ $owner->email ?? '' }}"
                                   required />
                        </div>

                        {{-- PASSWORD --}}
                        <div class="col-md-7">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   class="form-control"
                                   placeholder="Password"
                                   name="password"
                                   @if(!isset($owner)) required @endif />
                        </div>

                        <hr>

                        {{-- SUBMIT --}}
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection