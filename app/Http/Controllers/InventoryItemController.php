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

class InventoryItemController extends BaseController
{
    /**
     * Display a listing of the items.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return InventoryItem::all();
    }
}
