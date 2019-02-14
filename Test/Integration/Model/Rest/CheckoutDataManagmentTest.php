<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
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
        $result = $subject->getData('0', 'DE', '04229');

        self::assertInstanceOf(CheckoutDataInterface::class, $result);
    }
}
