<?php

namespace Tests\Feature;

use App\Http\Controllers\SurvivorController;
use App\Models\InventoryItem;
use App\Models\Survivor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SurvivorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testStoreSurvivorAndInventorySuccessfully()
    {
        $items = InventoryItem::all()->pluck('id')->toArray();

        // Select random items
        $item1 = $items[array_rand($items)];
        $item2 = $items[array_rand(array_diff($items, [$item1]))];

        $survivorData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [
                [
                    'item'     => $item1,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
                [
                    'item'     => $item2,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
            ],
        ];

        $response = $this->postJson('/api/survivors/', $survivorData);

        $response->assertStatus(200); // Check for successful creation (200 Created)
        $response->assertJson(['data' => $survivorData]);
    }

    public function testStoreMethodWithoutName()
    {
        // Providing invalid latitude and longitude
        $invalidData = [
            'name'      => "",
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithInvalidInventoryItem()
    {
        $items = InventoryItem::all()->pluck('id')->toArray();

        // Select random items
        $item1 = $items[array_rand($items)];

        // Providing invalid gender_id
        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => 1, // An invalid gender id
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [
                [
                    'item'     => $item1,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
                [
                    'item'     => "",
                    'quantity' => fake()->numberBetween(1, 10),
                ],
            ],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithInvalidGenderId()
    {
        // Providing invalid gender_id
        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => 999, // An invalid gender id
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => []
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithInvalidItemId()
    {
        // Providing invalid gender_id
        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [
                [
                    'item'     => 5,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
                [
                    'item'     => 6,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
            ],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithInvalidLocation()
    {
        // Providing invalid latitude and longitude
        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => "Maranguape",
            'longitude' => "Brazil",
            'inventory' => [
                [
                    'item'     => 1,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
                [
                    'item'     => 2,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
            ],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithEmptyInventary()
    {
        // Providing invalid latitude and longitude
        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testStoreMethodWithoutData()
    {
        // Providing invalid latitude and longitude
        $invalidData = [
            'name'      => "",
            'age'       => "",
            'gender_id' => "",
            'latitude'  => "",
            'longitude' => "",
            'inventory' => [],
        ];

        $response = $this->postJson('/api/survivors/', $invalidData);

        $response->assertStatus(422);
    }

    public function testUpdateSurvivorSuccessfully()
    {
        $survivors = Survivor::where('is_infected', false)->pluck('id');

        $survivorData = [
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
        ];

        $response = $this->patchJson('/api/survivors/' . $survivors[array_rand($survivors->toArray())], $survivorData);

        $response->assertStatus(200);
    }

    public function testUpdateAllDataSuccessfully()
    {
        $survivors = Survivor::where('is_infected', false)->pluck('id');

        $invalidData = [
            'name'      => fake()->name,
            'age'       => fake()->numberBetween(18, 99),
            'gender_id' => fake()->numberBetween(1, 2),
            'latitude'  => fake()->latitude,
            'longitude' => fake()->longitude,
            'inventory' => [
                [
                    'item'     => 1,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
                [
                    'item'     => 2,
                    'quantity' => fake()->numberBetween(1, 10),
                ],
            ],
        ];

        $response = $this->patchJson('/api/survivors/' . $survivors[array_rand($survivors->toArray())], $invalidData);

        $response->assertStatus(200);
    }

    public function testUpdateMethodWithInvalidLocation()
    {
        $invalidData = [
            'latitude'  => "",
            'longitude' => fake()->longitude,
        ];

        $response = $this->patchJson('/api/survivors/' . fake()->numberBetween(1, 10), $invalidData);

        $response->assertStatus(422);
    }

    public function testInventoryReturnsSurvivorItems()
    {
        // Create a survivor with some items
        $survivorId = Survivor::where('is_infected', false)->first()->id;

        $response = $this->getJson("/api/survivors/{$survivorId}/inventory");
        $response->assertStatus(200);
    }

    public function testInventoryReturnsEmptyCollectionForNonExistentSurvivor()
    {
        $survivorId = Survivor::max('id') + 1;

        $response = $this->getJson("/api/survivors/{$survivorId}/inventory");
        $response->assertStatus(404);
    }
}
