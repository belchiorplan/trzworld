<?php

namespace App\Service;

use App\Models\InventoryItem;
use App\Models\Survivor;
use Illuminate\Support\Facades\DB;

class SurvivorService
{
    /**
     * Validate if all items exist in the survivor's inventory.
     *
     * @param  int  $survivorId
     * @param  array  $items
     * @return array
     */
    public function validateItemsExistence(int $survivorId, array $items): array
    {
        $survivor = Survivor::find($survivorId)->name;

        foreach ($items as $item) {
            $nameItem = InventoryItem::find($item['item'])->name;

            $inventoryItem = Survivor::findOrFail($survivorId)
                ->inventoryItems()
                ->where('item_id', $item['item'])
                ->first();

            if (!$inventoryItem) {
                DB::rollBack();
                $message = "Item {$nameItem} does not exist in survivor {$survivor}'s inventory";
                return ['status' => false, 'message' => $message];
            }

            if (($inventoryItem->quantity - $item['quantity']) < 0) {
                DB::rollBack();
                $message = "{$survivor}'s item {$nameItem} cannot be exchanged, as it does not have sufficient quantity.";
                return ['status' => false, 'message' => $message];
            }
        }

        return ['status' => true];
    }
}
