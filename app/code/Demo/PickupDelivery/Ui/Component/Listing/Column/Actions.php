<?php

namespace Demo\PickupDelivery\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item['entity_id'])) {
                    continue;
                }

                $id = (int) $item['entity_id'];

                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $this->urlBuilder->getUrl(
                            'demopickupdelivery/point/edit',
                            ['id' => $id]
                        ),
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl(
                            'demopickupdelivery/point/delete',
                            ['id' => $id]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete pickup point'),
                            'message' => __(
                                'Are you sure you want to delete this pickup point?'
                            ),
                        ],
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
