<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Bouncer;

class CompanyStaffController extends Controller
{
    /**
     * LIST STAFF
     */
    public function index()
    {
        if (
            auth()->user()->isAn('admin') ||
            auth()->user()->isAn('superadmin')
        ) {

            $staffs = User::with('company')
                ->whereIs('company_staff')
                ->latest()
                ->paginate(10);

        } else {

            $staffs = User::with('company')
                ->whereIs('company_staff')
                ->whereHas('company', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                ->latest()
                ->paginate(10);
        }

        return view('company-staff.index', compact('staffs'));
    }

    /**
     * CREATE FORM
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
            'username'   => 'required|unique:users,username',
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        /**
         * OWNER ONLY CAN CREATE
         * STAFF FOR OWN COMPANY
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

        /**
         * CREATE USER
         */
        $user = User::create([
            'company_id' => $request->company_id,
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        /**
         * ASSIGN ROLE
         */
        Bouncer::assign('company_staff')->to($user);

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff created successfully');
    }

    /**
     * EDIT FORM
     */
    public function edit(User $user)
    {
        if (!$user->isAn('company_staff')) {
            abort(404);
        }

        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($user->company->user_id != auth()->id()) {
                abort(403);
            }

            $companies = Company::where('user_id', auth()->id())->get();

        } else {

            $companies = Company::all();
        }

        return view('company-staff.create', compact('user', 'companies'));
    }

    /**
     * UPDATE STAFF
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'username'   => 'required|unique:users,username,' . $user->id,
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($user->company->user_id != auth()->id()) {
                abort(403);
            }
        }

        $user->update([
            'company_id' => $request->company_id,
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
        ]);

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff updated successfully');
    }

    /**
     * DELETE STAFF
     */
    public function destroy(User $user)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {

            if ($user->company->user_id != auth()->id()) {
                abort(403);
            }
        }

        $user->delete();

        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff deleted successfully');
    }
}