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

        $jsLayout['components']['checkout']['children']['sidebar']['children']
        ['shipping-information']['config']['template'] =
            'Demo_PickupDelivery/shipping-information';

        $jsLayout['components']['checkout']['children']['sidebar']['children']
        ['shipping-information']['children']
        ['demo_pickup_point_summary'] = [
            'component' => 'Demo_PickupDelivery/js/view/pickup-point-summary',
            'displayArea' => 'shipping-additional',
            'sortOrder' => 10,
        ];

        return $jsLayout;
    }
}
