<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceCase extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'company_id',
        'submit_datetime',
        'description',
        'status',
        'order_number',
        'completed_at',
        'is_paid',
        'receipt',
        'price',
        'accepted_at',
        'duration',
        'remark',
    ];

    protected $casts = [
        'submit_datetime' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function companyStaff()
    {
        return $this->belongsTo(CompanyStaff::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDurationColorAttribute()
    {
        if (!$this->submit_datetime) {
            return 'secondary';
        }

        $days = now()->diffInDays($this->submit_datetime);

        if ($days <= 2) {
            return 'success';
        }

        if ($days <= 4) {
            return 'warning';
        }

        return 'danger';
    }

    protected static function booted()
    {
        static::saving(function ($serviceCase) {
    
            if ($serviceCase->accepted_at && $serviceCase->completed_at) {
    
                $accepted = \Carbon\Carbon::parse($serviceCase->accepted_at);
                $completed = \Carbon\Carbon::parse($serviceCase->completed_at);
    
                $days = $accepted->diffInDays($completed);
                $hours = $accepted->copy()->addDays($days)->diffInHours($completed);
                $minutes = $accepted->copy()
                    ->addDays($days)
                    ->addHours($hours)
                    ->diffInMinutes($completed);
    
                $parts = [];
    
                if ($days > 0) {
                    $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                }
    
                if ($hours > 0) {
                    $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
                }
    
                if ($minutes > 0) {
                    $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                }
    
                $serviceCase->duration = implode(' ', $parts);
            }
        });
    }
}
