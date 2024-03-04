<?php

namespace Database\Seeders;

use App\Models\Survivor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GenderSeeder::class,
            SurvivorSeeder::class,
        ]);
    }
}
