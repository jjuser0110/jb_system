<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_cases', function (Blueprint $table) {

            // add new description column
            $table->text('description')->nullable();

            // remove old service relation
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');

            // optional:
            // remove old photo column because media library handles files
            $table->dropColumn('photo');
        });
    }

    public function down(): void
    {
        Schema::table('service_cases', function (Blueprint $table) {

            $table->unsignedBigInteger('service_id')->nullable();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete();

            $table->string('photo')->nullable();

            $table->dropColumn('description');
        });
    }
};