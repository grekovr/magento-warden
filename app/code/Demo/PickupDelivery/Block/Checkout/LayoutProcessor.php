<?php

namespace Demo\PickupDelivery\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as CheckoutLayoutProcessor;

class LayoutProcessor
{
    public function afterProcess(
        CheckoutLayoutProcessor $subject,
        array $jsLayout
    ): array {
        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']
        ['demo_pickup_point'] = [
            'component' => 'Demo_PickupDelivery/js/view/pickup-point',
            'displayArea' => 'shippingAdditional',
            'sortOrder' => 20,
        ];

        return $jsLayout;
    }
}
