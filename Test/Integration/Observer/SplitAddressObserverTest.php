<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Model\RecipientStreet;
use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Magento\Framework\Event\InvokerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Address;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

/**
 * SplitAddressObserverTest
 *
 * @magentoAppIsolation enabled
 *
 * @package Dhl\ShippingCore\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class SplitAddressObserverTest extends \PHPUnit\Framework\TestCase
{
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
                            'testCarrier'
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
     *
     * @test
     */
    public function createRecipientStreetAndSave()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);
        $address->setParentId(12000);
        $address->setStreet('Nonnenstraße 11d');
        $address->setAddressType('shipping');
        $address->getOrder()->setShippingMethod('testCarrier_test');

        $orderAddress = $this->objectManager->create(
            RecipientStreet::class,
            [
                'data' => [
                    'order_address_id' => 12000,
                    'street' => 'Nonnenstraße',
                    'street_number' => '11d',
                    'supplement' => '',
                ],
            ]
        );

        $callback = function (RecipientStreet $address) use ($orderAddress) {
            $arrayDiff = array_diff($address->getData(), $orderAddress->getData());

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
            'name' => 'dhl_split_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     *
     * @test
     */
    public function orderAddressHasWrongAddressType()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);
        $address->setAddressType('billing');

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->never())
            ->method('save');

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhl_split_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);
    }

    /**
     * @test
     */
    public function recipientStreetSaveThrowsException()
    {
        /** @var Address $address */
        $address = $this->objectManager->create(Address::class);
        $address->setParentId(12000);
        $address->setStreet('Nonnenstraße 11d');
        $address->setAddressType('shipping');
        $address->getOrder()->setShippingMethod('testCarrier_test');

        $resourceModelMock = $this->createMock(RecipientStreetResource::class);
        $resourceModelMock
            ->expects($this->once())
            ->method('save')
            ->will($this->throwException(new CouldNotSaveException(__('Unable to save recipient street.'))));

        $this->objectManager->addSharedInstance($resourceModelMock, RecipientStreetResource::class);

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Unable to save recipient street.');

        $config = [
            'instance' => SplitAddress::class,
            'name' => 'dhl_split_address',
        ];

        $this->observer->setData(['address' => $address]);
        $this->invoker->dispatch($config, $this->observer);
    }
}
