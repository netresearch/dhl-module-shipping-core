<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Model\ShippingSettings\PackagingDataProvider;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingDataHydrator;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct2;
use Dhl\ShippingCore\Test\Integration\Fixture\FakeReader;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderFixture;
use Dhl\ShippingCore\Test\Integration\Fixture\ShipmentFixture;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class PackagingDataProviderTest extends TestCase
{
    /**
     * @return ShipmentInterface[][]
     * @throws \Exception
     */
    public function dataProvider(): array
    {
        $shipment = ShipmentFixture::createFailedShipment(
            new AddressDe(),
            [new SimpleProduct(), new SimpleProduct2()],
            'flatrate_flatrate'
        );
        $order = $shipment->getOrder();

        // force items reload, they are not properly indexed after "checkout"
        $order->setItems(null);

        /** @var Collection $shipmentCollection */
        $shipmentCollection = Bootstrap::getObjectManager()->create(Collection::class);
        $shipmentCollection->setOrderFilter($order);

        return [
            $shipmentCollection->getItems()
        ];
    }

    /**
     * @param Order\Shipment $shipment
     * @dataProvider dataProvider
     */
    public function testGetData(Order\Shipment $shipment)
    {
        /** @var PackagingDataProvider $subject */
        $subject = Bootstrap::getObjectManager()->create(PackagingDataProvider::class, ['reader' => new FakeReader()]);
        $packagingData = $subject->getData($shipment);
        self::assertInstanceOf(ShippingDataInterface::class, $packagingData);

        /** @var ShippingDataHydrator $hydrator */
        $hydrator = Bootstrap::getObjectManager()->create(ShippingDataHydrator::class);

        $data = $hydrator->toArray($packagingData);

        self::assertNotEmpty($data);
    }
}
