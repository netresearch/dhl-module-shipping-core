<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Config\Source\ExportContentType;
use Dhl\ShippingCore\Model\Config\Source\TermsOfTrade;
use Dhl\ShippingCore\Model\ItemAttribute\ShipmentItemAttributeReader;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Codes;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class PackageInputDataProcessor
 *
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
     * @param ShipmentInterface $shipment
     */
    private function processInputs(ShippingOptionInterface $shippingOption, ShipmentInterface $shipment)
    {
        $defaultPackage = $this->config->getOwnPackagesDefault((string) $shipment->getStoreId());

        foreach ($shippingOption->getInputs() as $input) {
            switch ($input->getCode()) {
                // shipping product
                case Codes::PACKAGING_INPUT_PRODUCT_CODE:
                    $option = $this->optionFactory->create();
                    $value = substr(strrchr((string) $shipment->getOrder()->getShippingMethod(), '_'), 1);
                    $option->setValue($value);
                    $option->setLabel(
                        $shipment->getOrder()->getShippingDescription()
                    );
                    $input->setOptions([$option]);
                    $input->setDefaultValue($value);
                    break;

                case Codes::PACKAGING_INPUT_PACKAGING_WEIGHT:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWeight() : '');
                    break;

                // weight
                case Codes::PACKAGING_INPUT_WEIGHT:
                    $itemTotalWeight = $this->itemAttributeReader->getTotalWeight($shipment);
                    $packagingWeight = $defaultPackage ? $defaultPackage->getWeight() : 0;
                    $totalWeight = $itemTotalWeight + $packagingWeight;
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getWeightUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue((string) $totalWeight);
                    break;

                case Codes::PACKAGING_INPUT_WEIGHT_UNIT:
                    $weightUnit = $this->config->getWeightUnit($shipment->getStoreId()) === 'kg'
                        ? \Zend_Measure_Weight::KILOGRAM
                        : \Zend_Measure_Weight::POUND;
                    $input->setDefaultValue($weightUnit);
                    break;

                // dimensions
                case Codes::PACKAGING_INPUT_WIDTH:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getWidth() : '');
                    break;

                case Codes::PACKAGING_INPUT_HEIGHT:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getHeight() : '');
                    break;

                case Codes::PACKAGING_INPUT_LENGTH:
                    $comment = $this->commentFactory->create();
                    $comment->setContent($this->config->getDimensionUnit($shipment->getStoreId()));
                    $input->setComment($comment);
                    $input->setDefaultValue($defaultPackage ? (string) $defaultPackage->getLength() : '');
                    break;

                case Codes::PACKAGING_INPUT_SIZE_UNIT:
                    $dimensionsUnit = $this->config->getDimensionUnit($shipment->getStoreId()) === 'cm'
                        ? \Zend_Measure_Length::CENTIMETER
                        : \Zend_Measure_Length::INCH;
                    $input->setDefaultValue($dimensionsUnit);
                    break;

                // customs
                case Codes::PACKAGING_INPUT_CUSTOMS_VALUE:
                    $price = $this->itemAttributeReader->getTotalPrice($shipment);
                    $currency = $shipment->getStore()->getBaseCurrency();
                    $currencySymbol = $currency->getCurrencySymbol() ?: $currency->getCode();
                    $comment = $this->commentFactory->create();
                    $comment->setContent($currencySymbol);
                    $input->setComment($comment);
                    $input->setDefaultValue((string) $price);
                    break;

                case Codes::PACKAGING_INPUT_EXPORT_DESCRIPTION:
                    $exportDescriptions = $this->itemAttributeReader->getPackageExportDescriptions($shipment);
                    $exportDescription = implode(' ', $exportDescriptions);
                    $input->setDefaultValue(substr($exportDescription, 0, 80));
                    break;

                case 'dgCategory':
                    $dgCategories = $this->itemAttributeReader->getPackageDgCategories($shipment);
                    $input->setDefaultValue(implode(', ', $dgCategories));
                    break;

                case Codes::PACKAGING_INPUT_TERMS_OF_TRADE:
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

                case Codes::PACKAGING_INPUT_CONTENT_TYPE:
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
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        foreach ($optionsData as $optionGroup) {
            $this->processInputs($optionGroup, $shipment);
        }

        return $optionsData;
    }
}
