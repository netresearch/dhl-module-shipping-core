<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Observer;

use Dhl\ShippingCore\Observer\SplitAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressRepository;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

/**
 * SaveSplitAddressTest
 *
 * In this test, the observer is not directly invoked.
 * It is invoked by saving an order address instead.
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class SaveSplitAddressTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @return string[][]
     */
    public function dataProvider()
    {
        return [
            'billing' => [
                'flatrate', // carrier that requires splitting (observer configuration)
                Address::TYPE_BILLING, // address typ to update
                'Mittelstraße 20', // street to update
                '', // expected street name
                '', // expected street number
                '' // expected supplement
            ],
            'shipping_no_split' => [
                'tablerate',
                Address::TYPE_SHIPPING,
                'Mittelstraße 20',
                '',
                '',
                ''
            ],
            'shipping_split_street' => [
                'flatrate',
                Address::TYPE_SHIPPING,
                'Mittelstraße 20',
                'Mittelstraße',
                '20',
                ''
            ],
            'shipping_split_supplement' => [
                'flatrate',
                Address::TYPE_SHIPPING,
                'Mittelstraße 20 13. Stock',
                'Mittelstraße',
                '20',
                '13. Stock'
            ],
        ];
    }

    /**
     * Set the carrier code that requires street splitting. Make sure a fresh observer instance gets created.
     *
     * Note: "shared=false" object manager config has no effect on observers.
     *
     * @param string $carrierCode
     */
    private static function updateCarrierCode(string $carrierCode)
    {
        $observerArgs = ['carrierCodes' => [$carrierCode => $carrierCode]];
        Bootstrap::getObjectManager()->configure([SplitAddress::class => ['arguments' => $observerArgs]]);
        Bootstrap::getObjectManager()->removeSharedInstance(SplitAddress::class);
    }

    /**
     * Make sure the fixture order address gets not split. All splitting behaviour testing happens in actual test.
     *
     * @throws \Exception
     */
    public static function createOrder()
    {
        self::updateCarrierCode('foobar');
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
    }

    /**
     * Roll back fixture.
     *
     * @throws LocalizedException
     */
    public static function createOrderRollback()
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
     * - Save a billing address → no extension attributes are updated
     * - Save a shipping address that does not require splitting → no extension attributes are updated
     * - Save a shipping address that requires splitting → extension attributes are updated
     *
     * @test
     * @dataProvider dataProvider
     * @magentoDataFixture createOrder
     *
     * @param string $carrierCode
     * @param string $addressType
     * @param string $fullStreet
     * @param string $expectedName
     * @param string $expectedNumber
     * @param string $expectedSupplement
     * @throws LocalizedException
     */
    public function saveAddress(
        string $carrierCode,
        string $addressType,
        string $fullStreet,
        string $expectedName,
        string $expectedNumber,
        string $expectedSupplement
    ) {
        self::updateCarrierCode($carrierCode);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->create(OrderRepositoryInterface::class);

        /** @var Address $address */
        $address = ($addressType === Address::TYPE_SHIPPING)
            ? self::$order->getShippingAddress()
            : self::$order->getBillingAddress();
        $address->setStreet($fullStreet);

        $orderRepository->save(self::$order);

        // reset repository's registry, reload address
        /** @var AddressRepository $addressRepository */
        $addressRepository = Bootstrap::getObjectManager()->create(AddressRepository::class);

        $address = $addressRepository->get($address->getEntityId());

        $attributes = $address->getExtensionAttributes();
        $this->assertEquals($expectedName, $attributes ? $attributes->getDhlgwStreetName() : '');
        $this->assertEquals($expectedNumber, $attributes ? $attributes->getDhlgwStreetNumber() : '');
        $this->assertEquals($expectedSupplement, $attributes ? $attributes->getDhlgwStreetSupplement() : '');
    }
}
