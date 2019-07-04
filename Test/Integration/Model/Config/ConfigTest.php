<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Api\ConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * ConfigTest
 *
 * @package Dhl\ShippingCore\Test\Integration
 * @author  Ronny Gertler <ronny.gertler@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @magentoConfigFixture current_store dhlshippingsolutions/dhlglobalwebservices/cod_methods payflow_advanced,payflow_link,payflowpro
     */
    public function getCodMethods()
    {
        /** @var ConfigInterface $config */
        $config = Bootstrap::getObjectManager()->get(ConfigInterface::class);

        $paymentMethods = $config->getCodMethods();
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
     * @magentoConfigFixture current_store dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/terms_of_trade DTP/DDP
     * @magentoConfigFixture fixturestore_store dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/terms_of_trade DDU/DAP
     */
    public function getTermsOfTrade()
    {
        /** @var ConfigInterface $config */
        $config = Bootstrap::getObjectManager()->get(ConfigInterface::class);

        self::assertEquals('DTP/DDP', $config->getTermsOfTrade());
        self::assertEquals('DDU/DAP', $config->getTermsOfTrade('fixturestore'));
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
        /** @var ConfigInterface $config */
        $config = Bootstrap::getObjectManager()->get(ConfigInterface::class);

        self::assertEquals('00,00,00', $config->getCutOffTime());
        self::assertEquals('12,07,10', $config->getCutOffTime('fixturestore'));
    }
}
