<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\InfectedReported;
use App\Models\InventoryItem;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

/**
 * @OA\Tag(
 *     name="Reports"
 * )
 */
class ReportsController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/reports/percentage-infected",
     *     summary="Display the percentage of not infected survivors",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="percentage",
     *                 type="number",
     *                 format="double",
     *                 description="Percentage of not infected survivors"
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
     * Display the percentage of not infected
     *
     * @return JsonResponse
     */
    public function percentageInfected(): JsonResponse
    {
        return $this->calculatePercentage(true);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/percentage-not-infected",
     *     summary="Display the percentage of not infected survivors",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="percentage",
     *                 type="number",
     *                 format="double",
     *                 description="Percentage of not infected survivors"
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
     * Display the percentage of not infected
     *
     * @return JsonResponse
     */
    public function percentageNotInfected(): JsonResponse
    {
        return $this->calculatePercentage(false);
    }

    /**
     * Calculate percentage of survivors are and not infected
     *
     * @param  bool $infected
     * @return JsonResponse
     */
    public function calculatePercentage(bool $infected): JsonResponse
    {
        $survivors = Survivor::all();
        $count     = $survivors->count();
        $filter    = $survivors->where('is_infected', $infected)->count();

        if ($count > 0) {
            $percentage = ($filter / $count) * 100;
            $status = $infected ? "infected" : "not infected";

            $message = "The percentage of {$status} survivors is: {$percentage}%";

            return $this->sendResponse($message);
        }

        $status = $infected ? "infected" : "not infected";
        $message = "There are no {$status} survivors.";

        return $this->sendResponse($message);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/average-items",
     *     summary="Calculate average quantity of items per survivor",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="average_items",
     *                 type="object",
     *                 description="Average quantity of items per survivor",
     *                 @OA\AdditionalProperties(
     *                     type="number",
     *                     format="double",
     *                     description="Average quantity of the item per survivor"
     *                 )
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
     * Calculate average of items per survivor
     *
     * @return array
     */
    public function calculateAverageItemsQuantity(): array
    {
        // Get all items
        $items = InventoryItem::all();

        // Get only survivors not infected
        $survivors = Survivor::where('is_infected', false)->count();

        $averages = [];

        // Calculate average quantity for each survivor
        foreach ($items as $item) {
            $totalItems = SurvivorInventory::where('item_id', $item->id)->pluck('quantity')->sum();
            $averages[$item->name] = number_format($totalItems / $survivors, 0);
        }

        return $averages;
    }

    /**
     * @OA\Get(
     *     path="/api/reports/total-points-lost",
     *     summary="Calculate total points lost due to infected survivors",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message indicating total points lost"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No infected survivors found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating no infected survivors found"
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
     * Calculate total points lost
     *
     * @return JsonResponse
     */
    public function calculateTotalPointsLost(): JsonResponse
    {
        // Get IDs of infected survivors
        $infectedSurvivorsIds = Survivor::where('is_infected', true)->pluck('id');

        if ($infectedSurvivorsIds->count() < 1) {
            $message = "We didn't lose any points.";
            return $this->sendResponse($message);
        }

        // Get items owned by infected survivors
        $survivorItems = SurvivorInventory::whereIn('survivor_id', $infectedSurvivorsIds)->get();

        // Get all items
        $items = InventoryItem::all();

        // Calculate total points
        $totalPoints = $survivorItems->map(function ($item) use ($items) {
            $point = $items->find($item->item_id)->points * $item->quantity;
            return $point;
        })->sum();

        $message = "The quantity points lost is: {$totalPoints} points";

        return $this->sendResponse($message);
    }

}
