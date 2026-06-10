<?php

namespace App\Http\Controllers;

use App\Models\ServiceCase;
use App\Models\Company;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiceCaseExport;

class ServiceCaseController extends Controller
{
    /**
     * LISTING
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $dateFrom = $request->date_from
            ?? now()->startOfMonth()->format('Y-m-d');

        $dateTo = $request->date_to
            ?? now()->endOfMonth()->format('Y-m-d');

        $query = ServiceCase::with(['user.company']);

        /**
         * COMPANY RESTRICTION
         */
        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
        
            // STAFF
            if ($user->company_id) {
        
                $allowedCompanyIds = [$user->company_id];
        
            }
            // OWNER
            elseif ($user->isAn('owner')) {
        
                $allowedCompanyIds = Company::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
        
            } else {
        
                abort(403);
        
            }
        
            if (empty($allowedCompanyIds)) {
                abort(403);
            }
        
            $query->whereIn('company_id', $allowedCompanyIds);
        
            $companies = Company::whereIn('id', $allowedCompanyIds)
                ->orderBy('company_name')
                ->get();
        }
        else {
        
            $companies = Company::orderBy('company_name')->get();
        
        }

        /**
         * FILTER: COMPANY
         */
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        /**
         * FILTER: STATUS
         */
        if ($request->status) {
            $query->where('status', $request->status);
        }

        /**
         * DATE FILTER
         */
        $query->whereDate('submit_datetime', '>=', $dateFrom)
            ->whereDate('submit_datetime', '<=', $dateTo);

        $serviceCases = $query->latest()->get();

        return view('service-cases.index', compact(
            'serviceCases',
            'companies',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        $user = auth()->user();
    
        if ($user->isAn('admin') || $user->isAn('superadmin')) {
    
            $companies = Company::all();
    
        } else {
    
            // Staff normally has company_id
            $companyId = $user->company_id;
    
            // Owner may not have company_id, find company by user_id
            if ($user->isAn('admin') || $user->isAn('superadmin')) {

                $companies = Company::all();
            
            } elseif ($user->isAn('owner')) {
            
                $companies = Company::where('user_id', $user->id)
                    ->orderBy('company_name')
                    ->get();
            
                if ($companies->isEmpty()) {
                    abort(403, 'No company assigned.');
                }
            
            } else {
            
                $companyId = $user->company_id;
            
                if (!$companyId) {
                    abort(403, 'No company assigned.');
                }
            
                $companies = Company::where('id', $companyId)->get();
            }
        }
    
        return view('service-cases.create', compact('companies'));
    }
    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'submit_datetime' => 'required',
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);
        $user = auth()->user();

        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
        
            if ($user->isAn('owner')) {
        
                $allowed = Company::where('user_id', $user->id)
                    ->where('id', $request->company_id)
                    ->exists();
        
                abort_unless($allowed, 403);
        
            } else {
        
                abort_unless(
                    $user->company_id == $request->company_id,
                    403
                );
        
            }
        }

        $serviceCase = ServiceCase::create([
            'user_id' => $user->id,
            'company_id' => $request->company_id,
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'status' => 'pending',
        ]);

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $photo) {
        
                $serviceCase
                    ->addMedia($photo)
                    ->toMediaCollection('photos');
            }
        }

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case created successfully');
    }
        /**
     * SHOW
     */
    public function show(ServiceCase $serviceCase)
    {
        return view('service-cases.show', compact('serviceCase'));
    }
    /**
     * EDIT
     */
    public function edit(ServiceCase $serviceCase)
    {
        $user = auth()->user();

        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
        
            if ($user->company_id) {
        
                $allowedCompanyIds = [$user->company_id];
        
            } elseif ($user->isAn('owner')) {
        
                $allowedCompanyIds = Company::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
        
            } else {
        
                abort(403);
        
            }
        
            abort_unless(
                in_array($serviceCase->company_id, $allowedCompanyIds),
                403
            );
        }

        if ($user->isAn('admin') || $user->isAn('superadmin')) {

            $companies = Company::all();
        
        } elseif ($user->isAn('owner')) {
        
            $companies = Company::where('user_id', $user->id)
                ->orderBy('company_name')
                ->get();
        
        } else {
        
            $companies = Company::where('id', $user->company_id)
                ->get();
        }

        return view('service-cases.create', compact(
            'serviceCase',
            'companies'
        ));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, ServiceCase $serviceCase)
    {
        $request->validate([
            'submit_datetime' => 'required',
            'description' => 'required|string',
            'status' => 'nullable|in:pending,accepted,service_done,complete,cancel,reject',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);

        $user = auth()->user();

        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
        
            if ($user->company_id) {
        
                $allowedCompanyIds = [$user->company_id];
        
            } elseif ($user->isAn('owner')) {
        
                $allowedCompanyIds = Company::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
        
            } else {
        
                abort(403);
        
            }
        
            abort_unless(
                in_array($serviceCase->company_id, $allowedCompanyIds),
                403
            );
        }

        $serviceCase->update([
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'status' => $request->status ?? $serviceCase->status,
        ]);

        if ($request->hasFile('photos')) {

            foreach ($request->file('photos') as $photo) {
        
                $serviceCase
                    ->addMedia($photo)
                    ->toMediaCollection('photos');
            }
        }

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case updated successfully');
    }

    /**
     * DELETE
     */
    public function destroy(ServiceCase $serviceCase)
    {
        $user = auth()->user();

        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
        
            if ($user->company_id) {
        
                $allowedCompanyIds = [$user->company_id];
        
            } elseif ($user->isAn('owner')) {
        
                $allowedCompanyIds = Company::where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
        
            } else {
        
                abort(403);
        
            }
        
            abort_unless(
                in_array($serviceCase->company_id, $allowedCompanyIds),
                403
            );
        }

        $serviceCase->clearMediaCollection('photos');
        $serviceCase->delete();

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case deleted successfully');
    }

    /**
     * ADMIN CHECK
     */
    private function adminOnly()
    {
        $user = auth()->user();

        abort_unless(
            $user->isAn('admin') ||
            $user->isAn('superadmin'),
            403
        );
    }

    /**
     * STATUS LISTS
     */
    public function pending()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with('user.company')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('service-cases.pending', compact('serviceCases'));
    }

    public function accepted()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with('user.company')
            ->where('status', 'accepted')
            ->latest()
            ->get();

        return view('service-cases.accepted', compact('serviceCases'));
    }

    public function workDone()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with('user.company')
            ->where('status', 'service_done')
            ->latest()
            ->get();

        return view('service-cases.workdone', compact('serviceCases'));
    }

    public function completed()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with('user.company')
            ->where('status', 'complete')
            ->latest()
            ->get();

        return view('service-cases.completed', compact('serviceCases'));
    }

    /**
     * ACTIONS
     */
    public function accept(ServiceCase $serviceCase)
    {
        $this->adminOnly();

        $serviceCase->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Case accepted');
    }

    public function serviceDone(ServiceCase $serviceCase)
    {
        $this->adminOnly();

        $serviceCase->update([
            'status' => 'service_done',
        ]);

        return back()->with('success', 'Service marked as done');
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

    /**
     * EXPORT
     */
    public function export(Request $request)
    {
        $query = ServiceCase::with(['user.company']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('submit_datetime', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('submit_datetime', '<=', $request->date_to);
        }

        $serviceCases = $query->latest()->get();

        return Excel::download(
            new ServiceCaseExport($serviceCases),
            'service-cases.xlsx'
        );
    }
}