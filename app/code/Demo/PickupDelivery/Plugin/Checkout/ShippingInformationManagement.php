<?php

namespace Demo\PickupDelivery\Plugin\Checkout;

use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement as Subject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class ShippingInformationManagement
{
    public function __construct(
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly PointRepositoryInterface $pointRepository
    ) {
    }

    public function beforeSaveAddressInformation(
        Subject $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        $quote = $this->quoteRepository->getActive($cartId);

        if ($addressInformation->getShippingCarrierCode() !== 'pickupdelivery') {
            $this->clearPickupPoint($quote);

            return [$cartId, $addressInformation];
        }

        $pickupPointId = (int) ($addressInformation->getExtensionAttributes()
            ?->getPickupPointId() ?? 0);

        if (!$pickupPointId) {
            throw new LocalizedException(
                __('Please select a pickup point.')
            );
        }

        $point = $this->pointRepository->getById($pickupPointId);

        if (!$point->getIsActive()) {
            throw new LocalizedException(
                __('The selected pickup point is inactive.')
            );
        }

        $quote->setData('pickup_point_id', $point->getId());
        $quote->setData('pickup_point_code', $point->getCode());
        $quote->setData('pickup_point_name', $point->getName());
        $quote->setData('pickup_point_city', $point->getCity());
        $quote->setData('pickup_point_address', $point->getAddress());

        return [$cartId, $addressInformation];
    }

    private function clearPickupPoint(Quote $quote): void
    {
        $quote->setData('pickup_point_id', null);
        $quote->setData('pickup_point_code', null);
        $quote->setData('pickup_point_name', null);
        $quote->setData('pickup_point_city', null);
        $quote->setData('pickup_point_address', null);
    }
}
