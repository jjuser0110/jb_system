<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('customers', 'companies');
        Schema::rename('customer_staff', 'company_staff');
    }

    public function down(): void
    {
        Schema::rename('companies', 'customers');
        Schema::rename('company_staff', 'customer_staff');
    }
};
