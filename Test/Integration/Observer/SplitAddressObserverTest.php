<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Observer;

use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Dhl\ShippingCore\Model\SplitAddress\RecipientStreet;
use Dhl\ShippingCore\Observer\SplitAddress;
use Dhl\ShippingCore\Test\Provider\StreetDataProvider;
use Magento\Framework\Event\InvokerInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Address;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * @magentoAppIsolation enabled
 */
class SplitAddressObserverTest extends \PHPUnit\Framework\TestCase
{
    const CARRIER_CODE = 'foo';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var InvokerInterface
     */
    private $invoker;

    /**
     * @var Observer
     */
    private $observer;

    /**
     * Init object manager
     */
    public function setUp()
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->invoker = $this->objectManager->get(InvokerInterface::class);
        $this->observer = $this->objectManager->get(Observer::class);

        $this->objectManager->configure(
            [
                SplitAddress::class => [
                    'arguments' => [
                        'carrierCodes' => [
                            self::CARRIER_CODE
                        ]
                    ],
                ],
            ]
        );
    }

    protected function tearDown()
    {
        $this->objectManager->removeSharedInstance(SplitAddress::class);
        $this->objectManager->removeSharedInstance(RecipientStreetResource::class);

        parent::tearDown();
    }

    /**
     * @return string[][][]
     */
    public function getStreetData(): array
    {
        return StreetDataProvider::getStreetData();
    }

    /**
     * Split street, positive case.
     *
     * If an order shipping address is persisted and its order is assigned to
     * a carrier which is registered for street splitting, then the address parts
     * must end up in a RecipientStreet entity.
     *
     * - the order address is the input for the observer/street splitter mechanism.
     * - the recipient street entity is the expected outcome of running the observer.
     *
     * @test
     * @dataProvider getStreetData
     *
     * @param string[] $street
     * @param string[] $expected
     */
    public function createRecipientStreetAndSave(array $street, array $expected)
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);

        $address->setEntityId(12000);
        $address->setStreet($street);
        $address->setAddressType('shipping');
        $address->getOrder()->setShippingMethod(sprintf('%s_%s', self::CARRIER_CODE, 'test'));

        $recipientStreet = $this->objectManager->create(
            RecipientStreet::class,
            [
                'data' => [
                    'order_address_id' => 12000,
                    'street' => $expected['street_name'],
                    'street_number' => $expected['street_number'],
                    'supplement' => $expected['supplement'],
                ],
            ]
        );

        $callback = function (RecipientStreet $address) use ($recipientStreet) {
            $arrayDiff = array_diff($address->getData(), $recipientStreet->getData());

            return empty($arrayDiff);
        };

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback($callback))
            ->willReturnSelf();

        $this->objectManager->addSharedInstance($resourceModelMock, RecipientStreetResource::class);

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhlgw_split_shipping_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);

        /** @var Logger $testLogger */
        $testLogger = $this->objectManager->get(LoggerInterface::class);
        self::assertEmpty($testLogger->getMessages());
    }

    /**
     * Split street, wrong address type.
     *
     * In case a *billing* address is updated, the street splitter must not
     * create a new RecipientStreet entity.
     *
     * @test
     */
    public function orderAddressHasWrongAddressType()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);

        $address->setEntityId(12000);
        $address->setStreet(['Nonnenstraße 11c']);
        $address->setAddressType('billing');
        $address->getOrder()->setShippingMethod(sprintf('%s_%s', self::CARRIER_CODE, 'test'));

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->never())
            ->method('save');

        $this->objectManager->addSharedInstance($resourceModelMock, RecipientStreetResource::class);

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhlgw_split_shipping_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);

        /** @var Logger $testLogger */
        $testLogger = $this->objectManager->get(LoggerInterface::class);
        self::assertEmpty($testLogger->getMessages());
    }

    /**
     * Split street, not registered carrier.
     *
     * If an order shipping address is persisted and its order is *not* assigned to
     * a carrier which is registered for street splitting, then the street splitter
     * must not create a new RecipientStreet entity.
     *
     * @test
     */
    public function carrierIsNotRegistered()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);

        $address->setEntityId(12000);
        $address->setStreet(['Nonnenstraße 11c']);
        $address->setAddressType('shipping');
        $address->getOrder()->setShippingMethod(sprintf('%s_%s', 'bar', 'test'));

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->never())
            ->method('save');

        $this->objectManager->addSharedInstance($resourceModelMock, RecipientStreetResource::class);

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhlgw_split_shipping_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);

        /** @var Logger $testLogger */
        $testLogger = $this->objectManager->get(LoggerInterface::class);
        self::assertEmpty($testLogger->getMessages());
    }

    /**
     * Observers must not throw exceptions. Make sure they are caught in the observer.
     *
     * @test
     */
    public function exceptionMustNotBubbleUp()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);

        $address->setEntityId(12000);
        $address->setStreet(['Nonnenstraße 11c']);
        $address->setAddressType('shipping');
        $address->getOrder()->setShippingMethod(sprintf('%s_%s', self::CARRIER_CODE, 'test'));

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception('MySQL server has gone away (error 2006)')));

        $this->objectManager->addSharedInstance($resourceModelMock, RecipientStreetResource::class);

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhlgw_split_shipping_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);

        /** @var Logger $testLogger */
        $testLogger = $this->objectManager->get(LoggerInterface::class);
        self::assertNotEmpty($testLogger->getMessages());
    }
}
