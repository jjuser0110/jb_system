<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    /**
     * LIST OWNERS
     */
    public function index()
    {
        $owners = User::whereIs('owner')->get();

        return view('owner.index', compact('owners'));
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        $companies = Company::whereNull('user_id')->get();

         return view('owner.create', compact('companies'));

    }

    /**
     * STORE OWNER
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'exists:companies,id',
            'new_company_names' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // CHECK NEW COMPANIES
        if ($request->new_company_names) {

            $companyNames = preg_split('/\r\n|\r|\n/', $request->new_company_names);

            foreach ($companyNames as $companyName) {

                $companyName = trim($companyName);

                if (!$companyName) {
                    continue;
                }

                $exists = Company::whereRaw(
                    'LOWER(company_name) = ?',
                    [strtolower($companyName)]
                )->exists();

                if ($exists) {

                    return redirect()->back()
                        ->withErrors([
                            'new_company_names' =>
                                "Company '{$companyName}' already taken by another owner"
                        ])
                        ->withInput();
                }
            }
        }
    
        $owner = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        
        $owner->assign('owner');
        // ASSIGN EXISTING COMPANIES
        if ($request->company_ids) {

            Company::whereIn('id', $request->company_ids)
                ->update([
                    'user_id' => $owner->id
                ]);
        }

        // CREATE NEW COMPANIES
        if ($request->new_company_names) {

            $companyNames = preg_split('/\r\n|\r|\n/', $request->new_company_names);

            foreach ($companyNames as $companyName) {

                $companyName = trim($companyName);

                if (!$companyName) {
                    continue;
                }

                Company::create([
                    'company_name' => $companyName,
                    'user_id' => $owner->id,
                ]);
            }
        }
    
        return redirect()->route('owner.index')
            ->withSuccess('Owner created successfully');
    }
    /**
     * EDIT PAGE
     */
    public function edit(User $owner)
    {
        $companies = Company::whereNull('user_id')
            ->orWhere('user_id', $owner->id)
            ->get();
    
        return view('owner.create', compact('owner', 'companies'));
    }
    /**
     * UPDATE OWNER
     */
    public function update(Request $request, User $owner)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $owner->id,
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'exists:companies,id',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $data = [
            'name' => $request->name,
            'username' => $request->username,
        ];
    
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
    
        $owner->update($data);
    
        // Remove all previous companies
        Company::where('user_id', $owner->id)
            ->update([
                'user_id' => null
            ]);
    
        // Assign selected companies
        if ($request->company_ids) {
    
            Company::whereIn('id', $request->company_ids)
                ->update([
                    'user_id' => $owner->id
                ]);
        }
    
        return redirect()->route('owner.index')
            ->withSuccess('Owner updated successfully');
    }

    /**
     * DELETE OWNER
     */
    public function destroy(User $owner)
    {
        $owner->delete();

        return redirect()->route('owner.index')
            ->withSuccess('Owner deleted successfully');
    }
}