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
        Schema::create('survivors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->tinyInteger('gender_id');
            $table->double('latitude');
            $table->double('longitude');
            $table->boolean('is_infected')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survivors');
    }
};
