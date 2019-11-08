<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model;

use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\DayValidator\NoHoliday;
use Dhl\ShippingCore\Model\ShipmentDate;
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
 * @package Dhl\ShippingCore\Test\Integration\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class ShipmentDateTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $mockConfig;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $mockTimezone;

    /**
     * @var Order
     */
    private $order;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();

        $this->mockConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTimezone = $this->getMockBuilder(TimezoneInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Order $order */
        $this->order = OrderFixture::createOrder(
            new AddressDe(),
            [
                new SimpleProduct2()
            ],
            'flatrate_flatrate'
        );
    }

    /**
     * Test data provider.
     *
     * @return array
     */
    public function getTestData(): array
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
                'currentTime'   => $createBaseDate(),
                'cutoffTime'    => $createBaseDate()->setTime(15, 0),
                'expectedDate'  => $createBaseDate()->setDate(2019, 2, 1),
                'originCountry' => 'DE',
            ],
            'before cut-off time, all days allowed, but current day is a holiday' => [
                'currentTime'   => $createBaseDate()->setDate(2019, 1, 1),
                'cutoffTime'    => $createBaseDate()->setTime(15, 0),
                'expectedDate'  => $createBaseDate()->setDate(2019, 1, 2),
                'originCountry' => 'DE',
            ],
            'after cut-off time, all days allowed' => [
                'currentTime'   => $createBaseDate(),
                'cutoffTime'    => $createBaseDate()->setTime(8, 0),
                'expectedDate'  => $createBaseDate()->setDate(2019, 2, 2),
                'originCountry' => 'DE',
            ],
            'after cut-off time, all days allowed, but following days are holidays' => [
                'currentTime'   => $createBaseDate()->setDate(2019, 12, 24),
                'cutoffTime'    => $createBaseDate()->setTime(8, 0),
                'expectedDate'  => $createBaseDate()->setDate(2019, 12, 27),
                'originCountry' => 'DE',
            ],
        ];
    }

    /**
     * @dataProvider getTestData
     *
     * @param \DateTime $currentTime
     * @param \DateTime $cutOffTime
     * @param \DateTime $expectedDate
     * @param string $originCountry
     *
     * @throws \RuntimeException
     */
    public function testGetDate(
        \DateTime $currentTime,
        \DateTime $cutOffTime,
        \DateTime $expectedDate,
        string $originCountry
    ) {
        $this->mockTimezone->method('scopeDate')->willReturn($currentTime);
        $this->mockConfig->method('getCutOffTime')->willReturn($cutOffTime);
        $this->mockConfig->method('getOriginCountry')->willReturn($originCountry);

        /** @var ShipmentDate $subject */
        $subject = $this->objectManager->create(
            ShipmentDate::class,
            [
                'timezone' => $this->mockTimezone,
                'config'          => $this->mockConfig,
                'dayValidators'   => [
                    $this->objectManager->create(
                        NoHoliday::class,
                        [
                            'config' => $this->mockConfig,
                        ]
                    ),
                ],
            ]
        );

        $result = $subject->getDate($this->order->getStoreId());

        self::assertEquals($expectedDate, $result);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage No valid start date.
     */
    public function testGetDateException()
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

        $mockHoliday = $this->getMockBuilder(NoHoliday::class)
            ->disableOriginalConstructor()
            ->getMock();

        // All days are holidays
        $mockHoliday->method('validate')->willReturn(false);

        $this->mockTimezone->method('scopeDate')->willReturn($createBaseDate());
        $this->mockConfig->method('getCutOffTime')->willReturn($cutOffTime);

        /** @var ShipmentDate $subject */
        $subject = $this->objectManager->create(
            ShipmentDate::class,
            [
                'timezone' => $this->mockTimezone,
                'config'          => $this->mockConfig,
                'dayValidators'   => [
                    $mockHoliday,
                ],
            ]
        );

        $subject->getDate($this->order->getStoreId());
    }
}
