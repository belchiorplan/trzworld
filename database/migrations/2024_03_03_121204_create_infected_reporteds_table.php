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
        Schema::create('infected_reporteds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infected_survivor_id');
            $table->foreignId('reporting_survivor_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('infected_survivor_id')->references('id')->on('survivors');
            $table->foreign('reporting_survivor_id')->references('id')->on('survivors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infected_reporteds');
    }
};
