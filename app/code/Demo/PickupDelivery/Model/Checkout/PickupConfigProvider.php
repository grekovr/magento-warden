<?php

namespace Demo\PickupDelivery\Model\Checkout;

use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\ConfigProviderInterface;

class PickupConfigProvider implements ConfigProviderInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CheckoutSession $checkoutSession
    ) {
    }

    public function getConfig(): array
    {
        $points = [];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setOrder('sort_order', 'ASC');

        foreach ($collection as $point) {
            $points[] = [
                'id' => (int) $point->getId(),
                'code' => (string) $point->getCode(),
                'name' => (string) $point->getName(),
                'city' => (string) $point->getCity(),
                'address' => (string) $point->getAddress(),
            ];
        }

        return [
            'demoPickupDelivery' => [
                'points' => $points,
                'selectedPointId' => $this->checkoutSession
                    ->getQuote()
                    ->getData('pickup_point_id'),
            ],
        ];
    }
}
