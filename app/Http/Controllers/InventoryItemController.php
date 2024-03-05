<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\StoreInfectedReportedRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\InfectedReported;
use App\Models\InventoryItem;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Inventory Items"
 * )
 */
class InventoryItemController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/inventory-items/all",
     *     summary="Display a listing of the items",
     *     tags={"Inventory Items"},
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              description="Details of the item",
     *              @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  description="ID of the item"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Name of the item"
     *              ),
     *              @OA\Property(
     *                  property="points",
     *                  type="integer",
     *                  description="Poinst of the item"
     *              )
     *          )
     *      ),
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
     * Display a listing of the items.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return InventoryItem::all();
    }
}
