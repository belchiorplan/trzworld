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

class ReportsController extends BaseController
{
    /**
     * Display the percentage of not infected
     *
     * @return JsonResponse
     */
    public function percentageInfected(): JsonResponse
    {
        return $this->calculatePercentage(true);
    }

    /**
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
