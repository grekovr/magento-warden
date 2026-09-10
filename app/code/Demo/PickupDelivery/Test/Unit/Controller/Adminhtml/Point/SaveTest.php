<?php

namespace Demo\PickupDelivery\Test\Unit\Controller\Adminhtml\Point;

use Demo\PickupDelivery\Api\Data\PointInterface;
use Demo\PickupDelivery\Api\Data\PointInterfaceFactory;
use Demo\PickupDelivery\Api\PointRepositoryInterface;
use Demo\PickupDelivery\Controller\Adminhtml\Point\Save;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SaveTest extends TestCase
{
    private Context|MockObject $context;
    private Request|MockObject $request;
    private PointInterfaceFactory|MockObject $pointFactory;
    private PointRepositoryInterface|MockObject $repository;
    private RedirectFactory|MockObject $redirectFactory;
    private ManagerInterface|MockObject $messageManager;
    private Redirect|MockObject $redirect;

    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->request = $this->createMock(Request::class);
        $this->pointFactory = $this->createMock(PointInterfaceFactory::class);
        $this->repository = $this->createMock(PointRepositoryInterface::class);
        $this->redirectFactory = $this->createMock(RedirectFactory::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->redirect = $this->createMock(Redirect::class);

        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getMessageManager')->willReturn($this->messageManager);
        $this->redirectFactory->method('create')->willReturn($this->redirect);
        $this->redirect->method('setPath')->willReturnSelf();
    }

    public function testSaveCreatesPointAndRedirectsToGrid(): void
    {
        $data = [
            'code' => 'kyiv-center',
            'name' => 'Central Point',
            'city' => 'Kyiv',
            'address' => 'Test address',
            'is_active' => '1',
            'sort_order' => '10',
        ];
        $point = $this->createMock(PointInterface::class);

        $this->request->expects($this->once())->method('getPostValue')->willReturn($data);
        $this->pointFactory->expects($this->once())->method('create')->willReturn($point);
        $point->expects($this->once())->method('setCode')->with('kyiv-center')->willReturnSelf();
        $point->expects($this->once())->method('setName')->with('Central Point')->willReturnSelf();
        $point->expects($this->once())->method('setCity')->with('Kyiv')->willReturnSelf();
        $point->expects($this->once())->method('setAddress')->with('Test address')->willReturnSelf();
        $point->expects($this->once())->method('setIsActive')->with(1)->willReturnSelf();
        $point->expects($this->once())->method('setSortOrder')->with(10)->willReturnSelf();
        $this->repository->expects($this->once())->method('save')->with($point);
        $this->messageManager->expects($this->once())->method('addSuccessMessage');
        $this->redirect->expects($this->once())->method('setPath')->with('*/*/index')->willReturnSelf();

        $controller = new Save(
            $this->context,
            $this->pointFactory,
            $this->repository,
            $this->redirectFactory
        );

        $this->assertSame($this->redirect, $controller->execute());
    }

    public function testEmptyPostRedirectsToGrid(): void
    {
        $this->request->expects($this->once())->method('getPostValue')->willReturn([]);
        $this->redirect->expects($this->once())->method('setPath')->with('*/*/index')->willReturnSelf();

        $controller = new Save(
            $this->context,
            $this->pointFactory,
            $this->repository,
            $this->redirectFactory
        );

        $this->assertSame($this->redirect, $controller->execute());
    }
}
