<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('service_cases', function (Blueprint $table) {
    
            $table->decimal('price', 10, 2)
                ->nullable()
                ->after('status');
    
            $table->boolean('is_paid')
                ->default(false)
                ->after('price');
    
            $table->timestamp('accepted_at')
                ->nullable()
                ->after('submit_datetime');
    
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_cases', function (Blueprint $table) {
            //
        });
    }
};
