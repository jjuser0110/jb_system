<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * CHANGE OLD DATA FIRST
         */
        DB::table('service_cases')
            ->where('status', 'inprogress')
            ->update([
                'status' => 'accepted'
            ]);

        /**
         * MODIFY ENUM
         */
        DB::statement("
            ALTER TABLE service_cases 
            MODIFY status ENUM(
                'pending',
                'accepted',
                'service_done',
                'complete',
                'cancel'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        /**
         * REVERT DATA
         */
        DB::table('service_cases')
            ->where('status', 'accepted')
            ->update([
                'status' => 'inprogress'
            ]);

        /**
         * REVERT ENUM
         */
        DB::statement("
            ALTER TABLE service_cases 
            MODIFY status ENUM(
                'pending',
                'inprogress',
                'complete',
                'cancel'
            ) DEFAULT 'pending'
        ");
    }
};