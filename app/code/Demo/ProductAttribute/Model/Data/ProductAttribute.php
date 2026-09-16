<?php

namespace Demo\ProductAttribute\Model\Data;

use Demo\ProductAttribute\Api\Data\ProductAttributeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Product attribute data model.
 */
class ProductAttribute extends AbstractSimpleObject implements ProductAttributeInterface
{

    /**
     * @inheritdoc
     */
    public function getSku(): string
    {
        return (string) $this->_get(self::SKU);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeCode(): string
    {
        return (string) $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function getValue(): ?string
    {
        return $this->_get(self::VALUE);
    }
}
