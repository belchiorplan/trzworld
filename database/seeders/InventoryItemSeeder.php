<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InventoryItem::create(['name' => 'Água de Fiji']);
        InventoryItem::create(['name' => 'sopa campbell']);
        InventoryItem::create(['name' => 'bolsa de primeiros socorros']);
        InventoryItem::create(['name' => 'AK47']);
    }
}
