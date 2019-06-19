<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class ProductData
 *
 * @package Dhl\ShippingCore\Model
 */
class ProductData
{
    /**
     * @var Collection
     */
    private $productCollection;

    /**
     * ProductData constructor.
     * @param Collection $productCollection
     */
    public function __construct(Collection $productCollection)
    {
        $this->productCollection = $productCollection;
    }

    /**
     * @param string[] $productIds
     * @param $storeId
     *
     * @return array
     */
    public function getProductData(array $productIds, $storeId): array
    {
        $this->productCollection->addStoreFilter($storeId)
            ->addFieldToFilter(
                'entity_id',
                ['in' => $productIds]
            )->addAttributeToSelect(
                DGCategory::CODE,
                true
            )->addAttributeToSelect(
                TariffNumber::CODE,
                true
            )->addAttributeToSelect(
                ExportDescription::CODE,
                true
            )->addAttributeToSelect(
                'country_of_manufacture',
                true
            );

        $data = [];

        while ($product = $this->productCollection->fetchItem()) {
            $data[$product->getId()] = [
                DGCategory::CODE        => $product->getData(DGCategory::CODE),
                ExportDescription::CODE => $product->getData(ExportDescription::CODE),
                TariffNumber::CODE      => $product->getData(TariffNumber::CODE),
                'countryOfOrigin'       => $product->getData('country_of_manufacture')
            ];
        }

        return $data;
    }
}
