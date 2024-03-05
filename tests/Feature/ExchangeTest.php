<?php


use App\Models\Survivor;
use App\Models\SurvivorInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testItemExchangeSuccessful()
    {
        // Get survivors
        $survivors = Survivor::where('is_infected', false)->pluck('id');

        $survivor1 = $survivors[array_rand($survivors->toArray())];
        $survivor2 = $survivors[array_rand(array_diff($survivors->toArray(), [$survivor1]))];

        $itemsSurvivor1 = [
            ['item' => 1, 'quantity' => 1],
            ['item' => 2, 'quantity' => 1],
        ];
        $itemsSurvivor2 = [
            ['item' => 3, 'quantity' => 1],
            ['item' => 4, 'quantity' => 2],
        ];

        foreach ($itemsSurvivor1 as $item) {
            $survivorInventary = SurvivorInventory::where('survivor_id', $survivor1)->where('item_id', $item['item'])->first();

            if (!$survivorInventary) {
                SurvivorInventory::create([
                    'survivor_id' => $survivor1,
                    'item_id'     => $item['item'],
                    'quantity'    => 5,
                ]);
            } else {
                $survivorInventary->quantity = 5;
                $survivorInventary->save();
            }
        }

        foreach ($itemsSurvivor2 as $item) {
            $survivorInventary = SurvivorInventory::where('survivor_id', $survivor2)->where('item_id', $item['item'])->first();

            if (!$survivorInventary) {
                SurvivorInventory::create([
                    'survivor_id' => $survivor2,
                    'item_id'     => $item['item'],
                    'quantity'    => 5,
                ]);
            } else {
                $survivorInventary->quantity = 5;
                $survivorInventary->save();
            }
        }

        $response = $this->postJson('/api/exchanges/trade', [
            'survivor1_id'      => $survivor1,
            'survivor2_id'      => $survivor2,
            'items_to_trade_s1' => $itemsSurvivor1,
            'items_to_trade_s2' => $itemsSurvivor2,
        ]);

        $response->assertStatus(200);
    }

    public function testExchangeWithInfectedSurvivor()
    {
        $survivorsNotInfected = Survivor::where('is_infected', false)->pluck('id');
        $survivorsInfected    = Survivor::where('is_infected', true)->pluck('id');

        $survivor1 = $survivorsNotInfected[array_rand($survivorsNotInfected->toArray())];
        $survivor2 = $survivorsInfected[array_rand($survivorsInfected->toArray())];

        $itemsSurvivor1 = [
            ['item' => 1, 'quantity' => 1],
        ];
        $itemsSurvivor2 = [
            ['item' => 2, 'quantity' => 1],
        ];

        $response = $this->postJson('/api/exchanges/trade', [
            'survivor1_id'      => $survivor1,
            'survivor2_id'      => $survivor2,
            'items_to_trade_s1' => $itemsSurvivor1,
            'items_to_trade_s2' => $itemsSurvivor2,
        ]);

        $response->assertStatus(422);
    }

    public function testExchangeWithUnequalPoints()
    {
        $survivors = Survivor::where('is_infected', false)->pluck('id');

        $survivor1 = $survivors[array_rand($survivors->toArray())];
        $survivor2 = $survivors[array_rand(array_diff($survivors->toArray(), [$survivor1]))];

        $itemsSurvivor1 = [
            ['item' => 1, 'quantity' => 1], // Low point item
        ];
        $itemsSurvivor2 = [
            ['item' => 2, 'quantity' => 2], // High point item
        ];

        $response = $this->postJson('/api/exchanges/trade', [
            'survivor1_id'      => $survivor1,
            'survivor2_id'      => $survivor2,
            'items_to_trade_s1' => $itemsSurvivor1,
            'items_to_trade_s2' => $itemsSurvivor2,
        ]);

        $response->assertStatus(422);
    }
}
