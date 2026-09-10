<?php

namespace Demo\PickupDelivery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class CopyPickupPointSnapshotToOrder implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        /** @var Quote $quote */
        $quote = $observer->getData('quote');

        /** @var Order $order */
        $order = $observer->getData('order');

        $order->setData('pickup_point_id', $quote->getData('pickup_point_id'));
        $order->setData('pickup_point_code', $quote->getData('pickup_point_code'));
        $order->setData('pickup_point_name', $quote->getData('pickup_point_name'));
        $order->setData('pickup_point_city', $quote->getData('pickup_point_city'));
        $order->setData('pickup_point_address', $quote->getData('pickup_point_address'));
    }
}
