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
        $response = $this->getJson('/api/reports/percentage-infected');
        $response->assertStatus(200);
    }

    public function testReportsPercentageNotInfectionSuccessfully()
    {
        $response = $this->getJson('/api/reports/percentage-not-infected');
        $response->assertStatus(200);
    }

    public function testReportsAverageItemsSuccessfully()
    {
        $response = $this->getJson('/api/reports/average-items');
        $response->assertStatus(200);
    }

    public function testReportsTotalPointsLostSuccessfully()
    {
        $response = $this->getJson('/api/reports/total-poinsts-lost');
        $response->assertStatus(200);
    }
}
