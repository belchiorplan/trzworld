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
        Schema::create('survivor_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survivor_id');
            $table->foreignId('item_id');
            $table->timestamps();

            $table->foreign('survivor_id')->references('id')->on('survivors');
            $table->foreign('item_id')->references('id')->on('inventory_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survivor_inventories');
    }
};
