<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Adminhtml\Shipping\Block\View;

use Dhl\ShippingCore\Model\BulkShipmentConfiguration;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Shipping\Block\Adminhtml\View;

/**
 * Class AddShipmentCancelButton
 *
 */
class AddShipmentCancelButton
{
    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfigProvider;

    /**
     * AddShipmentCancelButton constructor.
     *
     * @param BulkShipmentConfiguration $bulkConfigProvider
     */
    public function __construct(BulkShipmentConfiguration $bulkConfigProvider)
    {
        $this->bulkConfigProvider = $bulkConfigProvider;
    }

    /**
     * Add a "Cancel Shipment" button to the shipment details page if the shipment has DHLGW tracks.
     *
     * @param View $viewBlock
     * @return null
     */
    public function beforeSetLayout(View $viewBlock)
    {
        $shipment = $viewBlock->getShipment();
        $carrierCode = strtok($shipment->getOrder()->getShippingMethod(), '_');

        try {
            $this->bulkConfigProvider->getBulkCancellationService($carrierCode);
        } catch (LocalizedException $exception) {
            // cancellation not supported by given carrier
            return null;
        }

        $tracks = $viewBlock->getShipment()->getAllTracks();
        $dhlTracks = array_filter($tracks, static function (ShipmentTrackInterface $track) use ($carrierCode) {
            return ($track->getCarrierCode() === $carrierCode);
        });

        if (empty($dhlTracks)) {
            // no carrier tracks in shipment, nothing to cancel
            return null;
        }

        $shipmentId = $viewBlock->getShipment()->getId();
        $cancelUrl = $viewBlock->getUrl('dhl/shipment/cancel', ['shipment_id' => $shipmentId]);
        $viewBlock->addButton(
            'dhl_cancel_shipment',
            [
                'label' => __('Cancel Labels'),
                'onclick' => "setLocation('$cancelUrl')"
            ]
        );

        return null;
    }
}
