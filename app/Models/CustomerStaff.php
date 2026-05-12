<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerStaff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'staff_name',
        'phone_number',
        'registered_date',
        'role_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

