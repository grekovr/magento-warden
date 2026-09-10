<?php

namespace Demo\PickupDelivery\Controller\Adminhtml\Point;

use Demo\PickupDelivery\Api\Data\PointInterfaceFactory;
use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Demo_PickupDelivery::pickup_points';

    public function __construct(
        Action\Context $context,
        private readonly PointInterfaceFactory $pointFactory,
        private readonly PointRepositoryInterface $pointRepository,
        private readonly RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $this->redirectFactory
                ->create()
                ->setPath('*/*/index');
        }

        try {
            $point = $this->pointFactory->create();

            if (!empty($data['entity_id'])) {
                $point->setId((int) $data['entity_id']);
            }

            $point->setCode((string) ($data['code'] ?? ''));
            $point->setName((string) ($data['name'] ?? ''));
            $point->setCity((string) ($data['city'] ?? ''));
            $point->setAddress((string) ($data['address'] ?? ''));
            $point->setIsActive((int) ($data['is_active'] ?? 0));
            $point->setSortOrder((int) ($data['sort_order'] ?? 0));

            $this->pointRepository->save($point);

            $this->messageManager->addSuccessMessage(
                __('The pickup point has been saved.')
            );

            return $this->redirectFactory
                ->create()
                ->setPath('*/*/index');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __('Unable to save the pickup point.')
            );
        }

        return $this->redirectFactory
            ->create()
            ->setPath('*/*/new');
    }
}
