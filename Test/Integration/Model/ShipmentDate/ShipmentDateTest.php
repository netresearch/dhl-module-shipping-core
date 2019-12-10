<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\ShipmentDate;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Model\ShipmentDate\Validator\NoHoliday;
use Dhl\ShippingCore\Model\ShipmentDate\ShipmentDate;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct2;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderFixture;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipmentDateTest
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class ShipmentDateTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ConfigInterface|MockObject
     */
    private $configMock;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $timezoneMock;

    /**
     * @throws \Exception
     */
    public static function createOrder()
    {
        self::$order = OrderFixture::createOrder(new AddressDe(), [new SimpleProduct2()], 'flatrate_flatrate');
    }

    /**
     * @throws \Exception
     */
    public static function createOrderRollback()
    {
        try {
            OrderFixture::rollbackFixtureEntities();
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();

        $this->timezoneMock = $this->getMockBuilder(TimezoneInterface::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();
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
                'originCountry' => 'DE',
            ],
            'before cut-off time, all days allowed, but current day is a holiday' => [
                'currentTime' => $createBaseDate()->setDate(2019, 1, 1),
                'cutoffTime' => $createBaseDate()->setTime(15, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 1, 2),
                'originCountry' => 'DE',
            ],
            'after cut-off time, all days allowed' => [
                'currentTime' => $createBaseDate(),
                'cutoffTime' => $createBaseDate()->setTime(8, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 2, 2),
                'originCountry' => 'DE',
            ],
            'after cut-off time, all days allowed, but following days are holidays' => [
                'currentTime' => $createBaseDate()->setDate(2019, 12, 24),
                'cutoffTime' => $createBaseDate()->setTime(8, 0),
                'expectedDate' => $createBaseDate()->setDate(2019, 12, 27),
                'originCountry' => 'DE',
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
     * @param \DateTime $currentTime
     * @param \DateTime $cutOffTime
     * @param \DateTime $expectedDate
     * @param string $originCountry
     *
     * @throws \RuntimeException
     */
    public function calculateShipmentDate(
        \DateTime $currentTime,
        \DateTime $cutOffTime,
        \DateTime $expectedDate,
        string $originCountry
    ) {
        $this->timezoneMock->method('scopeDate')->willReturn($currentTime);
        $this->configMock->method('getCutOffTime')->willReturn($cutOffTime);
        $this->configMock->method('getOriginCountry')->willReturn($originCountry);

        $dayValidator = $this->objectManager->create(NoHoliday::class, ['config' => $this->configMock]);
        /** @var ShipmentDate $subject */
        $subject = $this->objectManager->create(
            ShipmentDate::class,
            [
                'timezone' => $this->timezoneMock,
                'config' => $this->configMock,
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
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No valid start date.
     */
    public function calculationError()
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

        $cutOffTime = $createBaseDate()->setTime(8, 0);

        $holidayValidatorMock = $this->getMockBuilder(NoHoliday::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();

        // All days are holidays
        $holidayValidatorMock->method('validate')->willReturn(false);

        $this->timezoneMock->method('scopeDate')->willReturn($createBaseDate());
        $this->configMock->method('getCutOffTime')->willReturn($cutOffTime);

        /** @var ShipmentDate $subject */
        $subject = $this->objectManager->create(
            ShipmentDate::class,
            [
                'timezone' => $this->timezoneMock,
                'config' => $this->configMock,
                'dayValidators' => [$holidayValidatorMock],
            ]
        );

        $subject->getDate(self::$order->getStoreId());
    }
}
