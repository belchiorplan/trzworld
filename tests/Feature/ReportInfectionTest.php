<?php

namespace Tests\Feature;

use App\Models\Survivor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportInfectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testStoreReportInfectionSuccessfully()
    {
        $survivors = Survivor::all()->pluck('id');

        $reportData = [
            'infected_survivor_id'  => $survivors[array_rand($survivors->toArray())],
            'reporting_survivor_id' => $survivors[array_rand($survivors->toArray())],
        ];

        $response = $this->postJson('/api/report-infections/report', $reportData);

        $response->assertStatus(200);
    }

    public function testStoreReportInfectionWithInvalidSurvivorId()
    {
        $survivors = Survivor::all()->pluck('id')->toArray();

        // Assuming that the largest ID in the collection is the last one
        $largestId = end($survivors);

        // Providing invalid reporting_survivor_id
        $invalidData = [
            'infected_survivor_id'  => $largestId + 1, // Assuming the largest ID is 10, add 1 to make it invalid
            'reporting_survivor_id' => $largestId + 2, // Add 2 to ensure a different invalid ID
        ];

        $response = $this->postJson('/api/report-infections/report', $invalidData);

        $response->assertStatus(422);
    }
}
