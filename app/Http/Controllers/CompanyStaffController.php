<?php

namespace App\Http\Controllers;

use App\Models\CompanyStaff;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Bouncer;

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

            $staffs = CompanyStaff::with('company', 'user')
                ->latest()
                ->paginate(10);

        } else {

            $staffs = CompanyStaff::with('company', 'user')
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
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
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
    
        DB::transaction(function () use ($request) {

            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
            // IMPORTANT: assign role in Bouncer
            Bouncer::assign('company_staff')->to($user);
        
            CompanyStaff::create([
                'company_id' => $request->company_id,
                'user_id' => $user->id,
            ]);
        });
        
        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff created successfully');
    }
    public function assignStaff(Request $request)
    {
        $user = User::find($request->user_id);

        CompanyStaff::create([
            'company_id' => $request->company_id,
            'user_id' => $user->id,
        ]);

        Bouncer::assign('company_staff')->to($user);

        return back();
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
            'username' => 'required|unique:users,username,' . $companyStaff->user_id . ',id',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $companyStaff->user_id . ',id',
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
    
        /**
         * UPDATE USER
         */
        $companyStaff->user->update([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
        ]);
    
        /**
         * UPDATE STAFF
         */
        $companyStaff->update([
            'company_id' => $request->company_id,
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
    
        $user = $companyStaff->user;
    
        $companyStaff->delete();
    
        if ($user) {
            $user->delete();
        }
    
        return redirect()
            ->route('company-staff.index')
            ->with('success', 'Staff deleted successfully');
    }
}