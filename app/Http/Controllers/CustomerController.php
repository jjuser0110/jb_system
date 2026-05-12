<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with('role')
            ->when($request->search, function ($query) use ($request) {
                $query->where('customer_name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($request->per_page ?? 10);
    
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'register_date' => 'nullable|date',
            'contact_no' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Customer::create([
            'customer_name' => $request->customer_name,
            'register_date' => $request->register_date,
            'contact_no' => $request->contact_no,
            'role_id' => 3,
        ]);

        return redirect()
            ->route('customers.index')
            ->withSuccess('Customer created successfully');
    }

    public function edit(Customer $customer)
    {
        return view('customers.create', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'register_date' => 'nullable|date',
            'contact_no' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer->update([
            'customer_name' => $request->customer_name,
            'register_date' => $request->register_date,
            'contact_no' => $request->contact_no,
        ]);

        return redirect()
            ->route('customers.index')
            ->withSuccess('Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->withSuccess('Customer deleted successfully');
    }
}