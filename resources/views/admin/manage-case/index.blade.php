@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="mb-4">Service Case Management</h4>

    {{-- FILTER --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">

        <a href="{{ route('admin.manage-case.index') }}" class="btn btn-secondary">ALL</a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'pending']) }}" class="btn btn-warning">Pending</a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'accepted']) }}" class="btn btn-info">In Progress</a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'service_done']) }}" class="btn btn-primary">Work Done</a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'complete']) }}" class="btn btn-success">Completed</a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'cancel']) }}" class="btn btn-danger">Cancelled</a>

    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Staff</th>
                        <th>Description</th>
                        <th>Photo</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Payment</th>
                        <th>Submit Date</th>
                        <th width="280">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($serviceCases as $case)

                    <tr>
                        <td>#{{ $case->id }}</td>
                        <td>#{{ $case->company->company_name ?? '-' }}</td>

                        {{-- STAFF --}}
                        <td>{{ $case->user->name ?? '-' }}</td>

                        {{-- DESCRIPTION --}}
                        <td>{{ $case->description ?? '-' }}</td>
                        <td>
                            @if($case->getMedia('photos')->count())

                        <a href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#photoModal{{ $case->id }}">
                            View Gallery
                        </a>

                        <div class="modal fade"
                            id="photoModal{{ $case->id }}"
                            tabindex="-1">

                            <div class="modal-dialog modal-xl">

                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Photo Gallery</h5>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="row">

                                            @foreach($case->getMedia('photos') as $photo)

                                                <div class="col-md-4 mb-3">

                                                    <a href="{{ $photo->getUrl() }}"
                                                    target="_blank">

                                                        <img src="{{ $photo->getUrl() }}"
                                                            class="img-fluid rounded border">

                                                    </a>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        @endif
                        </td>
                        <td>{{ $case->remark ?? '-' }}</td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge
                                @if($case->status=='pending') bg-warning
                                @elseif($case->status=='accepted') bg-info
                                @elseif($case->status=='service_done') bg-primary
                                @elseif($case->status=='complete') bg-success
                                @else bg-danger
                                @endif">

                                @if($case->status=='pending')
                                    Pending
                                @elseif($case->status=='accepted')
                                    In Progress
                                @elseif($case->status=='service_done')
                                    Work Done
                                @elseif($case->status=='complete')
                                    Completed
                                @else
                                    Cancelled
                                @endif

                            </span>
                        </td>

                        {{-- PRICE --}}
                        <td>
                            @if($case->price)
                                RM {{ number_format($case->price,2) }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- PAYMENT --}}
                        <td>
                            @if($case->is_paid)
                                <span class="badge bg-success">PAID</span>
                            @else
                                <span class="badge bg-danger">UNPAID</span>
                            @endif
                        </td>

                        {{-- DATE --}}
                        <td>{{ $case->submit_datetime }}</td>

                        {{-- ACTION --}}
                        <td>

                        {{-- START WORK --}}
                        @if($case->status == 'pending')
                            <form method="POST" action="{{ route('admin.manage-case.status', $case) }}">
                                @csrf
                                <input type="hidden" name="status" value="accepted">
                                <button 
                                    class="btn btn-info btn-sm w-100 mb-1"
                                    onclick="return confirm('Are you sure want to proceed this service?')">
                                    Start Work
                                </button>
                            </form>
                        @endif

                        {{-- MARK DONE --}}
                        @if($case->status == 'accepted')
                            <form method="POST" action="{{ route('admin.manage-case.status', $case) }}">
                                @csrf
                                <input type="hidden" name="status" value="service_done">
                                <button class="btn btn-primary btn-sm w-100 mb-1">Mark Work Done</button>
                            </form>
                        @endif

                        {{-- PAYMENT POPUP (NOW CONTAINS PRICE + REMARK + RECEIPT) --}}
                        @if($case->status == 'service_done' && !$case->is_paid)
                            <button class="btn btn-warning btn-sm w-100 mb-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#paymentModal{{ $case->id }}">
                                Payment & Complete
                            </button>
                        @endif

                        {{-- COMPLETED (NO FORM ANYMORE) --}}
                        @if($case->status == 'complete')
                            <span class="badge bg-success w-100 d-block">Completed</span>
                        @endif

                        {{-- CANCEL --}}
                        @if(in_array($case->status,['pending','accepted','service_done']))
                            <form method="POST" action="{{ route('admin.manage-case.status', $case) }}">
                                @csrf
                                <input type="hidden" name="status" value="cancel">
                                <button class="btn btn-danger btn-sm w-100 mt-1">Cancelled</button>
                            </form>
                        @endif

                        </td>
                    </tr>

                    {{-- PAYMENT MODAL --}}
                    <div class="modal fade" id="paymentModal{{ $case->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form method="POST"
                                action="{{ route('admin.manage-case.payment', $case) }}"
                                enctype="multipart/form-data">

                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Payment & Complete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- PRICE --}}
                                    <div class="mb-3">
                                        <label>Price (RM)</label>
                                        <input type="number"
                                            name="price"
                                            step="0.01"
                                            class="form-control"
                                            required>
                                    </div>

                                    {{-- RECEIPT --}}
                                    <div class="mb-3">
                                        <label>Receipt / Photo</label>
                                        <input type="file"
                                            name="receipt"
                                            class="form-control"
                                            required>
                                    </div>

                                    {{-- REMARK --}}
                                    <div class="mb-3">
                                        <label>Remark</label>
                                        <textarea name="remark" class="form-control"></textarea>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-success w-100">
                                        Pay & Complete
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="9" class="text-center">No Record Found</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $serviceCases->links() }}
    </div>

</div>

@endsection