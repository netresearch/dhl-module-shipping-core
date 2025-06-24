<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate\ResponseProcessor;

use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\TestFramework\ObjectManager;

class RoundedPricesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var RoundedPrices
     */
    private $roundedPrices;

    #[\Override]
    protected function setUp(): void
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->roundedPrices = $this->objectManager->create(RoundedPrices::class);

        parent::setUp();
    }

    /**
     * @return mixed[]
     */
    public static function rateMethodProvider(): array
    {
        $methodPrices = [
            'free' => ['original' => 0.0, 'up' => 0.0, 'down' => 0.0, 'decimal_up' => 0.95, 'decimal_down' => 0.0],
            '3.456' => ['original' => 3.456, 'up' => 4.0, 'down' => 3.0, 'decimal_up' => 3.95, 'decimal_down' => 2.95],
            '5.55' => ['original' => 5.55, 'up' => 6.0, 'down' => 5.0, 'decimal_up' => 5.95, 'decimal_down' => 4.95],
            '9.99' => ['original' => 9.99, 'up' => 10.0, 'down' => 9.0, 'decimal_up' => 10.95, 'decimal_down' => 9.95],
            '9.95' => ['original' => 9.95, 'up' => 10.0, 'down' => 9.0, 'decimal_up' => 9.95, 'decimal_down' => 9.95],
            '10.00' => ['original' => 10, 'up' => 10.0, 'down' => 10.0, 'decimal_up' => 10.95, 'decimal_down' => 9.95],
        ];

        $data = [];
        foreach ($methodPrices as $id => $prices) {
            $data[$id] = [$prices];
        }

        return $data;
    }

    /**
     * Create method object with given price data
     *
     * @param float[] $prices
     * @return Method
     */
    private function createMethod(array $prices): Method
    {
        /** @var MethodFactory $methodFactory */
        $methodFactory = $this->objectManager->get(MethodFactory::class);
        
        $methodData = [
            'carrier' => 'foo',
            'carrier_title' => 'Foo Carrier',
            'method' => 'X',
            'method_title' => 'Foo Method',
            'price' => $prices['original'],
            'cost' => $prices['original'],
        ];

        return $methodFactory->create(['data' => $methodData]);
    }

    /**
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_rounding 0
     *
     * @param float[] $prices
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rateMethodProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function processMethodsWithNoRounding(array $prices)
    {
        $method = $this->createMethod($prices);
        $methods = $this->roundedPrices->processMethods([$method]);

        self::assertSame($prices['original'], $methods[0]->getData('price'));
        self::assertSame($prices['original'], $methods[0]->getData('cost'));
    }

    /**
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_rounding 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/number_format integer
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/direction up
     *
     * @param float[] $prices
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rateMethodProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function processMethodsRoundUpToInteger(array $prices)
    {
        $method = $this->createMethod($prices);
        $methods = $this->roundedPrices->processMethods([$method]);

        self::assertSame($prices['up'], $methods[0]->getData('price'));
        self::assertSame($prices['original'], $methods[0]->getData('cost'));
    }

    /**
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_rounding 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/number_format integer
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/direction down
     *
     * @param float[] $prices
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rateMethodProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function processMethodsRoundDownToInteger(array $prices)
    {
        $method = $this->createMethod($prices);
        $methods = $this->roundedPrices->processMethods([$method]);

        self::assertSame($prices['down'], $methods[0]->getData('price'));
        self::assertSame($prices['original'], $methods[0]->getData('cost'));
    }

    /**
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_rounding 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/number_format decimal
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/direction up
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/decimal_value 95
     *
     * @param float[] $prices
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rateMethodProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function processMethodsRoundUpToStaticDecimal(array $prices)
    {
        $method = $this->createMethod($prices);
        $methods = $this->roundedPrices->processMethods([$method]);

        self::assertSame($prices['decimal_up'], $methods[0]->getData('price'));
        self::assertSame($prices['original'], $methods[0]->getData('cost'));
    }

    /**
     *
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/use_rounding 1
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/number_format decimal
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/direction down
     * @magentoConfigFixture default_store dhlshippingsolutions/foo/rates_calculation/rounding_group/decimal_value 95
     *
     * @param float[] $prices
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rateMethodProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function processMethodsRoundDownToStaticDecimal(array $prices)
    {
        $method = $this->createMethod($prices);
        $methods = $this->roundedPrices->processMethods([$method]);

        self::assertSame($prices['decimal_down'], $methods[0]->getData('price'));
        self::assertSame($prices['original'], $methods[0]->getData('cost'));
    }
}
