<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_staff', function (Blueprint $table) {
    
            $table->id();
    
            $table->unsignedBigInteger('customer_id');
            $table->string('staff_name');
            $table->string('phone_number')->nullable();
            $table->date('registered_date')->nullable();
    
            $table->unsignedBigInteger('role_id')->default(4);
    
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_staff');
    }
};
