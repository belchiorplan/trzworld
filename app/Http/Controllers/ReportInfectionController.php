<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\StoreInfectedReportedRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\InfectedReported;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportInfectionController extends BaseController
{
    /**
     * Report a survivor infected
     */
    public function reportInfection(StoreInfectedReportedRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Save new report
            $infectedReported = new InfectedReported();
            $infectedReported->infected_survivor_id  = $request->input('infected_survivor_id');
            $infectedReported->reporting_survivor_id = $request->input('reporting_survivor_id');
            $infectedReported->save();

            $countedInfectedReports = InfectedReported::where('infected_survivor_id', $request->input('infected_survivor_id'))
                ->count();

            // Check if the survivor is now infected
            if ($countedInfectedReports >= 5) {
                $survivor = Survivor::find($request->input('infected_survivor_id'));

                if (!$survivor->is_infected) {
                    $updated = $survivor->update(['is_infected' => true]);

                    if ($updated) {
                        DB::commit();

                        $message = 'Hi, the survivor has been marked infected.';

                        return $this->sendResponse($message);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        $message = 'Hi, an error occurred while marking the user as infected';

        return $this->sendError($message);
    }
}
