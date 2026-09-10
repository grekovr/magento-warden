<?php

namespace Demo\PickupDelivery\Test\Unit\Model\Data;

use Demo\PickupDelivery\Model\Data\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testSettersReturnTheSameObject(): void
    {
        $point = new Point();

        $this->assertSame($point, $point->setId(10));
        $this->assertSame($point, $point->setCode('kyiv-center'));
        $this->assertSame($point, $point->setName('Central Point'));
        $this->assertSame($point, $point->setCity('Kyiv'));
        $this->assertSame($point, $point->setAddress('Test address'));
        $this->assertSame($point, $point->setIsActive(true));
        $this->assertSame($point, $point->setSortOrder(5));
    }

    public function testGettersReturnStoredValues(): void
    {
        $point = new Point([
            'entity_id' => 10,
            'code' => 'kyiv-center',
            'name' => 'Central Point',
            'city' => 'Kyiv',
            'address' => 'Test address',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $this->assertSame(10, $point->getId());
        $this->assertSame('kyiv-center', $point->getCode());
        $this->assertSame('Central Point', $point->getName());
        $this->assertSame('Kyiv', $point->getCity());
        $this->assertSame('Test address', $point->getAddress());
        $this->assertTrue($point->getIsActive());
        $this->assertSame(5, $point->getSortOrder());
    }
}
