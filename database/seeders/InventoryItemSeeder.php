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
        InventoryItem::create([
            'name' => 'Agua de Fiji',
            'points' => 14
        ]);
        InventoryItem::create([
            'name' => 'sopa campbell',
            'points' => 12
        ]);
        InventoryItem::create([
            'name' => 'bolsa de primeiros socorros',
            'points' => 10
        ]);
        InventoryItem::create([
            'name' => 'AK47',
            'points' => 8
        ]);
    }
}
