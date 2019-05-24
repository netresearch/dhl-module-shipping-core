<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Model\Packaging;

use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct;
use Dhl\ShippingCore\Test\Integration\Fixture\FakeReader;
use Dhl\ShippingCore\Test\Integration\Fixture\ShipmentFixture;
use Magento\Sales\Model\Order;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class PackagingDataProviderTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'shipment 1' => ['shipment' => ShipmentFixture::createShipment(
                new AddressDe(),
                new SimpleProduct(),
                'flatrate_flatrate'
            )]
        ];
    }

    /**
     * @param Order\Shipment $shipment
     * @dataProvider dataProvider
     */
    public function testGetData(Order\Shipment $shipment)
    {
        $objectManager = ObjectManager::getInstance();
        /** @var PackagingDataProvider $subject */
        $subject = $objectManager->create(PackagingDataProvider::class, ['reader' => new FakeReader()]);
        $packagingData = $subject->getData($shipment);

        self::assertInternalType('array', $packagingData);
    }
}
