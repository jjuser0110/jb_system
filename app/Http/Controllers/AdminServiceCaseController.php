<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCase;

class AdminServiceCaseController extends Controller
{
    /**
     * LIST ALL SERVICE CASE
     */
    public function index(Request $request)
    {
        // ONLY ADMIN / SUPERADMIN
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {
            abort(403);
        }

        $status = $request->status;

        $query = ServiceCase::with([
            'companyStaff.user',
            'service',
        ]);

        if ($status) {
            $query->where('status', $status);
        }

        $serviceCases = $query
            ->latest()
            ->paginate(10);

        return view(
            'admin.manage-case.index',
            compact('serviceCases', 'status')
        );
    }

    /**
     * UPDATE STATUS
     */
    public function updateStatus(Request $request, $id)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,inprogress,complete,cancel',
            'price' => 'nullable|numeric|min:0',
        ]);

        $serviceCase = ServiceCase::findOrFail($id);

        $serviceCase->status = $request->status;

        // IN PROGRESS
        if ($request->status == 'inprogress') {
            $serviceCase->accepted_at = now();
        }

        // COMPLETE
        if ($request->status == 'complete') {

            $serviceCase->completed_at = now();

            $serviceCase->price = $request->price;
        }

        $serviceCase->save();

        return back()->with(
            'success',
            'Service case updated successfully.'
        );
    }

    /**
     * UPDATE PAYMENT
     */
    public function updatePayment($id)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {
            abort(403);
        }

        $serviceCase = ServiceCase::findOrFail($id);

        $serviceCase->is_paid = !$serviceCase->is_paid;

        $serviceCase->save();

        return back()->with(
            'success',
            'Payment status updated.'
        );
    }
}