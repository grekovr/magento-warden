<?php

namespace Demo\PickupDelivery\Test\Unit\Ui\DataProvider\Point;

use Demo\PickupDelivery\Model\Point;
use Demo\PickupDelivery\Model\ResourceModel\Point\Collection;
use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Demo\PickupDelivery\Ui\DataProvider\Point\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    public function testGetDataReturnsCollectionDataIndexedById(): void
    {
        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collection = $this->createMock(Collection::class);
        $point = $this->createMock(Point::class);
        $point->method('getId')->willReturn(3);
        $point->method('getData')->willReturn(['entity_id' => 3, 'code' => 'center']);
        $collection->expects($this->once())->method('getItems')->willReturn([$point]);
        $collectionFactory->expects($this->once())->method('create')->willReturn($collection);

        $provider = new DataProvider(
            $collectionFactory,
            'form',
            'entity_id',
            'id'
        );

        $this->assertSame(
            [3 => ['entity_id' => 3, 'code' => 'center']],
            $provider->getData()
        );
        $this->assertSame(
            [3 => ['entity_id' => 3, 'code' => 'center']],
            $provider->getData()
        );
    }
}
