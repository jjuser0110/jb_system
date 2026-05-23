<?php

namespace App\Http\Controllers;

use App\Models\ServiceCase;
use App\Models\Company;
use App\Models\CompanyStaff;
use Illuminate\Http\Request;
use App\Exports\ServiceCaseExport;
use Maatwebsite\Excel\Facades\Excel;

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

        $query = ServiceCase::with([
            'companyStaff.user',
            'companyStaff.company',
            'staff'
        ]);

        /**
         * COMPANY STAFF ONLY SEE OWN COMPANY
         */
        if (
            !$user->isAn('admin') &&
            !$user->isAn('owner') &&
            !$user->isAn('superadmin')
        ) {

            $companyStaff = CompanyStaff::where(
                'user_id',
                $user->id
            )->first();

            if (!$companyStaff) {
                abort(403);
            }

            $query->whereHas('companyStaff', function ($q) use ($companyStaff) {
                $q->where('company_id', $companyStaff->company_id);
            });

            $companies = Company::where(
                'id',
                $companyStaff->company_id
            )->get();

        } else {

            $companies = Company::orderBy('company_name')->get();
        }

        /**
         * COMPANY FILTER
         */
        if ($request->company_id) {

            $query->whereHas('companyStaff', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        /**
         * STATUS FILTER
         */
        if ($request->status) {
            $query->where('status', $request->status);
        }

        /**
         * DATE FILTER
         */
        $query->whereDate('submit_datetime', '>=', $dateFrom)
            ->whereDate('submit_datetime', '<=', $dateTo);

        $serviceCases = $query
            ->latest()
            ->get();

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
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {

            $companies = Company::all();

            $companyStaffs = CompanyStaff::with(
                'user',
                'company'
            )->get();

        } else {

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

        return view('service-cases.create', compact(
            'companies',
            'companyStaffs'
        ));
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

        /**
         * ADMIN / OWNER
         */
        if (
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {

            $companyStaffId = $request->company_staff_id;

        } else {

            /**
             * COMPANY STAFF
             */
            $companyStaff = CompanyStaff::where(
                'user_id',
                $user->id
            )->first();

            if (!$companyStaff) {
                abort(403);
            }

            $companyStaffId = $companyStaff->id;
        }

        $serviceCase = ServiceCase::create([
            'company_staff_id' => $companyStaffId,
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'status' => 'pending',
        ]);

        /**
         * PHOTO UPLOAD
         */
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
     * EDIT FORM
     */
    public function edit(ServiceCase $serviceCase)
    {
        $user = auth()->user();

        /**
         * COMPANY STAFF ONLY
         */
        if (
            !$user->isAn('admin') &&
            !$user->isAn('owner') &&
            !$user->isAn('superadmin')
        ) {

            $companyStaff = CompanyStaff::where(
                'user_id',
                $user->id
            )->first();

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

            $companyStaffs = CompanyStaff::with(
                'user',
                'company'
            )->get();
        }

        return view('service-cases.create', compact(
            'serviceCase',
            'companies',
            'companyStaffs'
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
            'photo' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();

        /**
         * COMPANY STAFF ONLY
         */
        if (
            !$user->isAn('admin') &&
            !$user->isAn('owner') &&
            !$user->isAn('superadmin')
        ) {

            $companyStaff = CompanyStaff::where(
                'user_id',
                $user->id
            )->first();

            if (!$companyStaff) {
                abort(403);
            }

            abort_if(
                $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
                403
            );
        }

        $serviceCase->update([
            'description' => $request->description,
            'submit_datetime' => $request->submit_datetime,
            'completed_at' => $request->status === 'complete'
                ? now()
                : null,
        ]);

        /**
         * REPLACE PHOTO
         */
        if ($request->hasFile('photo')) {

            $serviceCase
                ->clearMediaCollection('photos');

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
            $user->isAn('admin') ||
            $user->isAn('owner') ||
            $user->isAn('superadmin')
        ) {

            $serviceCase->clearMediaCollection('photos');

            $serviceCase->delete();

        } else {

            $companyStaff = CompanyStaff::where(
                'user_id',
                $user->id
            )->first();

            if (!$companyStaff) {
                abort(403);
            }

            abort_if(
                $serviceCase->companyStaff->company_id !== $companyStaff->company_id,
                403
            );

            $serviceCase->clearMediaCollection('photos');

            $serviceCase->delete();
        }

        return redirect()
            ->route('service-cases.index')
            ->with('success', 'Case deleted successfully');
    }

    /**
     * ADMIN ONLY
     */
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

    /**
     * PENDING
     */
    public function pending()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with([
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        return view('service-cases.pending', compact('serviceCases'));
    }

    /**
     * ACCEPTED
     */
    public function accepted()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with([
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'accepted')
        ->latest()
        ->get();

        return view('service-cases.accepted', compact('serviceCases'));
    }

    /**
     * COMPLETED
     */
    public function completed()
    {
        $this->adminOnly();

        $serviceCases = ServiceCase::with([
            'companyStaff.user',
            'companyStaff.company',
        ])
        ->where('status', 'complete')
        ->latest()
        ->get();

        return view('service-cases.completed', compact('serviceCases'));
    }

    /**
     * ACCEPT
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

    /**
     * COMPLETE
     */
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

    /**
     * TOGGLE PAYMENT
     */
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
        $query = ServiceCase::with([
            'companyStaff.user',
            'companyStaff.company',
        ]);

        if ($request->company_id) {

            $query->whereHas('companyStaff', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {

            $query->whereDate(
                'submit_datetime',
                '>=',
                $request->date_from
            );
        }

        if ($request->date_to) {

            $query->whereDate(
                'submit_datetime',
                '<=',
                $request->date_to
            );
        }

        $serviceCases = $query
            ->latest()
            ->get();

        return Excel::download(
            new ServiceCaseExport($serviceCases),
            'service-cases.xlsx'
        );
    }
}