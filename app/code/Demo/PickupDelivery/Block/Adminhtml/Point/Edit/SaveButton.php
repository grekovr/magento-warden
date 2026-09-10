<?php

namespace Demo\PickupDelivery\Block\Adminhtml\Point\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => '',
            'sort_order' => 90,
        ];
    }
}
