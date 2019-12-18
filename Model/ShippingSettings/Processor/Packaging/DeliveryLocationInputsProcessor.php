<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class DeliveryLocationInputsProcessor
 *
 * @package Dhl\Paket\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class DeliveryLocationInputsProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * The input type for the shopfinder component.
     */
    const INPUT_TYPE_SHOPFINDER = 'shopfinder';

    /*
     * The virtual input's codes that will be added
     * to the shipping option with the shopfinder input.
     */
    const INPUT_CODES = [
        'company',
        'locationType',
        'locationNumber',
        'locationId',
        'street',
        'postalCode',
        'city',
        'countryCode',
    ];

    /**
     * @var InputInterfaceFactory
     */
    private $inputFactory;

    /**
     * DeliveryLocationInputsProcessor constructor.
     *
     * @param InputInterfaceFactory $inputFactory
     */
    public function __construct(InputInterfaceFactory $inputFactory)
    {
        $this->inputFactory = $inputFactory;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $optionsData,
        ShipmentInterface $shipment
    ): array {
        $index = null;
        $shopFinder = null;
        foreach ($optionsData as $key => $option) {
            foreach ($option->getInputs() as $input) {
                if ($input->getInputType() === self::INPUT_TYPE_SHOPFINDER) {
                    $index = $key;
                    $shopFinder = $option;
                    break 2;
                }
            }
        }

        if ($shopFinder && $index) {
            $inputs = $shopFinder->getInputs();
            foreach (self::INPUT_CODES as $inputCode) {
                /** @var InputInterface $input */
                $input = $this->inputFactory->create();
                $input->setCode($inputCode);
                $input->setInputType('hidden');
                $inputs[$inputCode] = $input;
            }
            $shopFinder->setInputs($inputs);
            $optionsData[$index] = $shopFinder;
        }

        return $optionsData;
    }
}
