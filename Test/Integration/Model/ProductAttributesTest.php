<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Test\Integration\Model;

use Dhl\ShippingCore\Setup\Module\Constants;
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
            [
                'in' => [
                    Constants::ATTRIBUTE_CODE_DG_CATEGORY,
                    Constants::ATTRIBUTE_CODE_TARIFF_NUMBER,
                    Constants::ATTRIBUTE_CODE_EXPORT_DESCRIPTION
                ]
            ]
        );
        self::assertEquals(3, $attributeCollection->getSize());
    }
}
