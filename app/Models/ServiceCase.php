<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCase extends Model
{
    protected $fillable = [
        'company_staff_id',
        'submit_datetime',
        'service_id',
        'photo',
        'status',
        'completed_at',
        'is_paid',
        'receipt',
        'price',
        'accepted_at',
    ];

    protected $casts = [
        'submit_datetime' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // COMPANY
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // COMPANY STAFF
    public function companyStaff()
    {
        return $this->belongsTo(CompanyStaff::class);
    }

    // STAFF
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // DURATION COLOR
    public function getDurationColorAttribute()
    {
        if (!$this->submit_datetime) {
            return 'secondary';
        }

        $days = now()->diffInDays($this->submit_datetime);

        if ($days <= 2) {
            return 'success'; // green
        }

        if ($days <= 4) {
            return 'warning'; // orange
        }

        return 'danger'; // red
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}