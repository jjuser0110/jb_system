<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_staff', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->after('company_id');

            // REMOVE OLD COLUMNS
            $table->dropColumn([
                'staff_name',
                'phone_number',
                'registered_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('company_staff', function (Blueprint $table) {

            $table->string('staff_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('registered_date')->nullable();

            $table->dropColumn('user_id');
        });
    }
};