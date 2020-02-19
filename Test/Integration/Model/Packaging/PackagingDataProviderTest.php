<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Model\ShippingSettings\PackagingDataProvider;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingDataHydrator;
use Dhl\ShippingCore\Test\Integration\Fixture\FakeReader;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;
use TddWizard\Fixtures\Sales\ShipmentBuilder;

class PackagingDataProviderTest extends TestCase
{
    /**
     * @var OrderInterface
     */
    private static $order;

    /**
     * @var ShipmentInterface
     */
    private static $shipment;

    /**
     * Create order fixture for DE recipient address with shipment and label status "Failed".
     *
     * @throws \Exception
     */
    public static function createFailedShipment()
    {
        self::$order = OrderBuilder::anOrder()
             ->withShippingMethod('flatrate_flatrate')
             ->withLabelStatus(LabelStatusManagementInterface::LABEL_STATUS_FAILED)
             ->build();

        // force items reload. order items are indexed consecutively, not by item id after "checkout"
        self::$order->setItems(null);

        self::$shipment = ShipmentBuilder::forOrder(self::$order)->build();
    }

    /**
     * @throws \Exception
     */
    public static function createFailedShipmentRollback()
    {
        try {
            OrderFixtureRollback::create()->execute(new OrderFixture(self::$order));
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    /**
     * @magentoDataFixture createFailedShipment
     */
    public function testGetData()
    {
        /** @var PackagingDataProvider $subject */
        $subject = Bootstrap::getObjectManager()->create(PackagingDataProvider::class, ['reader' => new FakeReader()]);
        $packagingData = $subject->getData(self::$shipment);
        self::assertInstanceOf(ShippingDataInterface::class, $packagingData);

        /** @var ShippingDataHydrator $hydrator */
        $hydrator = Bootstrap::getObjectManager()->create(ShippingDataHydrator::class);

        $data = $hydrator->toArray($packagingData);

        self::assertNotEmpty($data);
    }
}
