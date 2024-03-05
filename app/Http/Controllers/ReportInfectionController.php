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
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Report Infection"
 * )
 */
class ReportInfectionController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/report-infections/report",
     *     summary="Report a survivor as infected",
     *     tags={"Report Infection"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"infected_survivor_id", "reporting_survivor_id"},
     *                 @OA\Property(
     *                     property="infected_survivor_id",
     *                     type="integer",
     *                     description="ID of the survivor reported as infected"
     *                 ),
     *                 @OA\Property(
     *                     property="reporting_survivor_id",
     *                     type="integer",
     *                     description="ID of the survivor reporting the infection"
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
     *                 description="Message indicating successful report of infection"
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
     * Report a survivor infected
     *
     * @param  StoreInfectedReportedRequest $request
     * @return JsonResponse
     * @throws  \Exception
     */
    public function reportInfection(StoreInfectedReportedRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Save new report
            InfectedReported::create([
                'infected_survivor_id'  => $request->input('infected_survivor_id'),
                'reporting_survivor_id' => $request->input('reporting_survivor_id')
            ]);

            $countedInfectedReports = InfectedReported::where('infected_survivor_id', $request->input('infected_survivor_id'))
                                                        ->count();

            // Check if the survivor is now infected
            if ($countedInfectedReports >= 5) {
                $survivor = Survivor::find($request->input('infected_survivor_id'));

                if (!$survivor->is_infected) {
                    $survivor->update(['is_infected' => true]);
                }
            }

            DB::commit();

            $message = 'Hi, the survivor has been marked infected.';

            return $this->sendResponse($message);

        } catch (\Exception $e) {
            DB::rollBack();
        }

        $message = 'Hi, an error occurred while marking the user as infected';

        return $this->sendError($message);
    }
}
