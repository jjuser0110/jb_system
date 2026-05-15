<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyStaff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'staff_name',
        'phone_number',
        'registered_date',
        'role_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

