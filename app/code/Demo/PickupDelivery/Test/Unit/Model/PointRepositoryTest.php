<?php

namespace Demo\PickupDelivery\Test\Unit\Model;

use Demo\PickupDelivery\Api\Data\PointInterface;
use Demo\PickupDelivery\Model\Data\Point as DataPoint;
use Demo\PickupDelivery\Model\Data\PointFactory as PointDataFactory;
use Demo\PickupDelivery\Model\Point;
use Demo\PickupDelivery\Model\PointFactory;
use Demo\PickupDelivery\Model\PointRepository;
use Demo\PickupDelivery\Model\ResourceModel\Point as PointResource;
use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointRepositoryTest extends TestCase
{
    private PointFactory|MockObject $pointFactory;
    private PointDataFactory|MockObject $pointDataFactory;
    private PointResource|MockObject $pointResource;
    private CollectionFactory|MockObject $collectionFactory;
    private SearchResultsInterfaceFactory|MockObject $searchResultsFactory;
    private CollectionProcessorInterface|MockObject $collectionProcessor;
    private PointRepository $repository;

    protected function setUp(): void
    {
        $this->pointFactory = $this->createMock(PointFactory::class);
        $this->pointDataFactory = $this->createMock(PointDataFactory::class);
        $this->pointResource = $this->createMock(PointResource::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->searchResultsFactory = $this->createMock(SearchResultsInterfaceFactory::class);
        $this->collectionProcessor = $this->createMock(CollectionProcessorInterface::class);

        $this->repository = new PointRepository(
            $this->pointFactory,
            $this->pointDataFactory,
            $this->pointResource,
            $this->collectionFactory,
            $this->searchResultsFactory,
            $this->collectionProcessor
        );
    }

    public function testGetByIdReturnsPoint(): void
    {
        $model = $this->createMock(Point::class);
        $model->method('getId')->willReturn(7);
        $dataPoint = new DataPoint(['entity_id' => 7]);

        $this->pointFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($model);

        $this->pointResource
            ->expects($this->once())
            ->method('load')
            ->with($model, 7);

        $this->pointDataFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($dataPoint);

        $result = $this->repository->getById(7);

        $this->assertSame($dataPoint, $result);
    }

    public function testGetByIdThrowsExceptionWhenPointDoesNotExist(): void
    {
        $model = $this->createMock(Point::class);
        $model->method('getId')->willReturn(null);

        $this->pointFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($model);

        $this->pointResource
            ->expects($this->once())
            ->method('load')
            ->with($model, 999);

        $this->expectException(NoSuchEntityException::class);

        $this->repository->getById(999);
    }

    public function testSaveReturnsSavedPoint(): void
    {
        $dataPoint = new DataPoint([
            'code' => 'kyiv-center',
            'name' => 'Central Point',
        ]);
        $model = $this->createMock(Point::class);
        $model->method('getId')->willReturn(null);
        $model->expects($this->once())
            ->method('addData')
            ->with($dataPoint->__toArray());
        $model->method('getData')->willReturn([
            'entity_id' => 8,
            'code' => 'kyiv-center',
            'name' => 'Central Point',
        ]);
        $savedDataPoint = new DataPoint(['entity_id' => 8]);

        $this->pointFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($model);

        $this->pointResource
            ->expects($this->once())
            ->method('save')
            ->with($model);

        $this->pointDataFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($savedDataPoint);

        $result = $this->repository->save($dataPoint);

        $this->assertInstanceOf(PointInterface::class, $result);
        $this->assertSame($savedDataPoint, $result);
    }
}
