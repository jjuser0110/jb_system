<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Bouncer;

class UserSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | CREATE ROLES
        |--------------------------------------------------------------------------
        */

        $superadminRole = Bouncer::role()->firstOrCreate([
            'name' => 'superadmin',
            'title' => 'Super Admin',
        ]);

        $adminRole = Bouncer::role()->firstOrCreate([
            'name' => 'admin',
            'title' => 'Admin',
        ]);

        $customerRole = Bouncer::role()->firstOrCreate([
            'name' => 'customer',
            'title' => 'Customer',
        ]);

        $customerStaffRole = Bouncer::role()->firstOrCreate([
            'name' => 'customer_staff',
            'title' => 'Customer Staff',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE SUPERADMIN USER
        |--------------------------------------------------------------------------
        */

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Superadmin',
                'username' => 'superadmin',
                'password' => Hash::make('admin99999'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ASSIGN ROLE
        |--------------------------------------------------------------------------
        */

        $superadmin->assign($superadminRole);

        /*
        |--------------------------------------------------------------------------
        | GIVE ALL ABILITIES TO SUPERADMIN
        |--------------------------------------------------------------------------
        */

        $abilities = Bouncer::ability()->all();

        Bouncer::allow($superadminRole)->to($abilities);
    }
}