<?php

namespace Tests\Unit;

use App\Models\SurvivorInventory;
use App\Service\ExchangeService;
use PHPUnit\Framework\TestCase;

class ExchangeServiceTest extends TestCase
{
    public function itBlocksTradeWhenInventoryHasNoItemsOtherThanAk47()
    {
        // Arrange
        $inventory = [
            ['item_id' => 4, 'quantity' => 1], // AK47 item
            ['item_id' => 1, 'quantity' => 0], // Some other item with no quantity
            ['item_id' => 2, 'quantity' => 0], // Another item with no quantity
        ];

        $tradeService = new ExchangeService();

        // Act
        $result = $tradeService->blockTradeAK($inventory);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function itDoesNotBlockTradeWhenInventoryHasOtherItemsThanAk47()
    {
        // Arrange
        $inventory = [
            ['item_id' => 1, 'quantity' => 1], // Some item with quantity
            ['item_id' => 2, 'quantity' => 0], // Another item with no quantity
        ];

        $tradeService = new ExchangeService();

        // Act
        $result = $tradeService->blockTradeAK($inventory);

        // Assert
        $this->assertFalse($result);
    }
}
