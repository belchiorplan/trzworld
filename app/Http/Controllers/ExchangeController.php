<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Models\InventoryItem;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;

/**
 * @OA\Tag(
 *     name="Trade Items"
 * )
 */
class ExchangeController extends BaseController
{
    const AK47_ID = 4;

    /**
     * @OA\Post(
     *     path="/api/exchanges/trade",
     *     summary="Perform a trade between survivors",
     *     tags={"Trade Items"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"survivor1_id", "items_to_trade_s1", "survivor2_id", "items_to_trade_s2"},
     *                 @OA\Property(
     *                     property="survivor1_id",
     *                     type="integer",
     *                     description="ID of the first survivor involved in the trade"
     *                 ),
     *                 @OA\Property(
     *                     property="items_to_trade_s1",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         required={"item", "quantity"},
     *                         @OA\Property(
     *                             property="item",
     *                             type="integer",
     *                             description="ID of the item to be traded by survivor1"
     *                         ),
     *                         @OA\Property(
     *                             property="quantity",
     *                             type="integer",
     *                             description="Quantity of the item to be traded by survivor1"
     *                         )
     *                     ),
     *                     description="Items to be traded by survivor1"
     *                 ),
     *                 @OA\Property(
     *                     property="survivor2_id",
     *                     type="integer",
     *                     description="ID of the second survivor involved in the trade"
     *                 ),
     *                 @OA\Property(
     *                     property="items_to_trade_s2",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         required={"item", "quantity"},
     *                         @OA\Property(
     *                             property="item",
     *                             type="integer",
     *                             description="ID of the item to be traded by survivor2"
     *                         ),
     *                         @OA\Property(
     *                             property="quantity",
     *                             type="integer",
     *                             description="Quantity of the item to be traded by survivor2"
     *                         )
     *                     ),
     *                     description="Items to be traded by survivor2"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message indicating successful trade"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, invalid parameters provided",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating bad request"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating internal server error"
     *             )
     *         )
     *     )
     * )
     *
     * Perform the trade between survivors.
     *
     * @param  \App\Http\Requests\StoreExchangeRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function execTrade(StoreExchangeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Check if survivor is infected
            $isInfected = Survivor::whereIn('id', [$request->input('survivor1_id'), $request->input('survivor2_id')])
                                    ->where('is_infected', true)
                                    ->first();

            if ($isInfected) {
                $message = "Do you cannot trade items with survived infected.";

                return $this->sendError($message);
            }

            // Calculate the total points for items to be traded by survivor1
            $totalPointsSurvivor1 = $this->calculateTotalPoints($request->input('items_to_trade_s1'));

            // Calculate the total points for items to be traded by survivor2
            $totalPointsSurvivor2 = $this->calculateTotalPoints($request->input('items_to_trade_s2'));

            // Check if total points are equal for both survivors
            if ($totalPointsSurvivor1 !== $totalPointsSurvivor2) {
                $message = "Total points of items to be traded must be equal for both survivors.";

                return $this->sendError($message);
            }

            // Validate if all items exist in the inventory of both survivors
            $validateItemsExistenceS1 = $this->validateItemsExistence($request->input('survivor1_id'), $request->input('items_to_trade_s1'));
            $validateItemsExistenceS2 = $this->validateItemsExistence($request->input('survivor2_id'), $request->input('items_to_trade_s2'));

            if (!$validateItemsExistenceS1['status'] || !$validateItemsExistenceS2['status']) {
                return $this->sendError($validateItemsExistenceS1['message'] ?? $validateItemsExistenceS2['message']);
            }

            // Process the trade
            $this->processTrade(
                $request->input('survivor1_id'),
                $request->input('items_to_trade_s1'),
                $request->input('survivor2_id'),
                $request->input('items_to_trade_s2')
            );

            // Validates if the survivor is exchanging all their items for AK47
            $blockTradeAkS1 = $this->blockTradeAK($request->input('survivor1_id'));
            $blockTradeAkS2 = $this->blockTradeAK($request->input('survivor2_id'));

            if ($blockTradeAkS1 || $blockTradeAkS2) {
                DB::rollBack();

                $message = "One of the survivors is trying to keep all the weapons, your exchange will be cancelled!";
                return $this->sendError($message);
            }

            DB::commit();

            $message = "Trade completed successfully.";

            return $this->sendResponse($message);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error

            $message = $e->getMessage();

            return $this->sendError($message);
        }
    }

    /**
     * Validate if all items exist in the survivor's inventory.
     *
     * @param  int  $survivorId
     * @param  array  $items
     * @return array
     */
    private function validateItemsExistence(int $survivorId, array $items): array
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

    /**
     * Calculate the total points of items to be traded.
     *
     * @param  array  $items
     * @return int
     */
    private function calculateTotalPoints(array $items): int
    {
        $totalPoints = 0;
        foreach ($items as $item) {
            $inventoryItem = InventoryItem::findOrFail($item['item']);
            $totalPoints   += $inventoryItem->points * $item['quantity'];
        }
        return $totalPoints;
    }

    /**
     * Process the trade between survivors.
     *
     * @param  int  $survivor1Id
     * @param  array  $itemsToTradeSurvivor1
     * @param  int  $survivor2Id
     * @param  array  $itemsToTradeSurvivor2
     * @return void
     */
    private function processTrade(int $survivor1Id, array $itemsToTradeSurvivor1, int $survivor2Id, array $itemsToTradeSurvivor2): void
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
     * Validate if all items exist in the survivor's inventory.
     *
     * @param  int  $survivorId
     * @return bool
     */
    private function blockTradeAK(int $survivorId): bool
    {
        $inventarySurvivor = SurvivorInventory::where('survivor_id', $survivorId)->get();

        $sumTotalInventary = 0;
        foreach ($inventarySurvivor as $item) {
            if ($item->item_id != self::AK47_ID) {
                $sumTotalInventary += $item['quantity'];
            }
        }

        if ($sumTotalInventary == 0) {
            return true;
        }

        return false;
    }
}
