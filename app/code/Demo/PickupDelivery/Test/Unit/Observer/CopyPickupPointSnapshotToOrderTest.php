<?php

namespace Demo\PickupDelivery\Test\Unit\Observer;

use Demo\PickupDelivery\Observer\CopyPickupPointSnapshotToOrder;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CopyPickupPointSnapshotToOrderTest extends TestCase
{
    public function testExecuteCopiesSnapshotFromQuoteToOrder(): void
    {
        $snapshot = [
            'pickup_point_id' => 7,
            'pickup_point_code' => 'kyiv-center',
            'pickup_point_name' => 'Central Point',
            'pickup_point_city' => 'Kyiv',
            'pickup_point_address' => '1 Main Street',
        ];

        /** @var Quote|MockObject $quote */
        $quote = $this->createMock(Quote::class);

        /** @var Order|MockObject $order */
        $order = $this->createMock(Order::class);
        $actual = [];

        $quote->expects($this->exactly(5))
            ->method('getData')
            ->willReturnCallback(
                static fn (string $key) => $snapshot[$key]
            );

        $order->expects($this->exactly(5))
            ->method('setData')
            ->willReturnCallback(
                static function (string $key, mixed $value) use (&$actual): void {
                    $actual[$key] = $value;
                }
            );

        $observer = new Observer([
            'quote' => $quote,
            'order' => $order,
        ]);

        (new CopyPickupPointSnapshotToOrder())->execute($observer);

        $this->assertSame($snapshot, $actual);
    }
}
