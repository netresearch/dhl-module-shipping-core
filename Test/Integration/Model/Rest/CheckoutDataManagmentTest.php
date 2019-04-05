<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Api\ServiceSelectionRepositoryInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutData;
use Dhl\ShippingCore\Model\Rest\CheckoutDataManagement;
use Magento\TestFramework\ObjectManager;

/**
 * Class CheckoutDataManagmentTest
 *
 * @package Dhl\ShippingCore\Test\Model\Rest
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class CheckoutDataManagmentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    public function testGetData()
    {
        /** @var CheckoutDataManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutDataManagement::class);
        $result = $subject->getData('DE', '04229');

        self::assertInstanceOf(CheckoutData::class, $result);
    }

    public function testSetServiceSelection()
    {
        /** @var CheckoutDataManagementInterface $subject */
        $subject = $this->objectManager->create(CheckoutDataManagement::class);
        /** @var ServiceSelectionInterface $serviceSelection */
        $serviceSelection = $this->objectManager->create(
            ServiceSelectionInterface::class,
            [
                'serviceCode' => 'testServiceCode',
                'inputCode' => 'testInputCode',
                'value' => 'testValue',
            ]
        );
        $subject->setServiceSelection(12, [$serviceSelection]);

        /** @var ServiceSelectionRepositoryInterface $serviceSelectionRepo */
        $serviceSelectionRepo = $this->objectManager->get(ServiceSelectionRepositoryInterface::class);

        $this::markTestIncomplete(
            'Todo: Make sure there is a real quote with a quote address id.'
        );
        /** @var ServiceSelectionInterface $storedServiceSelection */
        $storedServiceSelection = $serviceSelectionRepo->getByQuoteAddressId(123)->getFirstItem();
        $this->assertEquals($storedServiceSelection->getValue(), $serviceSelection->getValue());
        $this->assertEquals($storedServiceSelection->getInputCode(), $serviceSelection->getInputCode());
        $this->assertEquals($storedServiceSelection->getServiceCode(), $serviceSelection->getServiceCode());
    }
}
