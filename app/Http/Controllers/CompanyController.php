<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isAn('admin') || auth()->user()->isAn('superadmin')) {

            $companies = Company::latest()->paginate(10);
        
        } else {
        
            $companies = Company::where('user_id', auth()->id())
                ->latest()
                ->paginate(10);
        }
    
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'register_date' => 'nullable|date',
            'contact_no' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Company::create([
            'company_name' => $request->company_name,
            'register_date' => $request->register_date,
            'contact_no' => $request->contact_no,
            'role_id' => 3,
            'user_id' => Auth::id(), 
        ]);

        return redirect()
            ->route('companies.index')
            ->withSuccess('Company created successfully');
    }

    public function edit(Company $company)
    {
        if ($company->user_id != auth()->id()) {
            abort(403);
        }
    
        return view('companies.create', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'register_date' => 'nullable|date',
            'contact_no' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company->update([
            'company_name' => $request->company_name,
            'register_date' => $request->register_date,
            'contact_no' => $request->contact_no,
        ]);

        return redirect()
            ->route('companies.index')
            ->withSuccess('Company updated successfully');
    }

    public function destroy(Company $company)
    {
        if (
            !auth()->user()->isAn('admin') &&
            !auth()->user()->isAn('superadmin')
        ) {
    
            if ($company->user_id != auth()->id()) {
                abort(403);
            }
        }
    
        $company->delete();
    
        return redirect()
            ->route('companies.index')
            ->withSuccess('Company deleted successfully');
    }
}