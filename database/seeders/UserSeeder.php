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

        $adminRole = Bouncer::role()->firstOrCreate([
            'name' => 'admin',
            'title' => 'Admin',
        ]);

        $companyRole = Bouncer::role()->firstOrCreate([
            'name' => 'company',
        ], [
            'title' => 'Company',
        ]);
        
        $companyStaffRole = Bouncer::role()->firstOrCreate([
            'name' => 'company_staff',
        ], [
            'title' => 'Company Staff',
        ]);

        $ownerRole = Bouncer::role()->firstOrCreate([
            'name' => 'owner',
            'title' => 'Owner',
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

        $superadmin->assign($adminRole);

        /*
        |--------------------------------------------------------------------------
        | GIVE ALL ABILITIES TO SUPERADMIN
        |--------------------------------------------------------------------------
        */

        $abilities = Bouncer::ability()->all();

        Bouncer::allow($adminRole)->to($abilities);
    }
}