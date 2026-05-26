<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\Models\User;
use App\Models\DailyActivity;
use App\Models\DailyCleaning;
use App\Models\Extra;
use App\Models\Expense;
use App\Models\DcWorkerSalary;
use App\Models\ServiceCase;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $user = Auth::user();
    
        // BASE QUERY
        $serviceCaseQuery = ServiceCase::query();
    
        /**
         * GET COMPANY ID
         */
        $companyId = $user->company_id;
    
        // OWNER USING companies.user_id
        if (!$companyId && $user->isAn('owner')) {
    
            $companyId = \App\Models\Company::where('user_id', $user->id)
                ->value('id');
        }
    
        /**
         * FILTER NON-ADMIN
         */
        if (
            !$user->isAn('admin') &&
            !$user->isAn('superadmin')
        ) {
    
            if (!$companyId) {
                abort(403);
            }
    
            $serviceCaseQuery->where('company_id', $companyId);
        }
    
        // TOTAL CASES
        $totalCases = (clone $serviceCaseQuery)->count();
    
        // PENDING
        $pendingCases = (clone $serviceCaseQuery)
            ->where('status', 'pending')
            ->count();
    
        // IN PROGRESS
        $inProgressCases = (clone $serviceCaseQuery)
            ->where('status', 'accepted')
            ->count();
    
        // COMPLETED
        $completedCases = (clone $serviceCaseQuery)
            ->where('status', 'complete')
            ->count();
    
        // PAID
        $paidCases = (clone $serviceCaseQuery)
            ->where('is_paid', 1)
            ->count();
    
        // UNPAID
        $unpaidCases = (clone $serviceCaseQuery)
            ->where('is_paid', 0)
            ->count();
    
        // TOTAL REVENUE
        $totalRevenue = (clone $serviceCaseQuery)
            ->where('is_paid', 1)
            ->sum('price');
    
        // RECENT CASES
        $recentCases = (clone $serviceCaseQuery)
            ->with([
                'user',
                'companyStaff'
            ])
            ->latest()
            ->take(10)
            ->get();
    
        return view('home', compact(
            'totalCases',
            'pendingCases',
            'inProgressCases',
            'completedCases',
            'paidCases',
            'unpaidCases',
            'totalRevenue',
            'recentCases'
        ));
    }
    
    public function change_password(Request $request){
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);


        if ($validator->fails()) {
            $message = "";
            foreach($validator->messages()->messages() as $m){
                foreach($m as $mm){
                    $message .=$mm.'\n';
                }
            }
            return redirect()->back()->withInfo($message);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('home')->withSuccess('Password changed successfully.');
    }
}
