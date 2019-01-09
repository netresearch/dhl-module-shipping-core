<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Rate\Processor\FreeShipping;
use Magento\Catalog\Model\ProductFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;

/**
 * FreeShippingTest
 *
 * @package Dhl\Express\Test\Integration
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
class FreeShippingTest extends \PHPUnit\Framework\TestCase
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
     * @var RateRequest
     */
    private $request;

    /**
     * @var MethodFactory
     */
    private $methodFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

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
     * Test free shipping is disabled.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_enable 0
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_products N
     */
    public function processMethodsFreeShippingDisabled()
    {
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

        $freeShipping = new FreeShipping($this->config);
        $methods = $freeShipping->processMethods([$method], $this->request, 'foo');

        self::assertSame(6.0, $methods[0]->getPrice());
        self::assertSame(6.0, $methods[0]->getCost());
    }

    /**
     * Test free shipping fee (domestic) calculation with large enough sub total value.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/free_shipping_virtual_products_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_products N
     */
    public function processMethodsDomesticWithLargerSubtotal()
    {
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

        // Total amount of order
        $this->request->setBaseSubtotalInclTax(100.0);

        $freeShipping = new FreeShipping($this->config);
        $methods = $freeShipping->processMethods([$method], $this->request, 'foo');

        // Limit reached, shipping price should be zero
        self::assertSame(0.0, $methods[0]->getPrice());
        self::assertSame(0.0, $methods[0]->getCost());
    }

    /**
     * Test free shipping (international) fee calculation with large enough sub total value.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/free_shipping_virtual_products_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_products X
     */
    public function processMethodsInternationalWithLargerSubtotal()
    {
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

        // Total amount of order
        $this->request->setBaseSubtotalInclTax(100.0);

        $freeShipping = new FreeShipping($this->config);
        $methods = $freeShipping->processMethods([$method], $this->request, 'foo');

        // Limit reached, shipping price should be zero
        self::assertSame(0.0, $methods[0]->getPrice());
        self::assertSame(0.0, $methods[0]->getCost());
    }

    /**
     * Test free shipping fee calculation with not enough sub total value.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/free_shipping_virtual_products_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_products N
     */
    public function processMethodsWithNotEnoughSubtotal()
    {
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

        // Total amount of order
        $this->request->setBaseSubtotalInclTax(10.0);

        $freeShipping = new FreeShipping($this->config);
        $methods = $freeShipping->processMethods([$method], $this->request, 'foo');

        // Price should be the same as initial provided
        self::assertSame(6.0, $methods[0]->getPrice());
        self::assertSame(6.0, $methods[0]->getCost());
    }

    /**
     * Test free shipping fee calculation without included virtual products.
     *
     * @test
     *
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/free_shipping_virtual_products_enable 0
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/international_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_subtotal 50
     * @magentoConfigFixture current_store carriers/foo/free_shipping_settings/domestic_free_shipping_products N
     */
    public function processMethodsWithVirtualProducts()
    {
        $product1 = $this->productFactory->create();
        $product1->setTypeId('virtual');

        $product2 = $this->productFactory->create();
        $product2->setTypeId('simple');

        /** @var \Magento\Quote\Model\Quote\Item $item1 */
        $item1 = $this->objectManager->create('Magento\Quote\Model\Quote\Item');
        $item1->setProduct($product1)
              ->setBasePriceInclTax(20);

        /** @var \Magento\Quote\Model\Quote\Item $item2 */
        $item2 = $this->objectManager->create('Magento\Quote\Model\Quote\Item');
        $item2->setProduct($product2)
              ->setBasePriceInclTax(40);

        $this->request->setAllItems(
            [
                $item1,
                $item2,
            ]
        );

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

        $freeShipping = new FreeShipping($this->config);
        $methods = $freeShipping->processMethods([$method], $this->request, 'foo');

        // Price should be the same as initial provided
        self::assertSame(6.0, $methods[0]->getPrice());
        self::assertSame(6.0, $methods[0]->getCost());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();

        $this->config = $this->objectManager->create(RateConfigInterface::class);
        $this->request = $this->objectManager->create(RateRequest::class);
        $this->productFactory = $this->objectManager->create(ProductFactory::class);
        $this->methodFactory = $this->objectManager->create(MethodFactory::class);
    }
}
