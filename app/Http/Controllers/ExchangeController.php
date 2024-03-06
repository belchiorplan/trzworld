<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Models\InventoryItem;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use App\Service\ExchangeService;
use App\Service\SurvivorService;
use App\Service\TotalPointsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use function Laravel\Prompts\select;

/**
 * @OA\Tag(
 *     name="Trade Items"
 * )
 */
class ExchangeController extends BaseController
{
    private TotalPointsService $totalPointsService;
    private ExchangeService $tradeService;
    private SurvivorService $survivorService;

    public function __construct(
        TotalPointsService $totalPointsService,
        ExchangeService    $tradeService,
        SurvivorService    $survivorService
    ) {
        $this->totalPointsService = $totalPointsService;
        $this->tradeService       = $tradeService;
        $this->survivorService    = $survivorService;
    }

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
     *         response=422,
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
            $isInfectedSurvivor1 = Survivor::find($request->input('survivor1_id'));
            $isInfectedSurvivor2 = Survivor::find($request->input('survivor2_id'));

            if ($isInfectedSurvivor1->is_infected || $isInfectedSurvivor2->is_infected) {
                $message = "Do you cannot trade items with survived infected.";
                return $this->sendError($message);
            }

            // Calculate the total points for items to be traded by survivor
            $totalPointsSurvivor1 = $this->totalPointsService->calculateTotalPoints($request->input('items_to_trade_s1'));
            $totalPointsSurvivor2 = $this->totalPointsService->calculateTotalPoints($request->input('items_to_trade_s2'));

            // Check if total points are equal for both survivors
            if ($totalPointsSurvivor1 !== $totalPointsSurvivor2) {
                DB::rollBack();

                $message = "Total points of items to be traded must be equal for both survivors.";

                return $this->sendError($message);
            }

            // Validate if all items exist in the inventory of both survivors
            $validateItemsExistenceS1 = $this->survivorService->validateItemsExistence($request->input('survivor1_id'), $request->input('items_to_trade_s1'));
            $validateItemsExistenceS2 = $this->survivorService->validateItemsExistence($request->input('survivor2_id'), $request->input('items_to_trade_s2'));

            if (!$validateItemsExistenceS1['status'] || !$validateItemsExistenceS2['status']) {
                return $this->sendError($validateItemsExistenceS1['message'] ?? $validateItemsExistenceS2['message']);
            }

            // Process the trade
            $this->tradeService->processTrade(
                $request->input('survivor1_id'),
                $request->input('items_to_trade_s1'),
                $request->input('survivor2_id'),
                $request->input('items_to_trade_s2')
            );

            $inventarySurvivor1 = SurvivorInventory::where('survivor_id', $request->input('survivor1_id'))->get();
            $inventarySurvivor2 = SurvivorInventory::where('survivor_id', $request->input('survivor2_id'))->get();

            // Validates if the survivor is exchanging all their items for AK47
            $blockTradeAkS1 = $this->tradeService->blockTradeAK($inventarySurvivor1->toArray());
            $blockTradeAkS2 = $this->tradeService->blockTradeAK($inventarySurvivor2->toArray());

            if ($blockTradeAkS1 || $blockTradeAkS2) {
                DB::rollBack();

                $message = "One of the survivors is trying to keep all the weapons, your exchange will be cancelled!";
                return $this->sendError($message);
            }

            DB::rollBack();

            $message = "Trade completed successfully.";

            return $this->sendResponse($message);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error

            $message = $e->getMessage();

            return $this->sendError($message);
        }
    }


}
