<?php

namespace Demo\PickupDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Point extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'demo_pickupdelivery_point',
            'entity_id'
        );
    }
}
