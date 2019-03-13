<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Util;

use Magento\TestFramework\ObjectManager;
use Magento\Directory\Model\CurrencyFactory;

/**
 * HandlingFeeTest
 *
 * @package Dhl\ShippingCore\Test\Integration
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
class UnitConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * prepare object manager, add mocks
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();

        $rateUsdToEur = 0.9377;
        $rateGbpToEur = 1.1723;
        $rateGbpToUsd = 1.2494;

        $currencyMock = $this->getMockBuilder(\Magento\Directory\Model\Currency::class)
            ->setMethods(['getRate'])
            ->disableOriginalConstructor()
            ->getMock();
        $currencyMock
            ->expects($this->any())
            ->method('getRate')
            ->willReturnOnConsecutiveCalls($rateUsdToEur, $rateGbpToEur, $rateGbpToUsd);

        $currencyFactoryMock = $this->getMockBuilder(CurrencyFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $currencyFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($currencyMock);

        $carrierHelper = $this->objectManager->get(\Magento\Shipping\Helper\Carrier::class);
        $directoryHelper = $this->objectManager->create(\Magento\Directory\Helper\Data::class, [
            'currencyFactory' => $currencyFactoryMock,
        ]);

        $this->unitConverter = $this->objectManager->create(UnitConverter::class, [
            'currencyConverter' => $directoryHelper,
            'unitConverter' => $carrierHelper,
        ]);
    }

    /**
     * @test
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    public function convertDimension()
    {
        $valueInKm = 1;
        $valueInM = 1000;
        $valueInInches = 39370.079;

        $conversionResult = $this->unitConverter->convertDimension(
            $valueInKm,
            \Zend_Measure_Length::KILOMETER,
            \Zend_Measure_Length::METER
        );
        $this->assertEquals($valueInM, $conversionResult);

        $conversionResult = $this->unitConverter->convertDimension(
            $valueInKm,
            \Zend_Measure_Length::KILOMETER,
            \Zend_Measure_Length::INCH
        );
        $this->assertEquals($valueInInches, $conversionResult);
    }

    /**
     * @test
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    public function convertMoney()
    {
        $valueInEur = 1;
        $valueInUsd = 1.066;
        $valueInGbp = 0.853;

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInUsd, 'USD', 'EUR');
        $this->assertEquals($valueInEur, $conversionResult);

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInGbp, 'GBP', 'EUR');
        $this->assertEquals($valueInEur, $conversionResult);

        $conversionResult = $this->unitConverter->convertMonetaryValue($valueInGbp, 'GBP', 'USD');
        $this->assertEquals($valueInUsd, $conversionResult);
    }

    /**
     * @test
     * @magentoConfigFixture default_store general/locale/code en_US
     */
    public function convertWeight()
    {
        $valueInKg = '1';
        $valueInG = '1000';
        $valueInLbs = '2.205';

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInKg,
            \Zend_Measure_Weight::KILOGRAM,
            \Zend_Measure_Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInKg,
            \Zend_Measure_Weight::KILOGRAM,
            \Zend_Measure_Weight::LBS
        );
        $this->assertEquals($valueInLbs, $conversionResult);
    }

    /**
     * @test
     * @magentoConfigFixture default_store general/locale/code de_DE
     */
    public function handleDeLocaleWithPointSeparator()
    {
        $lang = getenv('HTTP_ACCEPT_LANGUAGE');

        putenv('HTTP_ACCEPT_LANGUAGE=de-DE');

        $valueInLbs = '3.2';
        $valueInG = 1451.496;

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInLbs,
            \Zend_Measure_Weight::LBS,
            \Zend_Measure_Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        if ($lang) {
            putenv("HTTP_ACCEPT_LANGUAGE=$lang");
        } else {
            putenv('HTTP_ACCEPT_LANGUAGE');
        }
    }

    /**
     * @test
     * @magentoConfigFixture default_store general/locale/code de_DE
     */
    public function handleDeLocaleWithCommaSeparator()
    {
        $lang = getenv('HTTP_ACCEPT_LANGUAGE');

        putenv('HTTP_ACCEPT_LANGUAGE=de-DE');

        $valueInLbs = 3.2;
        $valueInG = 1451.496;

        $conversionResult = $this->unitConverter->convertWeight(
            $valueInLbs,
            \Zend_Measure_Weight::LBS,
            \Zend_Measure_Weight::GRAM
        );
        $this->assertEquals($valueInG, $conversionResult);

        if ($lang) {
            putenv("HTTP_ACCEPT_LANGUAGE=$lang");
        } else {
            putenv('HTTP_ACCEPT_LANGUAGE');
        }
    }
}
