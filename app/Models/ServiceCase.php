<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceCase extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'company_staff_id',
        'submit_datetime',
        'description',
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
}