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
    
        $query = ServiceCase::with([
            'service',
            'companyStaff.user',
            'companyStaff.company',
            'staff'
        ]);
    
        // ADMIN / OWNER = SEE ALL
        if (
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {
    
            $serviceCases = $query
                ->latest()
                ->get();
    
        } else {
    
            // COMPANY STAFF = ONLY OWN COMPANY
            $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
    
            if (!$companyStaff) {
                abort(403);
            }
    
            $serviceCases = $query
                ->whereHas('companyStaff', function ($q) use ($companyStaff) {
                    $q->where('company_id', $companyStaff->company_id);
                })
                ->latest()
                ->get();
        }
    
        return view('service-cases.index', compact('serviceCases'));
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        $user = auth()->user();
    
        // ADMIN / OWNER
        if (
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {
    
            $companies = Company::all();
    
            $companyStaffs = CompanyStaff::with('user', 'company')->get();
    
        } else {
    
            // COMPANY STAFF
            $companyStaff = CompanyStaff::with('company')
                ->where('user_id', $user->id)
                ->first();
    
            if (!$companyStaff) {
                abort(403);
            }
    
            $companies = Company::where(
                'id',
                $companyStaff->company_id
            )->get();
    
            $companyStaffs = CompanyStaff::with('user')
                ->where('id', $companyStaff->id)
                ->get();
        }
    
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

        if (
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {
        
            $companyStaffId = $request->company_staff_id;
        
        } else {
        
            $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
        
            if (!$companyStaff) {
                abort(403);
            }
        
            $companyStaffId = $companyStaff->id;
        }
    
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
    
        // COMPANY STAFF ONLY
        if (
            !$user->isAn('admin') &&
            !$user->isAn('owner') &&
            !$user->isAn('superadmin')
        ) {
    
            $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
    
            if (!$companyStaff) {
                abort(403);
            }
    
            abort_if(
                $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
                403
            );
    
            $companies = Company::where(
                'id',
                $companyStaff->company_id
            )->get();
    
            $companyStaffs = CompanyStaff::with('user')
                ->where('id', $companyStaff->id)
                ->get();
    
        } else {
    
            $companies = Company::all();
    
            $companyStaffs = CompanyStaff::with('user', 'company')->get();
        }
    
        $services = Service::orderBy('name')->get();
    
        return view('service-cases.create', compact(
            'serviceCase',
            'companies',
            'companyStaffs',
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

        // COMPANY STAFF ONLY
        if (
            !$user->isAn('admin') &&
            !$user->isAn('owner') &&
            !$user->isAn('superadmin')
        ) {

            $companyStaff = CompanyStaff::where('user_id', $user->id)->first();

            if (!$companyStaff) {
                abort(403);
            }

            abort_if(
                $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
                403
            );
        }
    
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
        $user = auth()->user();
    
        // ADMIN / OWNER CAN DELETE ALL
        if (
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {
    
            $serviceCase->delete();
    
        } else {
    
            $companyStaff = CompanyStaff::where('user_id', $user->id)->first();
    
            if (!$companyStaff) {
                abort(403);
            }
    
            abort_if(
                $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
                403
            );
    
            $serviceCase->delete();
        }
    
        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case deleted successfully');
    }

    private function adminOnly()
    {
        $user = auth()->user();

        abort_unless(
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin'),
            403
        );
    }

    public function pending()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with([
            'service',
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        return view('service-cases.pending', compact('serviceCases'));
    }

    public function accepted()
    {
        $this->adminOnly();
    
        $serviceCases = ServiceCase::with([
            'service',
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'accepted')
        ->latest()
        ->get();
    
        return view('service-cases.accepted', compact('serviceCases'));
    }

    public function completed()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with([
            'service',
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'complete')
        ->latest()
        ->get();

        return view('service-cases.completed', compact('serviceCases'));
    }

    public function accept(ServiceCase $serviceCase)
    {
        $this->adminOnly();

        $serviceCase->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Case accepted');
    }

    public function complete(Request $request, ServiceCase $serviceCase)
    {
        $this->adminOnly();

        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $serviceCase->update([
            'status' => 'complete',
            'price' => $request->price,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Case completed');
    }

    public function togglePayment(ServiceCase $serviceCase)
    {
        $this->adminOnly();

        $serviceCase->update([
            'is_paid' => !$serviceCase->is_paid
        ]);

        return back();
    }
}