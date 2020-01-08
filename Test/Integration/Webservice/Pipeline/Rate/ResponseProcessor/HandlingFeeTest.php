<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Rate\ResponseProcessor;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;

/**
 * HandlingFeeTest
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class HandlingFeeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MethodFactory
     */
    private $methodFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
        $this->methodFactory = $this->objectManager->create(MethodFactory::class);
    }

    /**
     * Test handling domestic fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_markup_domestic 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/type F
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/amount 3
     */
    public function processMethodsWithFixedDomesticHandlingFee()
    {
        /** @var RateRequest $request */
        $request = $this->objectManager->create(RateRequest::class);
        $request->setStoreId(1);
        $request->setCountryId('US');
        $request->setDestCountryId('US');

        $methodData = [
            'carrier' => 'foo',
            'carrier_title' => 'Foo Domestic',
            'method' => 'N',
            'method_title' => 'Markup Amount 3',
            'price' => 6.0,
            'cost' => 6.0,
        ];

        $method = $this->methodFactory->create(['data' => $methodData]);

        /** @var HandlingFee $handlingFee */
        $handlingFee = $this->objectManager->get(HandlingFee::class);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame($methodData['price'] + 3, $methods[0]->getPrice());
        self::assertSame($methodData['cost'], $methods[0]->getCost());
    }

    /**
     * Test handling international fee calculation with fixed handling fee.
     *
     * @test
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_markup_intl 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/intl_markup_group/type F
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/intl_markup_group/amount 3
     */
    public function processMethodsWithFixedInternationalHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setStoreId(1);
        $request->setCountryId('US');
        $request->setDestCountryId('DE');

        $methodData = [
            'carrier' => 'foo',
            'carrier_title' => 'Foo Intl',
            'method' => 'N',
            'method_title' => 'Markup Amount 3',
            'price' => 6.0,
            'cost' => 6.0,
        ];

        $method = $this->methodFactory->create(['data' => $methodData]);

        /** @var HandlingFee $handlingFee */
        $handlingFee = $this->objectManager->get(HandlingFee::class);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame($methodData['price'] + 3, $methods[0]->getPrice());
        self::assertSame($methodData['cost'], $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with percent handling fee.
     *
     * @test
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_markup_domestic 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/type P
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/percentage 50
     */
    public function processMethodsWithPercentHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setStoreId(1);
        $request->setCountryId('US');
        $request->setDestCountryId('US');

        $methodData = [
            'carrier' => 'foo',
            'carrier_title' => 'Foo Intl',
            'method' => 'N',
            'method_title' => 'Markup Percentage 50',
            'price' => 6.0,
            'cost' => 6.0,
        ];

        $method = $this->methodFactory->create(['data' => $methodData]);

        /** @var HandlingFee $handlingFee */
        $handlingFee = $this->objectManager->get(HandlingFee::class);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame($methodData['price'] * 1.5, $methods[0]->getPrice());
        self::assertSame($methodData['cost'], $methods[0]->getCost());
    }

    /**
     * Test handling fee calculation with fixed negative handling fee not dropping below 0.
     *
     * @test
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_markup_domestic 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/type F
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/domestic_markup_group/amount -10
     */
    public function processMethodsWithFixedNegativeHandlingFee()
    {
        $request = $this->objectManager->create(RateRequest::class);
        $request->setStoreId(1);
        $request->setCountryId('US');
        $request->setDestCountryId('US');

        $methodData = [
            'carrier' => 'foo',
            'carrier_title' => 'Foo Intl',
            'method' => 'N',
            'method_title' => 'Markup Amount -10',
            'price' => 6.0,
            'cost' => 6.0,
        ];

        $method = $this->methodFactory->create(['data' => $methodData]);

        /** @var HandlingFee $handlingFee */
        $handlingFee = $this->objectManager->get(HandlingFee::class);
        $methods = $handlingFee->processMethods([$method], $request);

        self::assertSame(0.0, $methods[0]->getPrice());
        self::assertSame($methodData['cost'], $methods[0]->getCost());
    }
}
