<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCase;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminServiceCaseController extends Controller
{
    private function adminOnly()
    {
        abort_unless(
            auth()->user()->isAn('admin') ||
            auth()->user()->isAn('superadmin'),
            403
        );
    }

    /**
     * LIST ALL CASES (ADMIN)
     */
    public function index(Request $request)
    {
        $this->adminOnly();

        $query = ServiceCase::with(['user.company']);

        $status = $request->status ?? 'pending';
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $serviceCases = $query->latest()->paginate(10);

        return view('admin.manage-case.index', compact('serviceCases'));
    }

    /**
     * UPDATE STATUS (UNIFIED FLOW)
     */
    public function updateStatus(Request $request, ServiceCase $serviceCase)
    {
        $this->adminOnly();
    
        $request->validate([
            'status' => 'required|in:pending,accepted,service_done,complete,cancel',
            'price' => 'nullable|numeric|min:0',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'remark' => 'nullable|string',
        ]);
    
        // 🚨 BLOCK completing if not paid
        if ($request->status === 'complete' && !$serviceCase->is_paid) {
            return back()->with('error', 'User must complete payment first.');
        }
    
        $serviceCase->status = $request->status;
    
        // START WORK
        if ($request->status === 'accepted') {

            if (!$serviceCase->order_number) {
        
                $prefix = 'SO' . now()->format('ym');
        
                $sequence = ServiceCase::where('order_number', 'like', $prefix . '%')
                    ->count() + 1;
        
                $serviceCase->order_number = $prefix . str_pad(
                    $sequence,
                    3,
                    '0',
                    STR_PAD_LEFT
                );
            }
        
            $serviceCase->accepted_at = now();
        }
        if ($request->status === 'service_done') {

            if ($request->hasFile('receipt')) {
        
                if ($serviceCase->receipt) {
                    Storage::disk('public')->delete($serviceCase->receipt);
                }
        
                $serviceCase->receipt = $request->file('receipt')
                    ->store('service_done', 'public');
            }
        
            $serviceCase->remark = $request->remark;
        }
    
        // COMPLETE WORK (ONLY AFTER PAYMENT)
        if ($request->status === 'complete') {
            $serviceCase->completed_at = now();
            $serviceCase->price = $request->price;
        }
    
        $serviceCase->save();
    
        return back()->with('success', 'Status updated successfully');
    }
    /**
     * PAYMENT UPDATE
     */
    public function updatePayment(Request $request, ServiceCase $serviceCase)
    {
        $this->adminOnly();
    
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);
    
        $serviceCase->update([
            'price' => $request->price,
            'is_paid' => true,
            'status' => 'complete',
            'completed_at' => now(),
        ]);
    
        return back()->with('success', 'Payment completed & case closed');
    }

    public function getDurationAttribute()
    {
        if (!$this->completed_at || !$this->submit_datetime) {
            return null;
        }

        $start = Carbon::parse($this->submit_datetime);
        $end = Carbon::parse($this->completed_at);

        $diffInMinutes = $start->diffInMinutes($end);

        $days = floor($diffInMinutes / 1440);
        $hours = floor(($diffInMinutes % 1440) / 60);
        $minutes = $diffInMinutes % 60;

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'total_minutes' => $diffInMinutes,
        ];
    }
}