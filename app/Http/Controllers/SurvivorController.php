<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

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
        $survivor = new Survivor();

        $survivor->name      = $request->input('name');
        $survivor->age       = $request->input('age');
        $survivor->gender_id = $request->input('gender_id');
        $survivor->latitude  = $request->input('latitude');
        $survivor->longitude = $request->input('longitude');

        $survivor->save();

        $message = 'Hello ' . $survivor->name . ', you have been registered. Your code is ' . $survivor->id;

        return $this->sendResponse($message);
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
        $survivor->update([
            'name'          => $request->input('name'),
            'age'           => $request->input('age'),
            'gender_id'     => $request->input('gender_id'),
            'last_location' => $request->input('last_location'),
        ]);

        $message = 'Hello ' . $survivor->name . ', you have been updated.';

        return $this->sendResponse($message);
    }

    /**
     * Remove the specified survivor from the database.
     */
    public function destroy(Survivor $survivor): JsonResponse
    {
        if ($survivor->delete()) {
            $message = 'Hello, the survivor ' . $survivor->name . ' has been deleted.';
            return $this->sendResponse($message);
        }

        $message = 'Hello, the survivor ' . $survivor->name . ' cannot be deleted.';

        return $this->sendError($message, 406);
    }

}
