<?php

namespace Demo\ProductAttribute\Api;

use Demo\ProductAttribute\Api\Data\ProductAttributeInterface;

/**
 * Product attribute management interface.
 *
 * @api
 */
interface ProductAttributeManagementInterface
{
    /**
     * Get product attribute value by product SKU.
     *
     * @param string $sku Product SKU.
     * @param string|null $attribute_code Attribute code. Defaults to status.
     * @return \Demo\ProductAttribute\Api\Data\ProductAttributeInterface
     */
    public function getProductAttribute(
        string $sku,
        ?string $attribute_code = null,
    ): ProductAttributeInterface;
}
