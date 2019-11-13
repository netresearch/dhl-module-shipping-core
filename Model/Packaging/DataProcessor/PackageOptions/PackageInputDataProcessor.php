<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor\PackageOptions;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Config\Source\ExportContentType;
use Dhl\ShippingCore\Model\Config\Source\TermsOfTrade;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\ShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Packaging\ShipmentItemAttributeReader;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackageInputDataProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackageInputDataProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ShipmentItemAttributeReader
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
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * PackageInputDataProcessor constructor.
     *
     * @param ConfigInterface $config
     * @param ShipmentItemAttributeReader $itemAttributeReader
     * @param TermsOfTrade $termsOfTradeSource
     * @param ExportContentType $contentTypeSource
     * @param CommentInterfaceFactory $commentFactory
     * @param OptionInterfaceFactory $optionFactory
     */
    public function __construct(
        ConfigInterface $config,
        ShipmentItemAttributeReader $itemAttributeReader,
        TermsOfTrade $termsOfTradeSource,
        ExportContentType $contentTypeSource,
        CommentInterfaceFactory $commentFactory,
        OptionInterfaceFactory $optionFactory
    ) {
        $this->config = $config;
        $this->itemAttributeReader = $itemAttributeReader;
        $this->termsOfTradeSource = $termsOfTradeSource;
        $this->contentTypeSource = $contentTypeSource;
        $this->commentFactory = $commentFactory;
        $this->optionFactory = $optionFactory;
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
                    $option = $this->optionFactory->create();
                    $value = substr(strrchr((string) $shipment->getOrder()->getShippingMethod(), '_'), 1);
                    $option->setValue($value);
                    $option->setLabel(
                        $shipment->getOrder()->getShippingDescription()
                    );
                    $input->setOptions([$option]);
                    $input->setDefaultValue($value);
                    break;

                case 'packagingWeight':
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWeight() : '');
                    break;

                // weight
                case 'weight':
                    $itemTotalWeight = $this->itemAttributeReader->getTotalWeight($shipment);
                    $packagingWeight = $defaultPackage ? $defaultPackage->getWeight() : 0;
                    $totalWeight = $itemTotalWeight + $packagingWeight;
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

                case 'dgCategory':
                    $dgCategories = $this->itemAttributeReader->getPackageDgCategories($shipment);
                    $input->setDefaultValue(implode(', ', $dgCategories));
                    break;

                case 'termsOfTrade':
                    $input->setOptions(
                        array_map(
                            function ($optionArray) {
                                $option = $this->optionFactory->create();
                                $option->setValue($optionArray['value']);
                                $option->setLabel((string)$optionArray['label']);
                                return $option;
                            },
                            $this->termsOfTradeSource->toOptionArray()
                        )
                    );
                    break;

                case 'contentType':
                    $input->setOptions(
                        array_map(
                            function ($optionArray) {
                                $option = $this->optionFactory->create();
                                $option->setValue($optionArray['value']);
                                $option->setLabel((string)$optionArray['label']);
                                return $option;
                            },
                            $this->contentTypeSource->toOptionArray()
                        )
                    );
                    break;
            }
        }
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, Shipment $shipment): array
    {
        foreach ($optionsData as $optionGroup) {
            $this->processInputs($optionGroup, $shipment);
        }

        return $optionsData;
    }
}
