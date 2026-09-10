<?php

namespace Demo\PickupDelivery\Block\Adminhtml\Point\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    public function __construct(
        private readonly Context $context
    ) {
    }

    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->context->getUrlBuilder()->getUrl('*/*/index')
            ),
            'sort_order' => 10,
        ];
    }
}
