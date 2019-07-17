<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\RouteProcessor;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ItemCheckoutProcessor
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 */
class ItemCheckoutProcessor extends AbstractProcessor
{
    /**
     * @var RouteProcessor
     */
    private $checkoutRoutProcessor;

    /**
     * ItemCheckoutProcessor constructor.
     * @param RouteProcessor $checkoutRoutProcessor
     */
    public function __construct(RouteProcessor $checkoutRoutProcessor)
    {
        $this->checkoutRoutProcessor = $checkoutRoutProcessor;
    }

    /**
     * Handle route config on item level in packaging popup.
     *
     * @param array $itemData
     * @param Shipment $shipment
     * @return array
     */
    public function processItemOptions(array $itemData, Shipment $shipment): array
    {
        /** @var ItemShippingOptionsInterface $data */
        foreach ($itemData as $data) {
            $data->setShippingOptions($this->checkoutRoutProcessor->processShippingOptions(
                $data->getShippingOptions(),
                $shipment->getShippingAddress()->getCountryId(),
                $shipment->getShippingAddress()->getPostcode(),
                (int) $shipment->getStoreId()
            ));
        }

        return $itemData;
    }
}
