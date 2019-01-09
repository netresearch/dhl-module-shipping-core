<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\Express\Model\Carrier\Express;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;

class RoundedPricesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var RoundedPrices
     */
    private $roundedPrices;

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

    protected function setUp()
    {

        $this->objectManager = ObjectManager::getInstance();

        $this->roundedPrices = $this->objectManager->create(RoundedPrices::class);
        parent::setUp();
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_format no_rounding
     * @dataProvider provideTestRateMethods
     * @param Method[] $methods
     */
    public function processMethodsWithNoRounding(array $methods)
    {
        $method = $this->roundedPrices->processMethods($methods['0']);
        $this->assertSame(0.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['5.55']);
        $this->assertSame(5.55, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['9.99']);
        $this->assertSame(9.99, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['10']);
        $this->assertSame(10.00, $method[0]->getPrice());
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_format full_price
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_mode round_up
     * @dataProvider provideTestRateMethods
     * @param Method[] $methods
     */
    public function processMethodsRoundUpFullPrice(array $methods)
    {
        $method = $this->roundedPrices->processMethods($methods['0']);
        $this->assertSame(0.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['5.55']);
        $this->assertSame(6.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['9.99']);
        $this->assertSame(10.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['10']);
        $this->assertSame(10.00, $method[0]->getPrice());
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_mode round_off
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_format full_price
     * @dataProvider provideTestRateMethods
     * @param Method[] $methods
     */
    public function processMethodsRoundOffFullPrice(array $methods)
    {
        $method = $this->roundedPrices->processMethods($methods['0']);
        $this->assertSame(0.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['5.55']);
        $this->assertSame(5.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['9.99']);
        $this->assertSame(9.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['10']);
        $this->assertSame(10.00, $method[0]->getPrice());
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_mode round_up
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_format static_decimal
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_static_decimal 95
     * @dataProvider provideTestRateMethods
     * @param Method[] $methods
     */
    public function processMethodsRoundUpToStaticDecimal(array $methods)
    {
        $method = $this->roundedPrices->processMethods($methods['0']);
        $this->assertSame(0.95, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['5.55']);
        $this->assertSame(5.95, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['9.99']);
        $this->assertSame(10.95, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['10']);
        $this->assertSame(10.95, $method[0]->getPrice());
    }

    /**
     * @test
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_mode round_off
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_format static_decimal
     * @magentoConfigFixture current_store carriers/foo/checkout_settings/round_prices_static_decimal 95
     * @dataProvider provideTestRateMethods
     * @param Method[] $methods
     */
    public function processMethodsRoundOffToStaticDecimal(array $methods)
    {
        $method = $this->roundedPrices->processMethods($methods['0']);
        $this->assertSame(0.00, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['5.55']);
        $this->assertSame(4.95, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['9.99']);
        $this->assertSame(9.95, $method[0]->getPrice());

        $method = $this->roundedPrices->processMethods($methods['10']);
        $this->assertSame(9.95, $method[0]->getPrice());
    }

    /**
     * @return Method[]
     */
    public function provideTestRateMethods(): array
    {
        $methodFactory = ObjectManager::getInstance()->create(MethodFactory::class);
        $methods = [];
        $methods['0'] = [
            $methodFactory->create(
                [
                    'data' => [
                        'carrier' => 'foo',
                        'carrier_title' => 'DHL EXPRESS',
                        'method' => 'X',
                        'method_title' => 'foo',
                        'price' => 0,
                        'cost' => 0,
                    ],
                ]
            ),
        ];
        $methods['5.55'] = [
            $methodFactory->create(
                [
                    'data' => [
                        'carrier' => 'foo',
                        'carrier_title' => 'DHL EXPRESS',
                        'method' => 'X',
                        'method_title' => 'foo',
                        'price' => 5.55,
                        'cost' =>5.55 ,
                    ],
                ]
            ),
        ];
        $methods['9.99'] = [
            $methodFactory->create(
                [
                    'data' => [
                        'carrier' => 'foo',
                        'carrier_title' => 'DHL EXPRESS',
                        'method' => 'X',
                        'method_title' => 'foo',
                        'price' => 9.99,
                        'cost' => 9.99,
                    ],
                ]
            ),
        ];
        $methods['10'] = [
            $methodFactory->create(
                [
                    'data' => [
                        'carrier' => 'foo',
                        'carrier_title' => 'DHL EXPRESS',
                        'method' => 'X',
                        'method_title' => 'foo',
                        'price' => 10,
                        'cost' => 10,
                    ],
                ]
            ),
        ];

        return [
            'methods' => [$methods]
        ];
    }
}
