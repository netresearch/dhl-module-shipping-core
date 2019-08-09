<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterfaceFactory;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ItemQtyOptionsProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemQtyOptionsProcessor extends AbstractProcessor
{
    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * ItemQtyOptionsProcessor constructor.
     *
     * @param OptionInterfaceFactory $optionFactory
     */
    public function __construct(
        OptionInterfaceFactory $optionFactory
    ) {
        $this->optionFactory = $optionFactory;
    }

    /**
     * @param Shipment $shipment
     * @param int $itemId
     * @return OptionInterface[]
     */
    private function generateOptions(Shipment $shipment, int $itemId): array
    {
        foreach ($shipment->getItems() as $item) {
            if ((int)$item->getOrderItemId() === $itemId) {
                $itemQuantity = (int)$item->getQty();
                break;
            }
        }
        $options = [];
        if (isset($itemQuantity)) {
            for ($i = 2; $i <= $itemQuantity; $i++) {
                $option = $this->optionFactory->create();
                $option->setLabel((string)$i);
                $option->setValue((string)$i);
                $options[] = $option;
            }
        }


        return $options;
    }

    /**
     * Set default values for item detail and item customs inputs from the shipment items.
     *
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(array $itemData, Shipment $shipment): array
    {
        foreach ($itemData as $itemShippingOptions) {
            $input = $itemShippingOptions->getShippingOptions()['details']->getInputs()['qty'];
            $input->setOptions(
                array_merge(
                    $input->getOptions(),
                    $this->generateOptions($shipment, $itemShippingOptions->getItemId())
                )
            );
        }

        return $itemData;
    }
}
