<?php

namespace Database\Seeders;

use App\Models\SurvivorInventory;
use Illuminate\Database\Seeder;

class SurvivorInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SurvivorInventory::factory(783)->create();
    }
}
