<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Unit\Model\Checkout\CheckoutDataProcessor;

use Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor\RouteProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RouteProcessorTest
 *
 * @package Dhl\ShippingCore\Test\Unit\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class RouteProcessorTest extends TestCase
{
    /**
     * @var string This data has two shipping options - one only for DE routes and one only for US routes
     */
    private $checkoutData = '{"carriers":[{"carrierCode":"testCarrier","packageLevelOptions":[{"code":"deOption","routes":[{"origin":"de","destinations":["de","at"]}],"inputs":[{"code":"deTest","label":"DETest","inputType":"text"}]},{"code":"usOption","routes":[{"origin":"us"}],"inputs":[{"inputType":"text","code":"usTest","label":"USTEST"}]}]}]}';

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfig;

    protected function setUp()
    {
        parent::setUp();

        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function dataProvider(): array
    {
        return [
            'US shipping origin' => [
                'checkoutData' => \json_decode($this->checkoutData, true),
                'countryId' => 'TW',
                'postalCode' => '11111',
                'shippingOrigin' => 'US',
            ],
            'DE shipping origin' => [
                'checkoutData' => \json_decode($this->checkoutData, true),
                'countryId' => 'AT',
                'postalCode' => '11111',
                'shippingOrigin' => 'DE'
            ],
        ];
    }

    /**
     * @param array $checkoutData
     * @param string $countryId
     * @param string $postalCode
     * @param string $shippingOrigin
     *
     * @dataProvider dataProvider
     */
    public function testProcess(array $checkoutData, string $countryId, string $postalCode, string $shippingOrigin)
    {
        $this->scopeConfig->method('getValue')->willReturn($shippingOrigin);

        $subject = new RouteProcessor($this->scopeConfig);

        $result = $subject->process($checkoutData, $countryId, $postalCode);

        $options = $result['carriers'][0]['packageLevelOptions'];

        self::assertCount(1, $options);
        if ($countryId === 'US') {
            self::assertSame('usOption', $options[0]['code']);
        }
        if ($countryId === 'DE') {
            self::assertSame('deOption', $options[0]['code']);
        }
    }
}
