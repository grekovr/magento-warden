<?php

namespace Demo\ProductAttribute\Plugin\Webapi;

use Demo\ProductAttribute\Api\Data\ProductAttributeInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

class DataObjectProcessorPlugin
{
    public function afterBuildOutputDataArray(
        DataObjectProcessor $subject,
        array $result,
        mixed $dataObject,
        string $dataObjectType
    ): array
    {
        if ($dataObject instanceof ProductAttributeInterface && $dataObject->getValue() === null) {
            $result[ProductAttributeInterface::VALUE] = null;
        }
        return $result;
    }
}
