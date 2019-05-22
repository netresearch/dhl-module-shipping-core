<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Model\Packaging;

use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderFixture;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class PackagingDataProviderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'order 1' => ['order' => OrderFixture::createOrder(
                new AddressDe(),
                new SimpleProduct(),
                'flatrate_flatrate'
            )]
        ];
    }

    /**
     * @param Order $order
     * @dataProvider dataProvider
     */
    public function testGetData(Order $order)
    {
        $objectManager = ObjectManager::getInstance();
        /** @var PackagingDataProvider $subject */
        $subject = $objectManager->create(PackagingDataProvider::class);
        $packagingData = $subject->getData($order);

        self::assertInternalType('array', $packagingData);
    }
}
