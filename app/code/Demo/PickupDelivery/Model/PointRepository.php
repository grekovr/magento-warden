<?php

namespace Demo\PickupDelivery\Model;

use Demo\PickupDelivery\Api\Data\PointInterface;
use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Demo\PickupDelivery\Model\Data\PointFactory as PointDataFactory;
use Demo\PickupDelivery\Model\ResourceModel\Point as PointResource;
use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Pickup point repository.
 */
class PointRepository implements PointRepositoryInterface
{
    public function __construct(
        private readonly PointFactory $pointFactory,
        private readonly PointDataFactory $pointDataFactory,
        private readonly PointResource $pointResource,
        private readonly CollectionFactory $collectionFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $model = $this->pointFactory->create();
        $this->pointResource->load($model, $id);

        if (!$model->getId()) {
            throw new NoSuchEntityException(
                __('Pickup point with ID "%1" does not exist.', $id)
            );
        }

        return $this->toDataModel($model);
    }

    /**
     * @inheritdoc
     */
    public function save(PointInterface $point)
    {
        $model = $this->pointFactory->create();

        if ($point->getId()) {
            $this->pointResource->load($model, $point->getId());

            if (!$model->getId()) {
                throw new NoSuchEntityException(
                    __('Pickup point with ID "%1" does not exist.', $point->getId())
                );
            }
        }

        $model->addData($point->__toArray());

        try {
            $this->pointResource->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save pickup point.'),
                $exception
            );
        }

        return $this->toDataModel($model);
    }

    /**
     * @inheritdoc
     */
    public function delete(PointInterface $point)
    {
        $model = $this->pointFactory->create();
        $model->setId($point->getId());

        try {
            $this->pointResource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete pickup point.'),
                $exception
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $point = $this->getById($id);

        return $this->delete($point);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];

        foreach ($collection as $model) {
            $items[] = $this->toDataModel($model);
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    private function toDataModel(Point $model): PointInterface
    {
        return $this->pointDataFactory->create([
            'data' => $model->getData()
        ]);
    }
}
