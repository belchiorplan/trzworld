<?php

namespace App\Service;

use App\Models\SurvivorInventory;

class ExchangeService
{
    const AK47_ID = 4;

    public function processTrade(int $survivor1Id, array $itemsToTradeSurvivor1, int $survivor2Id, array $itemsToTradeSurvivor2): void
    {
        // Remove items from survivor1 and add them to survivor2
        $this->transferItems($survivor1Id, $itemsToTradeSurvivor1, $survivor2Id);

        // Remove items from survivor2 and add them to survivor1
        $this->transferItems($survivor2Id, $itemsToTradeSurvivor2, $survivor1Id);
    }

    /**
     * Transfer items between survivors.
     *
     * @param  int  $sourceSurvivorId
     * @param  array  $itemsToTransfer
     * @param  int  $destinationSurvivorId
     * @return void
     */
    private function transferItems(int $sourceSurvivorId, array $itemsToTransfer, int $destinationSurvivorId): void
    {
        foreach ($itemsToTransfer as $item) {
            // Remove items from source survivor
            SurvivorInventory::where('survivor_id', $sourceSurvivorId)
                                ->where('item_id', $item['item'])
                                ->decrement('quantity', $item['quantity']);

            // Add items to destination survivor
            $destinationInventoryItem = SurvivorInventory::where('survivor_id', $destinationSurvivorId)
                                                        ->where('item_id', $item['item'])
                                                        ->first();

            if ($destinationInventoryItem) {
                $destinationInventoryItem->increment('quantity', $item['quantity']);
            } else {
                SurvivorInventory::create([
                    'survivor_id' => $destinationSurvivorId,
                    'item_id'     => $item['item'],
                    'quantity'    => $item['quantity']
                ]);
            }
        }
    }

    /**
     * Block trade AK47
     *
     * @param  array  $inventarySurvivor
     * @return bool
     */
    public function blockTradeAK(array $inventarySurvivor): bool
    {
        $sumTotalInventary = 0;
        foreach ($inventarySurvivor as $item) {
            if ($item['item_id'] != self::AK47_ID) {
                $sumTotalInventary += $item['quantity'];
            }
        }

        if ($sumTotalInventary == 0) {
            return true;
        }

        return false;
    }
}
