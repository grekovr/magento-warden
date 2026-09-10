<?php

namespace Demo\PickupDelivery\Controller\Adminhtml\Point;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'Demo_PickupDelivery::pickup_points';

    public function __construct(
        Action\Context $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        return $this->pageFactory->create();
    }
}
