<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Survivors"
 * )
 */
class SurvivorController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/survivors/",
     *     summary="List survivors",
     *     description="List all registered survivors",
     *     tags={"Survivors"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/definitions/Survivor")
     *         )
     *     )
     * )
     *
     * Display a listing of the survivors.
     * @return Collection
     */
    public function index(): Collection
    {
        return Survivor::all();
    }

    /**
     * @OA\Post(
     *     path="/api/survivors/",
     *     summary="Create a new survivor",
     *     tags={"Survivors"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "age", "gender_id", "latitude", "longitude", "inventory"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the survivor"
     *                 ),
     *                 @OA\Property(
     *                     property="age",
     *                     type="integer",
     *                     description="Age of the survivor"
     *                 ),
     *                 @OA\Property(
     *                     property="gender_id",
     *                     type="integer",
     *                     description="Gender ID of the survivor"
     *                 ),
     *                 @OA\Property(
     *                     property="latitude",
     *                     type="number",
     *                     format="double",
     *                     description="Latitude of the survivor's location"
     *                 ),
     *                 @OA\Property(
     *                     property="longitude",
     *                     type="number",
     *                     format="double",
     *                     description="Longitude of the survivor's location"
     *                 ),
     *                 @OA\Property(
     *                     property="inventory",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         required={"item", "quantity"},
     *                         @OA\Property(
     *                             property="item",
     *                             type="integer",
     *                             description="ID of the item in the survivor's inventory"
     *                         ),
     *                         @OA\Property(
     *                             property="quantity",
     *                             type="integer",
     *                             description="Quantity of the item in the survivor's inventory"
     *                         )
     *                     ),
     *                     description="List of items in the survivor's inventory"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             description="Details of the created survivor",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="ID of the created survivor"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="age",
     *                 type="integer",
     *                 description="Age of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="gender_id",
     *                 type="integer",
     *                 description="Gender ID of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="latitude",
     *                 type="number",
     *                 format="double",
     *                 description="Latitude of the survivor's location"
     *             ),
     *             @OA\Property(
     *                 property="longitude",
     *                 type="number",
     *                 format="double",
     *                 description="Longitude of the survivor's location"
     *             ),
     *             @OA\Property(
     *                 property="inventory",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="item_id",
     *                         type="integer",
     *                         description="ID of the item in the survivor's inventory"
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         description="Quantity of the item in the survivor's inventory"
     *                     )
     *                 ),
     *                 description="List of items in the survivor's inventory"
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
     */
    public function store(StoreSurvivorRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $survivor = Survivor::create([
                'name'      => $request->input('name'),
                'age'       => $request->input('age'),
                'gender_id' => $request->input('gender_id'),
                'latitude'  => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            if ($survivor) {
                $inventory_items = $request->input('inventory');

                foreach ($inventory_items as $item) {
                    SurvivorInventory::create([
                        'survivor_id' => $survivor->id,
                        'item_id'     => $item['item'],
                        'quantity'    => $item['quantity'],
                    ]);
                }

                $data = $survivor->toArray();

                $data['inventory'] = $request->input('inventory');

                DB::commit();

                return $this->sendData($data);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            return $this->sendError($message);
        }

        $message = "Hello, an error occurred when trying to register the survivor.";

        return $this->sendError($message);
    }

    /**
     * @OA\Get(
     *     path="/api/survivors/{survivor}",
     *     summary="Get details of a specific survivor",
     *     tags={"Survivors"},
     *     @OA\Parameter(
     *         name="survivor",
     *         in="path",
     *         description="ID of the survivor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             description="Details of the survivor",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="ID of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="age",
     *                 type="integer",
     *                 description="Age of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="gender_id",
     *                 type="integer",
     *                 description="Gender ID of the survivor"
     *             ),
     *             @OA\Property(
     *                 property="latitude",
     *                 type="number",
     *                 format="double",
     *                 description="Latitude of the survivor's location"
     *             ),
     *             @OA\Property(
     *                 property="longitude",
     *                 type="number",
     *                 format="double",
     *                 description="Longitude of the survivor's location"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Survivor not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating survivor not found"
     *             )
     *         )
     *     )
     * )
     */
    public function show(Survivor $survivor): Survivor
    {
        return $survivor;
    }

    /**
     * @OA\Get(
     *     path="/api/survivors/inventory/{survivor}",
     *     summary="Get details inventory of a specific survivor",
     *     tags={"Survivors"},
     *     @OA\Parameter(
     *         name="survivor",
     *         in="path",
     *         description="ID of the survivor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             description="Inventory of the survivor",
     *             @OA\Property(
     *                  property="survivor_id",
     *                  type="integer",
     *                  description="ID of the survivor"
     *              ),
     *             @OA\Property(
     *                 property="item_id",
     *                 type="integer",
     *                 description="Item"
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="string",
     *                 description="Quantity of the item"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Survivor not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message indicating survivor not found"
     *             )
     *         )
     *     )
     * )
     */
    public function inventory(Survivor $survivor): Collection
    {
        return SurvivorInventory::where('survivor_id', $survivor->id)->get();
    }

    /**
     * @OA\Put(
     *     path="/api/survivors/{survivor}",
     *     summary="Update the location of a survivor",
     *     tags={"Survivors"},
     *     @OA\Parameter(
     *         name="survivor",
     *         in="path",
     *         description="ID of the survivor to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="latitude",
     *                     type="number",
     *                     format="double",
     *                     description="Latitude of the survivor's new location"
     *                 ),
     *                 @OA\Property(
     *                     property="longitude",
     *                     type="number",
     *                     format="double",
     *                     description="Longitude of the survivor's new location"
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
     *                 description="Message indicating successful update"
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
     * Update the specified survivor in the database.
     *
     * @param  UpdateSurvivorRequest $request
     * @param  Survivor $survivor
     * @return JsonResponse
     */
    public function update(UpdateSurvivorRequest $request, Survivor $survivor): JsonResponse
    {
        try {
            DB::beginTransaction();

            $update = $survivor->update([
                'latitude'  => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            if ($update) {
                DB::commit();
                $message = "Hello {$survivor->name}, you have been updated your location.";
                return $this->sendResponse($message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        $message = "Hello, an error occurred when trying to update location the survivor.";

        return $this->sendError($message);
    }

}
