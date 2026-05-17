@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="mb-4">
        Service Case Management
    </h4>

    {{-- FILTER --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">

        <a href="{{ route('admin.manage-case.index') }}"
           class="btn btn-secondary">
            ALL
        </a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'pending']) }}"
           class="btn btn-warning">
            Pending
        </a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'inprogress']) }}"
           class="btn btn-info">
            In Progress
        </a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'complete']) }}"
           class="btn btn-success">
            Complete
        </a>

        <a href="{{ route('admin.manage-case.index', ['status' => 'cancel']) }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Staff</th>
                        <th class="d-none d-md-table-cell">
                            Service
                        </th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">
                            Price
                        </th>
                        <th class="d-none d-md-table-cell">
                            Paid
                        </th>
                        <th class="d-none d-md-table-cell">
                            Submit Date
                        </th>
                        <th width="250">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceCases as $case)
                        <tr>
                            <td>
                                #{{ $case->id }}
                            </td>               
                            <td>
                                <button
                                    class="btn btn-link p-0 text-start d-md-none"
                                    data-bs-toggle="modal"
                                    data-bs-target="#caseModal{{ $case->id }}">
                                    {{ $case->companyStaff->user->name ?? '-' }}
                                </button>
                                <span class="d-none d-md-inline">
                                    {{ $case->companyStaff->user->name ?? '-' }}
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $case->service->name ?? '-' }}
                            </td>
                            <td>
                                @if($case->status == 'pending')
                                    <span class="badge bg-warning">
                                        Pending
                                    </span>
                                @elseif($case->status == 'inprogress')
                                    <span class="badge bg-info">
                                        In Progress
                                    </span>
                                @elseif($case->status == 'complete')
                                    <span class="badge bg-success">
                                        Complete
                                    </span>
                                @elseif($case->status == 'cancel')
                                    <span class="badge bg-secondary">
                                        Cancel
                                    </span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($case->price)
                                    RM {{ number_format($case->price, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($case->is_paid)
                                    <span class="badge bg-success">
                                        PAID
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        UNPAID
                                    </span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $case->submit_datetime }}
                            </td>
                            <td>
                                <button
                                    class="btn btn-primary btn-sm d-md-none w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#caseModal{{ $case->id }}">
                                    View
                                </button>
                                <div class="d-none d-md-block">
                                    @if($case->status == 'pending')
                                        <form method="POST"
                                              action="{{ route('admin.manage-case.status', $case->id) }}"
                                              class="mb-2">
                                            @csrf
                                            <input type="hidden"
                                                   name="status"
                                                   value="inprogress">
                                            <button class="btn btn-info btn-sm w-100">
                                                In Progress
                                            </button>
                                        </form>
                                    @endif
                                    @if($case->status == 'inprogress')
                                        <form method="POST"
                                              action="{{ route('admin.manage-case.status', $case->id) }}"
                                              class="mb-2">
                                            @csrf
                                            <input type="hidden"
                                                   name="status"
                                                   value="complete">
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   name="price"
                                                   class="form-control mb-2"
                                                   placeholder="Input Price"
                                                   required>
                                            <button class="btn btn-success btn-sm w-100">
                                                Complete
                                            </button>
                                        </form>
                                    @endif
                                    @if(
                                        $case->status == 'pending' ||
                                        $case->status == 'inprogress'
                                    )
                                        <form method="POST"
                                              action="{{ route('admin.manage-case.status', $case->id) }}"
                                              class="mb-2">
                                            @csrf
                                            <input type="hidden"
                                                   name="status"
                                                   value="cancel">
                                            <button class="btn btn-secondary btn-sm w-100">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                    @if($case->status == 'complete')
                                        @if($case->is_paid)
                                            <div class="mb-2">
                                                <span class="badge bg-success w-100">
                                                    PAID
                                                </span>
                                            </div>
                                            @if($case->receipt)
                                                <a href="{{ asset('storage/' . $case->receipt) }}"
                                                   target="_blank"
                                                   class="btn btn-primary btn-sm w-100">
                                                    View Receipt
                                                </a>
                                            @endif
                                        @else
                                            <form method="POST"
                                                  action="{{ route('admin.manage-case.payment', $case->id) }}"
                                                  enctype="multipart/form-data">
                                                @csrf
                                                <input type="file"
                                                       name="receipt"
                                                       class="form-control mb-2"
                                                       required>
                                                <button class="btn btn-dark btn-sm w-100">
                                                    Mark Paid
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        {{-- MOBILE MODAL --}}
                        <div class="modal fade"
                             id="caseModal{{ $case->id }}"
                             tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Service Case #{{ $case->id }}
                                        </h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Staff:</strong><br>
                                            {{ $case->companyStaff->user->name ?? '-' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Service:</strong><br>
                                            {{ $case->service->name ?? '-' }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Status:</strong><br>
                                            @if($case->status == 'pending')
                                                <span class="badge bg-warning">
                                                    Pending
                                                </span>
                                            @elseif($case->status == 'inprogress')
                                                <span class="badge bg-info">
                                                    In Progress
                                                </span>
                                            @elseif($case->status == 'complete')
                                                <span class="badge bg-success">
                                                    Complete
                                                </span>
                                            @elseif($case->status == 'cancel')
                                                <span class="badge bg-secondary">
                                                    Cancel
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <strong>Price:</strong><br>
                                            @if($case->price)
                                                RM {{ number_format($case->price, 2) }}
                                            @else
                                                -
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <strong>Payment:</strong><br>
                                            @if($case->is_paid)
                                                <span class="badge bg-success">
                                                    PAID
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    UNPAID
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <strong>Submit Date:</strong><br>
                                            {{ $case->submit_datetime }}
                                        </div>

                                        @if($case->receipt)
                                            <div class="mb-3">
                                                <a href="{{ asset('storage/' . $case->receipt) }}"
                                                   target="_blank"
                                                   class="btn btn-primary w-100">
                                                    View Receipt
                                                </a>
                                            </div>
                                        @endif

                                        @if($case->status == 'pending')
                                            <form method="POST"
                                                  action="{{ route('admin.manage-case.status', $case->id) }}"
                                                  class="mb-2">
                                                @csrf
                                                <input type="hidden"
                                                       name="status"
                                                       value="inprogress">
                                                <button class="btn btn-info w-100">
                                                    In Progress
                                                </button>
                                            </form>
                                        @endif
                                        @if($case->status == 'inprogress')
                                            <form method="POST"
                                                  action="{{ route('admin.manage-case.status', $case->id) }}">
                                                @csrf
                                                <input type="hidden"
                                                       name="status"
                                                       value="complete">
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       name="price"
                                                       class="form-control mb-2"
                                                       placeholder="Input Price"
                                                       required>
                                                <button class="btn btn-success w-100">
                                                    Complete
                                                </button>
                                            </form>
                                        @endif

                                        @if(
                                            $case->status == 'pending' ||
                                            $case->status == 'inprogress'
                                        )
                                            <form method="POST"
                                                  action="{{ route('admin.manage-case.status', $case->id) }}"
                                                  class="mt-2">
                                                @csrf
                                                <input type="hidden"
                                                       name="status"
                                                       value="cancel">
                                                <button class="btn btn-secondary w-100">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif

                                        @if($case->status == 'complete' && !$case->is_paid)
                                            <form method="POST"
                                                  action="{{ route('admin.manage-case.payment', $case->id) }}"
                                                  enctype="multipart/form-data"
                                                  class="mt-3">
                                                @csrf
                                                <input type="file"
                                                       name="receipt"
                                                       class="form-control mb-2"
                                                       required>
                                                <button class="btn btn-dark w-100">
                                                    Mark Paid
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No Record Found
                            </td>
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