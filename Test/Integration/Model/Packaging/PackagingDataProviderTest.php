<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Dhl\ShippingCore\Model\ShippingDataHydrator;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct2;
use Dhl\ShippingCore\Test\Integration\Fixture\FakeReader;
use Dhl\ShippingCore\Test\Integration\Fixture\ShipmentFixture;
use Magento\Framework\Exception\LocalizedException;
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
                [new SimpleProduct(), new SimpleProduct2()],
                'flatrate_flatrate'
            )]
        ];
    }

    /**
     * @param Order\Shipment $shipment
     * @dataProvider dataProvider
     * @throws LocalizedException
     */
    public function testGetData(Order\Shipment $shipment)
    {
        $objectManager = ObjectManager::getInstance();
        /** @var PackagingDataProvider $subject */
//        $subject = $objectManager->create(PackagingDataProvider::class);
        $subject = $objectManager->create(PackagingDataProvider::class, ['reader' => new FakeReader()]);
        $packagingData = $subject->getData($shipment);
        self::assertInstanceOf(ShippingDataInterface::class, $packagingData);

        /** @var ShippingDataHydrator $hydrator */
        $hydrator = $objectManager->create(ShippingDataHydrator::class);

        $data = $hydrator->toArray($packagingData);

        self::assertNotEmpty($data);
    }
}
