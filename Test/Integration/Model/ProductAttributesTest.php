<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Test\Integration\Model;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ProductAttributesTest extends TestCase
{
    public function testProductAttributesProperlyCreated()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var AttributeCollectionFactory $attributeCollectionFactory */
        $attributeCollectionFactory = $objectManager->get(AttributeCollectionFactory::class);
        /** @var AttributeCollection $attributeCollection */
        $attributeCollection = $attributeCollectionFactory->create();

        $attributeCollection->addFieldToFilter(
            'attribute_code',
            ['in' => [DGCategory::CODE, TariffNumber::CODE, ExportDescription::CODE]]
        );
        self::assertEquals(3, $attributeCollection->getSize());
    }
}
