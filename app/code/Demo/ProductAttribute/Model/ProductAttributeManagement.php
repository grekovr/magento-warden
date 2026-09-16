<?php

namespace Demo\ProductAttribute\Model;

use Demo\ProductAttribute\Api\Data\ProductAttributeInterface;
use Demo\ProductAttribute\Api\ProductAttributeManagementInterface;
use Demo\ProductAttribute\Exception\UnprocessableEntityException;
use Demo\ProductAttribute\Model\Data\ProductAttributeFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Set as EavAttributeSet;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @throws NoSuchEntityException
     * @throws UnprocessableEntityException
     * @throws LocalizedException
     */
    public function getProductAttribute(
        string $sku,
        ?string $attribute_code = null,
    ): ProductAttributeInterface
    {
        $sku = $this->normalizeSku($sku);

        /** @var Product $product */
        $product = $this->productRepository->get($sku);

        $attribute_code = $this->resolveAttributeCode($attribute_code);

        $attribute = $this->resolveAttribute($attribute_code);

        $this->validateAttributeAssignment($attribute, $product);

        $value = $this->normalizeValue($product->getData($attribute_code));

        return $this->productAttributeFactory->create([
            'data' => [
                ProductAttributeInterface::SKU => $product->getSku(),
                ProductAttributeInterface::ATTRIBUTE_CODE => $attribute->getAttributeCode(),
                ProductAttributeInterface::VALUE => $value,
            ],
        ]);
    }

    /**
     * @throws UnprocessableEntityException
     */
    private function normalizeSku(
        string $sku,
    ): string
    {
        $sku = trim($sku);
        if ($sku === '') {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_EMPTY_FIELD_MESSAGE, ProductAttributeInterface::SKU)
            );
        }
        return $sku;
    }

    /**
     * @throws UnprocessableEntityException
     */
    private function resolveAttributeCode(
        ?string $attribute_code = null,
    ): string
    {
        $attribute_code ??= self::DEFAULT_ATTRIBUTE_CODE;
        $attribute_code = trim($attribute_code);
        if ($attribute_code === '') {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_EMPTY_FIELD_MESSAGE, ProductAttributeInterface::ATTRIBUTE_CODE)
            );
        }
        return $attribute_code;
    }

    /**
     * @throws LocalizedException
     * @throws UnprocessableEntityException
     */
    private function resolveAttribute(
        string $attribute_code,
    ): AbstractAttribute
    {
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
        return $attribute;
    }

    /**
     * @throws UnprocessableEntityException
     */
    private function validateAttributeAssignment(
        AbstractAttribute $attribute,
        ProductInterface $product,
    ): void
    {
        $attributeCode = $attribute->getAttributeCode();
        $attributeSetId = $product->getAttributeSetId();

        if ($attributeSetId === null) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_NOT_EXISTS_MESSAGE, $attributeCode)
            );
        }

        $this->eavAttributeSet->addSetInfo(
            Product::ENTITY,
            [$attributeCode],
            $attributeSetId,
        );
        if (!$attribute->isInSet($attributeSetId)) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_NOT_EXISTS_MESSAGE, $attributeCode)
            );
        }
    }

    /**
     * @throws UnprocessableEntityException
     */
    private function normalizeValue(
        mixed $value
    ): ?string
    {
        if (
            $value !== null
            && !is_scalar($value)
        ) {
            throw new UnprocessableEntityException(
                __(self::TEMPLATE_ATTRIBUTE_UNSUPPORTED_TYPE_MESSAGE)
            );
        }
        return $value === null
            ? null
            : (string) $value;
    }
}
