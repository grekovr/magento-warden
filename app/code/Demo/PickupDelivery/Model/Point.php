<?php

namespace Demo\PickupDelivery\Model;

use Magento\Framework\Model\AbstractModel;

class Point extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Demo\PickupDelivery\Model\ResourceModel\Point::class);
    }
}
