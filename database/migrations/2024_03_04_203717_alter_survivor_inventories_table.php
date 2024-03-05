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
        Schema::table('survivor_inventories', function (Blueprint $table) {
            $table->unique(['survivor_id', 'item_id'], 'survivor_inventories_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survivor_inventories', function (Blueprint $table) {
            $table->dropUnique('survivor_inventories_unique');
        });
    }
};
