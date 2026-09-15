<?php

namespace Demo\ProductAttribute\Model;

use Demo\ProductAttribute\Api\Data\ProductAttributeInterface;
use Demo\ProductAttribute\Api\ProductAttributeManagementInterface;
use Demo\ProductAttribute\Exception\UnprocessableEntityException;
use Demo\ProductAttribute\Model\Data\ProductAttributeFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\Set as EavAttributeSet;

/**
 * Product attribute management.
 */
class ProductAttributeManagement implements ProductAttributeManagementInterface
{

    private const DEFAULT_ATTRIBUTE_CODE = 'status';
    private const TEMPLATE_EMPTY_FIELD_MESSAGE = 'The "%1" field cannot be empty.';
    private const TEMPLATE_ATTRIBUTE_NOT_EXISTS_MESSAGE = 'Attribute "%1" does not exist.';
    private const TEMPLATE_ATTRIBUTE_UNSUPPORTED_TYPE_MESSAGE = 'Attribute value has unsupported type.';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductAttributeFactory $productAttributeFactory,
        private readonly EavConfig $eavConfig,
        private readonly EavAttributeSet $eavAttributeSet,
    ) {}

    /**
     * @inheritdoc
     */
    public function getProductAttribute(
        string $sku,
        ?string $attribute_code = null,
    ): ProductAttributeInterface
    {
        // TODO: prepare methods for input args and value.

        $sku = trim($sku);
        if ($sku === '') {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_EMPTY_FIELD_MESSAGE, ProductAttributeInterface::SKU)
            );
        }
        $product = $this->productRepository->get($sku);

        $attribute_code ??= self::DEFAULT_ATTRIBUTE_CODE;
        $attribute_code = trim($attribute_code);
        if ($attribute_code === '') {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_EMPTY_FIELD_MESSAGE, ProductAttributeInterface::ATTRIBUTE_CODE)
            );
        }

        $attribute = $this->eavConfig->getAttribute(
            Product::ENTITY,
            $attribute_code
        );
        if (
            !$attribute
            || !$attribute->getId()
        ) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_NOT_EXISTS_MESSAGE, $attribute_code)
            );
        }

        $attributeSetId = (int) $product->getAttributeSetId();
        $this->eavAttributeSet->addSetInfo(
            Product::ENTITY,
            [$attribute_code],
            $attributeSetId,
        );
        if (!$attribute->isInSet($attributeSetId)) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_NOT_EXISTS_MESSAGE, $attribute_code)
            );
        }

        $value = $product->getData($attribute_code);
        if (
            $value !== null
            && !is_scalar($value)
        ) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_UNSUPPORTED_TYPE_MESSAGE)
            );
        }
        $value = $value === null ? null : (string) $value;

        return $this->productAttributeFactory->create([
            'data' => [
                ProductAttributeInterface::SKU => $product->getSku(),
                ProductAttributeInterface::ATTRIBUTE_CODE => $attribute->getAttributeCode(),
                ProductAttributeInterface::VALUE => $value,
            ],
        ]);
    }
}
