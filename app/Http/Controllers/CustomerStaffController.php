<?php

namespace App\Http\Controllers;

use App\Models\CustomerStaff;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerStaffController extends Controller
{
    /**
     * LIST STAFF (optionally filter by customer)
     */
    public function index(Request $request)
    {
        $staffs = CustomerStaff::with('customer')
            ->when($request->customer_id, function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('staff_name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        return view('customer-staff.index', compact('staffs'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function create(Request $request)
    {
        $customers = Customer::all();

        return view('customer-staff.create', compact('customers'));
    }

    /**
     * STORE STAFF
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'staff_name' => 'required',
            'phone_number' => 'nullable',
            'registered_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        CustomerStaff::create([
            'customer_id' => $request->customer_id,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'registered_date' => $request->registered_date,
            'role_id' => 4, // default staff role
        ]);

        return redirect()
            ->route('customer-staff.index')
            ->with('success', 'Staff created successfully');
    }

    /**
     * EDIT FORM
     */
    public function edit(CustomerStaff $customerStaff)
    {
        $customers = Customer::all();

        return view('customer-staff.create', compact('customerStaff', 'customers'));
    }

    /**
     * UPDATE STAFF
     */
    public function update(Request $request, CustomerStaff $customerStaff)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'staff_name' => 'required',
            'phone_number' => 'nullable',
            'registered_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customerStaff->update([
            'customer_id' => $request->customer_id,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'registered_date' => $request->registered_date,
        ]);

        return redirect()
            ->route('customer-staff.index')
            ->with('success', 'Staff updated successfully');
    }

    /**
     * DELETE STAFF
     */
    public function destroy(CustomerStaff $customerStaff)
    {
        $customerStaff->delete();

        return redirect()
            ->route('customer-staff.index')
            ->with('success', 'Staff deleted successfully');
    }
}