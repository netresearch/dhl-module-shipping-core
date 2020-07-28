<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\ShipmentDate;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Model\ShipmentDate\ShipmentDate;
use Dhl\ShippingCore\Model\ShipmentDate\Validator\NoHoliday;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

class ShipmentDateTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @throws \Exception
     */
    public static function createOrder()
    {
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
    }

    /**
     * Roll back fixture.
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
     * Test data provider.
     *
     * @return \DateTime[][]|string[][]
     */
    public function dataProvider(): array
    {
        /**
         * 2019-02-01 10:00:00 was a Friday.
         *
         * @return \DateTime
         */
        $createBaseDate = static function (): \DateTime {
            return (new \DateTime())
                ->setDate(2019, 2, 1)
                ->setTime(10, 0);
        };

        return [
            'before cut-off time, all days allowed' => [
                'currentTime' => $createBaseDate(),
                'cutoffTime' => $createBaseDate()->setTime(15, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 2, 1),
            ],
            'before cut-off time, all days allowed, but current day is a holiday' => [
                'currentTime' => $createBaseDate()->setDate(2019, 1, 1),
                'cutoffTime' => $createBaseDate()->setTime(15, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 1, 2),
            ],
            'after cut-off time, all days allowed' => [
                'currentTime' => $createBaseDate(),
                'cutoffTime' => $createBaseDate()->setTime(8, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 2, 2),
            ],
            'after cut-off time, all days allowed, but following days are holidays' => [
                'currentTime' => $createBaseDate()->setDate(2019, 12, 24),
                'cutoffTime' => $createBaseDate()->setTime(8, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 12, 27),
            ],
        ];
    }

    /**
     * Assert that the correct shipment date gets calculated.
     *
     * Calculation takes into account current time, cut-off time, and holidays.
     *
     * @test
     * @dataProvider dataProvider
     * @magentoDataFixture createOrder
     *
     * @magentoConfigFixture default_store shipping/origin/country_id DE
     * @magentoConfigFixture default_store shipping/origin/region_id 91
     * @magentoConfigFixture default_store shipping/origin/postcode 04229
     * @magentoConfigFixture default_store shipping/origin/city Leipzig
     * @magentoConfigFixture default_store shipping/origin/street_line1 Nonnenstraße 11
     *
     * @param \DateTime $currentTime
     * @param \DateTime $cutOffTime
     * @param \DateTime $expectedDate
     *
     * @throws \RuntimeException
     */
    public function calculateShipmentDate(
        \DateTime $currentTime,
        \DateTime $cutOffTime,
        \DateTime $expectedDate
    ) {
        $timezoneMock = $this->getMockBuilder(TimezoneInterface::class)->disableOriginalConstructor()->getMock();
        $timezoneMock->method('scopeDate')->willReturn($currentTime);
        $configMock = $this->getMockBuilder(ConfigInterface::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getCutOffTime')->willReturn($cutOffTime);

        $dayValidator = Bootstrap::getObjectManager()->create(NoHoliday::class);

        /** @var ShipmentDate $subject */
        $subject = Bootstrap::getObjectManager()->create(
            ShipmentDate::class,
            [
                'timezone' => $timezoneMock,
                'config' => $configMock,
                'dayValidators' => [$dayValidator],
            ]
        );

        $result = $subject->getDate(self::$order->getStoreId());

        self::assertEquals($expectedDate, $result);
    }

    /**
     * Assert behaviour when no shipment date can be calculated.
     *
     * Invalid drop-off configuration might lead to no shipment date.
     * Make sure an exception is thrown to indicate an error.
     *
     * @magentoConfigFixture default_store shipping/origin/country_id DE
     * @magentoConfigFixture default_store shipping/origin/region_id 91
     * @magentoConfigFixture default_store shipping/origin/postcode 04229
     * @magentoConfigFixture default_store shipping/origin/city Leipzig
     * @magentoConfigFixture default_store shipping/origin/street_line1 Nonnenstraße 11
     *
     * @test
     */
    public function calculationError()
    {
        $this->expectExceptionMessage("No valid start date.");
        $this->expectException(\RuntimeException::class);

        /**
         * 2019-02-01 10:00:00 was a Friday.
         *
         * @return \DateTime
         */
        $createBaseDate = static function (): \DateTime {
            return (new \DateTime())
                ->setDate(2019, 2, 1)
                ->setTime(10, 0);
        };

        $cutOffTime = $createBaseDate()->setTime(8, 0);

        // every day is a holiday!
        $holidayValidatorMock = $this->getMockBuilder(NoHoliday::class)->disableOriginalConstructor()->getMock();
        $holidayValidatorMock->method('validate')->willReturn(false);

        $timezoneMock = $this->getMockBuilder(TimezoneInterface::class)->disableOriginalConstructor()->getMock();
        $timezoneMock->method('scopeDate')->willReturn($createBaseDate());
        $configMock = $this->getMockBuilder(ConfigInterface::class)->disableOriginalConstructor()->getMock();
        $configMock->method('getCutOffTime')->willReturn($cutOffTime);

        /** @var ShipmentDate $subject */
        $subject = Bootstrap::getObjectManager()->create(
            ShipmentDate::class,
            [
                'timezone' => $timezoneMock,
                'config' => $configMock,
                'dayValidators' => [$holidayValidatorMock],
            ]
        );

        $subject->getDate(self::$order->getStoreId());
    }
}
