<?php

namespace App\Http\Controllers;

use App\Models\CompanyStaff;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyStaffController extends Controller
{
    /**
     * LIST STAFF
     */
    public function index(Request $request)
    {
        if (
            auth()->user()->isAn('admin') ||
            auth()->user()->isAn('superadmin')
        ) {

            $staffs = CompanyStaff::with('company')
                ->latest()
                ->paginate(10);

        } else {

            $staffs = CompanyStaff::with('company')
                ->whereHas('company', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->latest()
                ->paginate(10);
        }

        return view('company-staff.index', compact('staffs'));
    }

    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        if (
            auth()->user()->isAn('admin') ||
            auth()->user()->isAn('superadmin')
        ) {

            $companies = Company::all();

        } else {

            $companies = Company::where('user_id', auth()->id())->get();
        }

        return view('company-staff.create', compact('companies'));
    }

    /**
     * STORE STAFF
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'staff_name' => 'required',
            'phone_number' => 'nullable',
            'registered_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /**
         * OWNER CAN ONLY CREATE STAFF
         * FOR OWN COMPANY
         */
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            $company = Company::where('id', $request->company_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$company) {
                abort(403);
            }
        }

        CompanyStaff::create([
            'company_id' => $request->company_id,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'registered_date' => $request->registered_date,
            'role_id' => 4,
        ]);

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff created successfully');
    }

    /**
     * EDIT FORM
     */
    public function edit(CompanyStaff $companyStaff)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($companyStaff->company->user_id != auth()->id()) {
                abort(403);
            }

            $companies = Company::where('user_id', auth()->id())->get();

        } else {

            $companies = Company::all();
        }

        return view('company-staff.create', compact('companyStaff', 'companies'));
    }

    /**
     * UPDATE STAFF
     */
    public function update(Request $request, CompanyStaff $companyStaff)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'staff_name' => 'required',
            'phone_number' => 'nullable',
            'registered_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /**
         * OWNER CAN ONLY UPDATE
         * OWN COMPANY STAFF
         */
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($companyStaff->company->user_id != auth()->id()) {
                abort(403);
            }

            $company = Company::where('id', $request->company_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$company) {
                abort(403);
            }
        }

        $companyStaff->update([
            'company_id' => $request->company_id,
            'staff_name' => $request->staff_name,
            'phone_number' => $request->phone_number,
            'registered_date' => $request->registered_date,
        ]);

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff updated successfully');
    }

    /**
     * DELETE STAFF
     */
    public function destroy(CompanyStaff $companyStaff)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($companyStaff->company->user_id != auth()->id()) {
                abort(403);
            }
        }

        $companyStaff->delete();

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff deleted successfully');
    }
}