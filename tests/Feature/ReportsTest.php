<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testReportsPercentageInfectionSuccessfully()
    {
        $response = $this->getJson('/api/reports/percentage-infection?infected_or_not=' . urlencode('true'));
        $response->assertStatus(200);
    }

    public function testReportsPercentageNotInfectionSuccessfully()
    {
        $response = $this->getJson('/api/reports/percentage-infection?infected_or_not=' . urlencode('false'));
        $response->assertStatus(200);
    }

    public function testReportsAverageItemsSuccessfully()
    {
        $response = $this->getJson('/api/reports/average-items');
        $response->assertStatus(200);
    }

    public function testReportsTotalPointsLostSuccessfully()
    {
        $response = $this->getJson('/api/reports/total-points-lost');
        $response->assertStatus(200);
    }
}
