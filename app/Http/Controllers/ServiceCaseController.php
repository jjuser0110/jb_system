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
            $companyId = $user->company_id;

            // OWNER WITHOUT company_id
            if (!$companyId && $user->isAn('owner')) {
            
                $companyId = Company::where('user_id', $user->id)->value('id');
            }
            
            if (!$companyId) {
                abort(403);
            }
            
            $query->where('company_id', $companyId);
            
            $companies = Company::where('id', $companyId)->get();
        } else {
            $companies = Company::orderBy('company_name')->get();
        }

        /**
         * FILTER: COMPANY
         */
        if ($request->company_id) {
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

        if (
            $user->isAn('admin') ||
            $user->isAn('superadmin')
        ) {
            $companies = Company::all();
        } else {
            $companyId = $user->company_id;

            // OWNER WITHOUT company_id
            if (!$companyId && $user->isAn('owner')) {
            
                $companyId = Company::where('user_id', $user->id)->value('id');
            }
            
            if (!$companyId) {
                abort(403);
            }
            
            $query->where('company_id', $companyId);
            
            $companies = Company::where('id', $companyId)->get();
        }

        return view('service-cases.create', compact('companies'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'submit_datetime' => 'required',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();

        if (!$user->company_id) {
            abort(403);
        }

        $serviceCase = ServiceCase::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'status' => 'pending',
        ]);

        if ($request->hasFile('photo')) {
            $serviceCase
                ->addMediaFromRequest('photo')
                ->toMediaCollection('photos');
        }

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case created successfully');
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
            if ($serviceCase->company_id !== $user->company_id) {
                abort(403);
            }
        }

        $companies = $user->isAn('admin') || $user->isAn('superadmin')
            ? Company::all()
            : Company::where('id', $user->company_id)->get();

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
            'status' => 'required|in:pending,accepted,service_done,complete,cancel,reject',
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();

        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
            if ($serviceCase->company_id !== $user->company_id) {
                abort(403);
            }
        }

        $serviceCase->update([
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'status' => $request->status,
            'completed_at' => $request->status === 'complete'
                ? now()
                : null,
        ]);

        if ($request->hasFile('photo')) {
            $serviceCase->clearMediaCollection('photos');

            $serviceCase
                ->addMediaFromRequest('photo')
                ->toMediaCollection('photos');
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
            if ($serviceCase->company_id !== $user->company_id) {
                abort(403);
            }
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

        if ($request->company_id) {
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