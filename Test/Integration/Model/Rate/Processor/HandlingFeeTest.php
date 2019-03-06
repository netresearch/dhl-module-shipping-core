<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;

/**
 * HandlingFeeTest
 *
 * @package Dhl\Express\Test\Integration
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
class HandlingFeeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var RateConfigInterface
     */
    private $config;

    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * Config fixtures are loaded before data fixtures. Config fixtures for
     * non-existent stores will fail. We need to set the stores up first manually.
     *
     * @link http://magento.stackexchange.com/a/93961
     */
    public static function setUpBeforeClass()
    {
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore_rollback.php');
        require realpath(
            TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore_rollback.php'
        );

        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore.php');
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore.php');

        parent::setUpBeforeClass();
    }

    /**
     * Delete manually added stores.
     *
     * @see setUpBeforeClass()
     */
    public static function tearDownAfterClass()
    {
        require realpath(TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_fixturestore_rollback.php');
        require realpath(
            TESTS_TEMP_DIR . '/../testsuite/Magento/Store/_files/core_second_third_fixturestore_rollback.php'
        );

        parent::tearDownAfterClass();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
        $this->config        = $this->objectManager->create(RateConfigInterface::class);
        $this->methodFactory = $this->objectManager->create(MethodFactory::class);
    }

    /**
     * Test handling domestic fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_affect_rates 1
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_type F
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_fee_fixed 3
     */
    public function processMethodsWithFixedDomesticHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setOrigCountryId('US');
        $request->setDestCountryId('US');

        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier' => 'foo',
                    'carrier_title' => 'TEST',
                    'method' => 'N',
                    'method_title' => 'LABEL',
                    'price' => 6.0,
                    'cost' => 6.0,
                ],
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling international fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/international_affect_rates 1
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/international_handling_type F
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/international_handling_fee_fixed 3
     */
    public function processMethodsWithFixedInternationalHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setOrigCountryId('US');
        $request->setDestCountryId('DE');

        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier' => 'foo',
                    'carrier_title' => 'TEST',
                    'method' => 'X',
                    'method_title' => 'LABEL',
                    'price' => 6.0,
                    'cost' => 6.0,
                ],
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with percent handling fee.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_affect_rates 1
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_type P
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_fee_percentage 50
     */
    public function processMethodsWithPercentHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setOrigCountryId('US');
        $request->setDestCountryId('US');

        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier' => 'foo',
                    'carrier_title' => 'TEST',
                    'method' => 'N',
                    'method_title' => 'LABEL',
                    'price' => 6.0,
                    'cost' => 6.0,
                ],
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame(9.0, $methods[0]->getPrice());
        self::assertSame(9.0, $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with fixed negative handling fee not dropping below 0.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_affect_rates 1
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_type F
     * @magentoConfigFixture current_store carriers/foo/shipping_markup/domestic_handling_fee_fixed -10
     */
    public function processMethodsWithFixedNegativeHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setOrigCountryId('US');
        $request->setDestCountryId('US');

        $method = $this->methodFactory->create(
            [
                'data' => [
                    'carrier' => 'foo',
                    'carrier_title' => 'TEST',
                    'method' => 'N',
                    'method_title' => 'LABEL',
                    'price' => 6.0,
                    'cost' => 6.0,
                ],
            ]
        );

        $handlingFee = new HandlingFee($this->config);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame(0.0, $methods[0]->getPrice());
        self::assertSame(0.0, $methods[0]->getCost());
    }
}
