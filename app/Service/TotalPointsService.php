<?php

namespace App\Service;

use App\Models\InventoryItem;
use App\Models\Survivor;
use App\Models\SurvivorInventory;

class TotalPointsService
{
    /**
     * Calculate the total points lost.
     *
     * @return int
     */
    public function calculateTotalPointsLost(): int
    {
        // Get IDs of infected survivors
        $infectedSurvivorsIds = Survivor::where('is_infected', true)->pluck('id');

        // If no infected survivors found, return 0
        if ($infectedSurvivorsIds->count() < 1) {
            return 0;
        }

        // Get items owned by infected survivors
        $survivorItems = SurvivorInventory::whereIn('survivor_id', $infectedSurvivorsIds)->get();

        // Get all items
        $items = InventoryItem::all();

        // Calculate total points
        return $survivorItems->map(function ($item) use ($items) {
            return $items->find($item->item_id)->points * $item->quantity;
        })->sum();
    }

    /**
     * Calculate the total points of items to be traded.
     *
     * @param  array  $items
     * @return int
     */
    public function calculateTotalPoints(array $items): int
    {
        $totalPoints = 0;
        foreach ($items as $item) {
            $inventoryItem = InventoryItem::findOrFail($item['item']);
            $totalPoints   += $inventoryItem->points * $item['quantity'];
        }
        return $totalPoints;
    }
}
