<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Config;

use Magento\TestFramework\ObjectManager;

/**
 * ModuleConfigTest
 *
 * @package Dhl\ShippingCore\Test\Integration
 * @author  Ronny Gertler <ronny.gertler@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
class CoreConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var CoreConfigInterface
     */
    private $config;

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
     * @test
     * @magentoConfigFixture current_store shipping/dhlglobalwebservices/cod_methods payflow_advanced,payflow_link,payflowpro
     */
    public function getCodMethods()
    {
        $paymentMethods = $this->config->getCodMethods();
        self::assertInternalType('array', $paymentMethods);
        self::assertNotEmpty($paymentMethods);
        self::assertContainsOnly('string', $paymentMethods);
        self::assertCount(3, $paymentMethods);
        self::assertContains('payflow_advanced', $paymentMethods);
        self::assertContains('payflow_link', $paymentMethods);
        self::assertContains('payflowpro', $paymentMethods);
    }

    /**
     * @test
     * @magentoConfigFixture current_store shipping/dhlglobalwebservices/terms_of_trade DTP/DDP
     * @magentoConfigFixture fixturestore_store shipping/dhlglobalwebservices/terms_of_trade DDU/DAP
     */
    public function getTermsOfTrade()
    {
        self::assertEquals('DTP/DDP', $this->config->getTermsOfTrade());
        self::assertEquals('DDU/DAP', $this->config->getTermsOfTrade('fixturestore'));
    }

    /**
     * @test
     * @magentoConfigFixture current_store shipping/dhlglobalwebservices/cut_off_time 00,00,00
     * @magentoConfigFixture fixturestore_store shipping/dhlglobalwebservices/cut_off_time 12,07,10
     */
    public function getCutOffTime()
    {
        self::assertEquals('00,00,00', $this->config->getCutOffTime());
        self::assertEquals('12,07,10', $this->config->getCutOffTime('fixturestore'));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();

        $this->config = $this->objectManager->create(CoreConfig::class);
    }
}
