<?php

namespace Demo\PickupDelivery\Model\ResourceModel\Point;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Demo\PickupDelivery\Model\Point::class,
            \Demo\PickupDelivery\Model\ResourceModel\Point::class
        );
    }
}
