<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_cases', function (Blueprint $table) {

            // Add columns
            $table->foreignId('user_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->after('user_id')
                ->constrained('companies')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_cases', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['company_id']);

            $table->dropColumn(['user_id', 'company_id']);
        });
    }
};
