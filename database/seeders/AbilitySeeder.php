<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Bouncer;

class AbilitySeeder extends Seeder
{
    public function run(): void
    {
        /**
         * DASHBOARD
         */
        Bouncer::allow('admin')->to('view-dashboard');
        Bouncer::allow('superadmin')->to('view-dashboard');
        Bouncer::allow('owner')->to('view-dashboard');
        Bouncer::allow('company_staff')->to('view-dashboard');

        /**
         * COMPANY MODULE
         */
        Bouncer::allow('admin')->to('manage-company');
        Bouncer::allow('superadmin')->to('manage-company');
        Bouncer::allow('owner')->to('manage-company');

        /**
         * CASE MODULE
         */
        Bouncer::allow('admin')->to('manage-case');
        Bouncer::allow('superadmin')->to('manage-case');
        Bouncer::allow('owner')->to('manage-case');
        Bouncer::allow('company_staff')->to('manage-case');

        /**
         * USER MANAGEMENT
         */
        Bouncer::allow('admin')->to('manage-users');
        Bouncer::allow('superadmin')->to('manage-users');
    }
}