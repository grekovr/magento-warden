<?php

namespace Demo\PickupDelivery\Test\Unit\Controller\Adminhtml\Point;

use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Demo\PickupDelivery\Controller\Adminhtml\Point\Delete;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    private Context|MockObject $context;
    private Request|MockObject $request;
    private PointRepositoryInterface|MockObject $repository;
    private RedirectFactory|MockObject $redirectFactory;
    private ManagerInterface|MockObject $messageManager;
    private Redirect|MockObject $redirect;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->request = $this->createMock(Request::class);
        $this->repository = $this->createMock(PointRepositoryInterface::class);
        $this->redirectFactory = $this->createMock(RedirectFactory::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->redirect = $this->createMock(Redirect::class);

        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->redirectFactory->method('create')->willReturn($this->redirect);
        $this->redirect->method('setPath')->willReturnSelf();
    }

    public function testDeleteRemovesPointAndRedirectsToGrid(): void
    {
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn(7);
        $this->repository->expects($this->once())->method('deleteById')->with(7);
        $this->messageManager->expects($this->once())->method('addSuccessMessage');
        $this->redirect->expects($this->once())->method('setPath')->with('*/*/index')->willReturnSelf();

        $controller = new Delete($this->context, $this->repository, $this->redirectFactory);

        $this->assertSame($this->redirect, $controller->execute());
    }

    public function testMissingIdDoesNotCallRepository(): void
    {
        $this->request->expects($this->once())->method('getParam')->with('id')->willReturn(null);
        $this->repository->expects($this->never())->method('deleteById');
        $this->messageManager->expects($this->once())->method('addErrorMessage');
        $this->redirect->expects($this->once())->method('setPath')->with('*/*/index')->willReturnSelf();

        $controller = new Delete($this->context, $this->repository, $this->redirectFactory);

        $this->assertSame($this->redirect, $controller->execute());
    }
}
