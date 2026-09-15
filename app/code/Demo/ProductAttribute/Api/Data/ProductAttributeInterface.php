<?php

namespace Demo\ProductAttribute\Api\Data;

/**
 * Product attribute data.
 *
 * @api
 */
interface ProductAttributeInterface
{
    public const SKU = 'sku';
    public const ATTRIBUTE_CODE = 'attribute_code';
    public const VALUE = 'value';

    /**
     * Get product SKU.
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Get attribute code.
     *
     * @return string
     */
    public function getAttributeCode(): string;

    /**
     * Get attribute value.
     *
     * @return string|null
     */
    public function getValue(): ?string;
}
