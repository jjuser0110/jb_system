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
        Bouncer::allow('admin')->to('manage-company-staff');
        Bouncer::allow('superadmin')->to('manage-company-staff');
        Bouncer::allow('owner')->to('manage-company-staff');

        /**
         * CASE MODULE
         */
        Bouncer::allow('admin')->to('manage-case');
        Bouncer::allow('superadmin')->to('manage-case');
        Bouncer::allow('owner')->to('staff-manage-case');
        Bouncer::allow('company_staff')->to('staff-manage-case');

        Bouncer::allow('admin')->to('admin-manage-case');
        Bouncer::allow('superadmin')->to('admin-manage-case');

        /**
         * SERVICE MODULE
         */
        Bouncer::allow('admin')->to('manage-service');
        Bouncer::allow('superadmin')->to('manage-service');
        Bouncer::allow('owner')->to('manage-service');

        /**
         * USER MANAGEMENT
         */
        Bouncer::allow('admin')->to('manage-users');
        Bouncer::allow('superadmin')->to('manage-users');
    }
}