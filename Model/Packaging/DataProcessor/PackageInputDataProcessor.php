<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Config\Source\ExportContentType;
use Dhl\ShippingCore\Model\Config\Source\TermsOfTrade;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\ItemAttributeReader;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackageInputDataProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackageInputDataProcessor extends AbstractProcessor
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ItemAttributeReader
     */
    private $itemAttributeReader;

    /**
     * @var TermsOfTrade
     */
    private $termsOfTradeSource;

    /**
     * @var ExportContentType
     */
    private $contentTypeSource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * PackageInputDataProcessor constructor.
     *
     * @param ConfigInterface $config
     * @param ItemAttributeReader $itemAttributeReader
     * @param TermsOfTrade $termsOfTradeSource
     * @param ExportContentType $contentTypeSource
     * @param CommentInterfaceFactory $commentFactory
     */
    public function __construct(
        ConfigInterface $config,
        ItemAttributeReader $itemAttributeReader,
        TermsOfTrade $termsOfTradeSource,
        ExportContentType $contentTypeSource,
        CommentInterfaceFactory $commentFactory
    ) {
        $this->config = $config;
        $this->itemAttributeReader = $itemAttributeReader;
        $this->termsOfTradeSource = $termsOfTradeSource;
        $this->contentTypeSource = $contentTypeSource;
        $this->commentFactory = $commentFactory;
    }

    /**
     * Set options and values to inputs on package level.
     *
     * @param ShippingOptionInterface $shippingOption
     * @param Shipment $shipment
     */
    private function processInputs(ShippingOptionInterface $shippingOption, Shipment $shipment)
    {
        $defaultPackage = $this->config->getOwnPackagesDefault((string) $shipment->getStoreId());

        foreach ($shippingOption->getInputs() as $input) {
            switch ($input->getCode()) {
                // shipping product
                case 'productCode':
                    $value = substr(strrchr((string) $shipment->getOrder()->getShippingMethod(), "_"), 1);
                    $label = $shipment->getOrder()->getShippingDescription();
                    $input->setOptions([['value' => $value, 'label' => $label,]]);
                    $input->setDefaultValue($value);
                    break;
                // weight
                case 'weight':
                    $totalWeight = $this->itemAttributeReader->getTotalWeight($shipment);
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue((string) $totalWeight);
                    break;
                case 'weightUnit':
                    $weightUnit = $this->config->getWeightUnit($shipment->getStoreId()) === 'kg'
                        ? \Zend_Measure_Weight::KILOGRAM
                        : \Zend_Measure_Weight::POUND;
                    $input->setDefaultValue($weightUnit);
                    break;
                // dimensions
                case 'width':
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWidth() : '');
                    break;
                case 'height':
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getHeight() : '');
                    break;
                case 'length':
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getLength() : '');
                    break;
                case 'sizeUnit':
                    $dimensionsUnit = $this->config->getDimensionUnit($shipment->getStoreId()) === 'cm'
                        ? \Zend_Measure_Length::CENTIMETER
                        : \Zend_Measure_Length::INCH;
                    $input->setDefaultValue($dimensionsUnit);
                    break;
                // customs
                case 'customsValue':
                    $price = $this->itemAttributeReader->getTotalPrice($shipment);
                    $currency = $shipment->getStore()->getBaseCurrency();
                    $currencySymbol = $currency->getCurrencySymbol() ?: $currency->getCode();
                    $comment = $this->commentFactory->create();
                    $comment->setContent($currencySymbol);
                    $input->setComment($comment);
                    $input->setDefaultValue((string) number_format($price, 2));
                    break;
                case 'exportDescription':
                    $exportDescriptions = $this->itemAttributeReader->getPackageExportDescriptions($shipment);
                    $exportDescription = implode(' ', $exportDescriptions);
                    $input->setDefaultValue(substr($exportDescription, 0, 80));
                    break;
                case 'termsOfTrade':
                    $input->setOptions($this->termsOfTradeSource->toOptionArray());
                    break;
                case 'contentType':
                    $input->setOptions($this->contentTypeSource->toOptionArray());
                    break;
            }
        }
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_PACKAGE) {
            return $optionsData;
        }

        foreach ($optionsData as $optionGroup) {
            $this->processInputs($optionGroup, $shipment);
        }

        return $optionsData;
    }
}
