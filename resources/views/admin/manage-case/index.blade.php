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

            <table class="table table-bordered table-hover">

                <thead class="table-light">

                    <tr>
                        <th>ID</th>
                        <th>Staff</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Paid</th>
                        <th>Submit Date</th>
                        <th width="250">
                            Action
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($serviceCases as $case)

                        <tr>

                            {{-- ID --}}
                            <td>
                                {{ $case->id }}
                            </td>

                            {{-- STAFF --}}
                            <td>
                                {{ $case->companyStaff->user->name ?? '-' }}
                            </td>

                            {{-- SERVICE --}}
                            <td>
                                {{ $case->service->name ?? '-' }}
                            </td>
                            {{-- STATUS --}}
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

                            {{-- PRICE --}}
                            <td>

                                @if($case->price)

                                    RM {{ number_format($case->price, 2) }}

                                @else

                                    -

                                @endif

                            </td>

                            {{-- PAYMENT --}}
                            <td>

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

                            {{-- SUBMIT DATE --}}
                            <td>
                                {{ $case->submit_datetime }}
                            </td>

                            {{-- ACTION --}}
                            <td>

                            {{-- IN PROGRESS --}}
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

                            {{-- COMPLETE --}}
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

                            {{-- CANCEL --}}
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

                            {{-- PAYMENT --}}
                            @if($case->status == 'complete')

                                <form method="POST"
                                    action="{{ route('admin.manage-case.payment', $case->id) }}">

                                    @csrf

                                    <button class="btn btn-dark btn-sm w-100">

                                        {{ $case->is_paid ? 'Mark Unpaid' : 'Mark Paid' }}

                                    </button>

                                </form>

                            @endif

                            </td>

                        </tr>

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