<?php

namespace Demo\PickupDelivery\Ui\DataProvider\Point;

use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        CollectionFactory $collectionFactory,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->collection = $collectionFactory->create();
    }

    public function getData(): array
    {
        if ($this->loadedData) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $point) {
            $this->loadedData[(int) $point->getId()] = $point->getData();
        }

        return $this->loadedData;
    }
}
