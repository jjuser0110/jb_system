<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCase;
use App\Models\Company;
use App\Models\CompanyStaff;
use Illuminate\Http\Request;

class ServiceCaseController extends Controller
{
    /**
     * LISTING
     */
    public function index()
    {
        $user = auth()->user();

        $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
        
        $serviceCases = ServiceCase::with([
                'service',
                'companyStaff.user',
                'companyStaff.company',
                'staff'
            ])
            ->whereHas('companyStaff', function ($q) use ($companyStaff) {
                $q->where('company_id', $companyStaff->company_id);
            })
            ->latest()
            ->get();

        return view('service-cases.index', compact('serviceCases'));
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        $user = auth()->user();

        $companyStaff = CompanyStaff::with('company')
            ->where('user_id', $user->id)
            ->first();
        
        $companies = $companyStaff ? Company::where('id', $companyStaff->company_id)->get() : collect();
        
        $companyStaffs = CompanyStaff::with('user')
            ->where('company_id', $companyStaff->company_id)
            ->get();

        $services = Service::orderBy('name')->get();

        return view('service-cases.create', compact(
            'companies',
            'companyStaffs',
            'services'
        ));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'submit_datetime' => 'required',
            'service_id' => 'nullable',
            'new_service_name' => 'nullable',
            'photo' => 'nullable|image',
        ]);
    
        $user = auth()->user();
    
        $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
    
        if (!$companyStaff) {
            abort(403);
        }
    
        // service logic
        if ($request->new_service_name) {
            $service = Service::firstOrCreate([
                'name' => trim($request->new_service_name)
            ]);
            $serviceId = $service->id;
        } else {
            $serviceId = $request->service_id;
        }
    
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('cases', 'public')
            : null;
    
        ServiceCase::create([
            'company_staff_id' => $companyStaff->id,
            'service_id' => $serviceId,
            'submit_datetime' => $request->submit_datetime,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);
    
        return redirect()->route('service-cases.index')
            ->with('success', 'Case created successfully');
    }

    /**
     * EDIT FORM
     */
    public function edit(ServiceCase $serviceCase)
    {
        $user = auth()->user();
    
        $companyStaff = CompanyStaff::with('company')
            ->where('user_id', $user->id)
            ->first();
    
        if (!$companyStaff) {
            abort(403);
        }
    
        abort_if(
            $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
            403
        );
    
        $companies = Company::where('id', $companyStaff->company_id)->get();
    
        $services = Service::orderBy('name')->get();
    
        return view('service-cases.create', compact(
            'serviceCase',
            'companies',
            'services'
        ));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, ServiceCase $serviceCase)
    {
        $request->validate([
            'submit_datetime' => 'required',
            'service_id' => 'nullable',
            'new_service_name' => 'nullable',
            'photo' => 'nullable|image',
        ]);
    
        $user = auth()->user();
    
        $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
    
        if (!$companyStaff) {
            abort(403);
        }
    
        abort_if(
            $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
            403
        );
    
        if ($request->new_service_name) {
            $service = Service::firstOrCreate([
                'name' => trim($request->new_service_name)
            ]);
            $serviceId = $service->id;
        } else {
            $serviceId = $request->service_id;
        }
    
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('cases', 'public')
            : $serviceCase->photo;
    
        $serviceCase->update([
            'service_id' => $serviceId,
            'submit_datetime' => $request->submit_datetime,
            'photo' => $photoPath,
            'completed_at' => $request->status === 'complete' ? now() : null,
        ]);
    
        return redirect()->route('service-cases.index')
            ->with('success', 'Case updated successfully');
    }

    /**
     * DELETE
     */
    public function destroy(ServiceCase $serviceCase)
    {
        $serviceCase->delete();

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case deleted successfully');
    }
}