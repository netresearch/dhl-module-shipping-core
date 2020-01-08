<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Attribute;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Migrate
 *
 */
class Migrate
{
    const DHL_DG_CATEGORY = 'dhl_dangerous_goods_category';

    const DHL_TARIFF_NUMBER = 'dhl_tariff_number';

    const DHL_EXPORT_DESCRIPTION = 'dhl_export_description';

    /**
     * @var Collection
     */
    private $productCollection;

    /**
     * @var ProductResource
     */
    private $productResource;

    private $attributes = [
        DGCategory::CODE => self::DHL_DG_CATEGORY,
        TariffNumber::CODE => self::DHL_TARIFF_NUMBER,
        ExportDescription::CODE => self::DHL_EXPORT_DESCRIPTION
    ];

    /**
     * Migrate constructor.
     *
     * @param Collection $productCollection
     * @param ProductResource $productResource
     */
    public function __construct(
        Collection $productCollection,
        ProductResource $productResource
    ) {
        $this->productCollection = $productCollection;
        $this->productResource = $productResource;
    }

    /**
     * Perform Attribute migration.
     *
     * @return array
     */
    public function runAttributeMigration(): array
    {
        $productTypes = [
            Type::TYPE_SIMPLE,
            Type::TYPE_BUNDLE,
            Configurable::TYPE_CODE
        ];

        $result = [];

        $products = $this->productCollection->addAttributeToSelect(
            [
                self::DHL_DG_CATEGORY,
                self::DHL_TARIFF_NUMBER,
                self::DHL_EXPORT_DESCRIPTION
            ]
        )->addFieldToFilter('type_id', ['in' => $productTypes]);

        /** @var Product $product */
        foreach ($products as $product) {
            try {
                $this->moveAttribute($product);
            } catch (\Exception $exception) {
                $result[] = sprintf('Could not migrate DHL attributes for SKU: %s', $product->getSku());
            }
        }

        return $result;
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    private function moveAttribute(Product $product)
    {
        foreach ($this->attributes as $new => $old) {
            $value = $product->getData($old);
            $product->addData([$new => $value]);
            $this->productResource->saveAttribute($product, $new);
        }
    }
}
