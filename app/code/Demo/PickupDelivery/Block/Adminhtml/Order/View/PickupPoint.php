<?php

namespace Demo\PickupDelivery\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;

class PickupPoint extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getOrder(): ?Order
    {
        $order = $this->registry->registry('current_order');

        return $order instanceof Order ? $order : null;
    }

    public function hasPickupPoint(): bool
    {
        return (bool) $this->getOrder()?->getData('pickup_point_id');
    }
}
