<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\InfectedReported;
use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SurvivorController extends BaseController
{
    /**
     * Display a listing of the survivors.
     */
    public function index(): Collection
    {
        return Survivor::all();
    }

    /**
     * Store a newly created survivor in the database.
     */
    public function store(StoreSurvivorRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $survivor = new Survivor();

            $survivor->name      = $request->input('name');
            $survivor->age       = $request->input('age');
            $survivor->gender_id = $request->input('gender_id');
            $survivor->latitude  = $request->input('latitude');
            $survivor->longitude = $request->input('longitude');

            if ($survivor->save()) {

                $inventory_items = $request->input('inventory');

                foreach ($inventory_items as $item) {
                    SurvivorInventory::create([
                        'survivor_id' => $survivor->id,
                        'item_id'     => $item,
                    ]);
                }

                DB::commit();
                $message = 'Hello ' . $survivor->name . ', you have been registered. Your code is ' . $survivor->id;
                return $this->sendResponse($message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        $message = 'Hello, an error occurred when trying to register the survivor.';

        return $this->sendError($message);
    }

    /**
     * Display the specified survivor.
     */
    public function show(Survivor $survivor): Survivor
    {
        return $survivor;
    }

    /**
     * Update the specified survivor in the database.
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
                $message = 'Hello ' . $survivor->name . ', you have been updated your location.';
                return $this->sendResponse($message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        $message = 'Hello, an error occurred when trying to update location the survivor.';

        return $this->sendError($message);
    }

}
