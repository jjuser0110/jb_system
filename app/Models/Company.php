<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'register_date',
        'contact_no',
        'role_id',
        'user_id',
    ];
    
        public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function staffs()
    {
        return $this->hasMany(CompanyStaff::class);
    }
}