<?php

namespace Demo\PickupDelivery\Api\Data;

/**
 * Pickup point data.
 *
 * @api
 */
interface PointInterface
{
    public const ENTITY_ID = 'entity_id';
    public const CODE = 'code';
    public const NAME = 'name';
    public const CITY = 'city';
    public const ADDRESS = 'address';
    public const IS_ACTIVE = 'is_active';
    public const SORT_ORDER = 'sort_order';

    /**
     * Get point ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set point ID.
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get point code.
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Set point code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get point name.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set point name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Set city.
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address.
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * Get active status.
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Set active status.
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get sort order.
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set sort order.
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);
}
