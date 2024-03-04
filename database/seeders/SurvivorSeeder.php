<?php

namespace Database\Seeders;

use App\Models\Survivor;
use Illuminate\Database\Seeder;

class SurvivorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Survivor::factory(10)->create();
    }
}
