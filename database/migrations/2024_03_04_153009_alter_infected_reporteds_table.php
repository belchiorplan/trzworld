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
        Schema::table('infected_reporteds', function (Blueprint $table) {
            $table->unique(['infected_survivor_id', 'reporting_survivor_id'], 'infected_reporting_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infected_reporteds', function (Blueprint $table) {
            $table->dropUnique('infected_reporting_unique');
        });
    }
};
