<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survivor>
 */
class SurvivorInventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $survivorIds = Survivor::pluck('id')->toArray();
        $itemIds     = InventoryItem::pluck('id')->toArray();

        return [
            'survivor_id' => $this->faker->randomElement($survivorIds),
            'item_id'     => $this->faker->randomElement($itemIds),
            'quantity'    => $this->faker->numberBetween(1, 10),
        ];
    }
}
