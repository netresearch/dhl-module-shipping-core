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
     * @test
     * @magentoConfigFixture current_store dhlshippingsolutions/dhlglobalwebservices/cod_methods payflow_advanced,payflow_link,payflowpro
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
     *
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoConfigFixture current_store dhlshippingsolutions/dhlglobalwebservices/terms_of_trade DTP/DDP
     * @magentoConfigFixture fixturestore_store dhlshippingsolutions/dhlglobalwebservices/terms_of_trade DDU/DAP
     */
    public function getTermsOfTrade()
    {
        self::assertEquals('DTP/DDP', $this->config->getTermsOfTrade());
        self::assertEquals('DDU/DAP', $this->config->getTermsOfTrade('fixturestore'));
    }

    /**
     * @test
     *
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoConfigFixture current_store dhlshippingsolutions/dhlglobalwebservices/cut_off_time 00,00,00
     * @magentoConfigFixture fixturestore_store dhlshippingsolutions/dhlglobalwebservices/cut_off_time 12,07,10
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
