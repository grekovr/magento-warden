<?php

namespace Demo\PickupDelivery\Api;

use Demo\PickupDelivery\Api\Data\PointInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Pickup point repository interface.
 *
 * @api
 */
interface PointRepositoryInterface
{
    /**
     * Retrieve pickup point by ID.
     *
     * @param int $id
     * @return PointInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save pickup point.
     *
     * @param PointInterface $point
     * @return PointInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(PointInterface $point);

    /**
     * Delete pickup point.
     *
     * @param PointInterface $point
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(PointInterface $point);

    /**
     * Delete pickup point by ID.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Retrieve pickup points matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
