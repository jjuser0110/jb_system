<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_cases', function (Blueprint $table) {

            $table->id();
        
            $table->foreignId('company_staff_id')
                ->constrained('company_staff')
                ->cascadeOnDelete();
        
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();
        
            $table->dateTime('submit_datetime');
        
            $table->string('photo')->nullable();
        
            $table->enum('status', [
                'pending',
                'complete',
                'reject',
                'cancel'
            ])->default('pending');
        
            $table->dateTime('completed_at')->nullable();
        
            $table->timestamps();
        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_cases');
    }
};