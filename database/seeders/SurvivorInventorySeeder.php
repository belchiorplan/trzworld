<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Database\Seeder;

class SurvivorInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $survivors      = Survivor::all();
        $inventoryItems = InventoryItem::all();

        foreach ($survivors as $survivor) {
            // Decides whether the Survivor will have all inventory items
            $hasAllItems = rand(0, 1) === 1;

            if ($hasAllItems) {
                foreach ($inventoryItems as $inventoryItem) {
                    SurvivorInventory::create([
                        'survivor_id' => $survivor->id,
                        'item_id'     => $inventoryItem->id,
                        'quantity'    => rand(1, 10),
                    ]);
                }
            } else {
                // Select some random items for Survivor
                $selectedItems = $inventoryItems->random(rand(1, $inventoryItems->count()));

                foreach ($selectedItems as $inventoryItem) {
                    SurvivorInventory::create([
                        'survivor_id' => $survivor->id,
                        'item_id'     => $inventoryItem->id,
                        'quantity'    => rand(1, 10),
                    ]);
                }
            }
        }
    }
}
