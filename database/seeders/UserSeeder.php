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
        // 1. Create role if not exists
        $role = Bouncer::role()->firstOrCreate([
            'name' => 'superadmin',
        ]);

        // 2. Create or get user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Superadmin',
                'username' => 'superadmin',
                'password' => Hash::make('admin99999'),
            ]
        );

        // 3. Assign role properly (object, not string)
        $user->assign($role);

        // 4. Give all abilities to role
        $abilities = Bouncer::ability()->all();

        Bouncer::allow($role)->to($abilities);
    }
}