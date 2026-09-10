<?php

namespace Demo\PickupDelivery\Controller\Adminhtml\Point;

use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Demo_PickupDelivery::pickup_points';

    public function __construct(
        Action\Context $context,
        private readonly PointRepositoryInterface $pointRepository,
        private readonly RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Missing pickup point ID.'));

            return $this->redirectFactory->create()->setPath('*/*/index');
        }

        try {
            $this->pointRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(
                __('The pickup point has been deleted.')
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable) {
            $this->messageManager->addErrorMessage(
                __('Unable to delete the pickup point.')
            );
        }

        return $this->redirectFactory->create()->setPath('*/*/index');
    }
}
