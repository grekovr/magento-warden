<?php

namespace Demo\PickupDelivery\Test\Unit\Ui\Component\Listing\Column;

use Demo\PickupDelivery\Ui\Component\Listing\Column\Actions;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    public function testPrepareDataSourceAddsEditAndDeleteActions(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $componentFactory = $this->createMock(UiComponentFactory::class);
        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturnMap([
                ['demopickupdelivery/point/edit', ['id' => 3], '/edit/3'],
                ['demopickupdelivery/point/delete', ['id' => 3], '/delete/3'],
            ]);

        $column = new Actions(
            $context,
            $componentFactory,
            $urlBuilder,
            [],
            ['name' => 'actions']
        );

        $dataSource = ['data' => ['items' => [['entity_id' => 3, 'name' => 'Center']]]];
        $result = $column->prepareDataSource($dataSource);

        $this->assertSame('/edit/3', $result['data']['items'][0]['actions']['edit']['href']);
        $this->assertSame('/delete/3', $result['data']['items'][0]['actions']['delete']['href']);
        $this->assertSame('Edit', (string) $result['data']['items'][0]['actions']['edit']['label']);
        $this->assertSame('Delete', (string) $result['data']['items'][0]['actions']['delete']['label']);
        $this->assertArrayHasKey('confirm', $result['data']['items'][0]['actions']['delete']);
    }
}
