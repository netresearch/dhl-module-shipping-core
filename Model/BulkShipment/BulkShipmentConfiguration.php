<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\BulkShipment;

use Dhl\ShippingCore\Api\BulkShipment\BulkLabelCancellationInterface;
use Dhl\ShippingCore\Api\BulkShipment\BulkLabelCreationInterface;
use Dhl\ShippingCore\Api\BulkShipment\BulkShipmentConfigurationInterface;
use Dhl\ShippingCore\Api\Pipeline\ShipmentRequest\RequestModifierInterface;

/**
 * Composite configuration accessor for handling bulk shipment actions (create, cancel).
 */
class BulkShipmentConfiguration
{
    /**
     * @var BulkShipmentConfigurationInterface[]
     */
    private $configurations;

    /**
     * BulkShipmentConfiguration constructor.
     *
     * @param BulkShipmentConfigurationInterface[] $configurations
     */
    public function __construct(
        array $configurations = []
    ) {
        $this->configurations = $configurations;
    }

    /**
     * Load bulk shipment configuration for the given carrier code.
     *
     * @param string $carrierCode
     * @return BulkShipmentConfigurationInterface
     * @throws \RuntimeException
     */
    private function getCarrierConfiguration(string $carrierCode): BulkShipmentConfigurationInterface
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }

        throw new \RuntimeException("Bulk shipment configuration for carrier $carrierCode is not available.");
    }

    /**
     * Obtain codes of all configured carriers.
     *
     * @return string[]
     */
    public function getCarrierCodes()
    {
        $carrierCodes = array_map(
            static function (BulkShipmentConfigurationInterface $config) {
                return $config->getCarrierCode();
            },
            $this->configurations
        );

        return $carrierCodes;
    }

    /**
     * Obtain the shipment request modifier to
     * @param string $carrierCode
     * @return RequestModifierInterface
     * @throws \RuntimeException
     */
    public function getRequestModifier(string $carrierCode): RequestModifierInterface
    {
        return $this->getCarrierConfiguration($carrierCode)->getRequestModifier();
    }

    /**
     * Obtain the service capable of requesting labels for multiple shipment requests.
     *
     * @param string $carrierCode
     * @return BulkLabelCreationInterface
     * @throws \RuntimeException
     */
    public function getBulkShipmentService(string $carrierCode): BulkLabelCreationInterface
    {
        $config = $this->getCarrierConfiguration($carrierCode);
        return $config->getLabelService();
    }

    /**
     * Obtain the service capable of cancelling labels for multiple track/cancellation requests.
     *
     * @param string $carrierCode
     * @return BulkLabelCancellationInterface
     * @throws \RuntimeException
     */
    public function getBulkCancellationService(string $carrierCode): BulkLabelCancellationInterface
    {
        $config = $this->getCarrierConfiguration($carrierCode);
        return $config->getCancellationService();
    }

    /**
     * Check if a carrier allows deleting single tracks of a shipment.
     *
     * Most carriers will not allow deleting single tracks: There is no
     * association between a shipment's track and a page in the label PDF,
     * a single label cannot be selectively removed. The packaging information
     * for the track is lost and it is not possible to re-create a label for
     * one of the shipment's packages only.
     *
     * @param string $carrierCode
     * @return bool
     * @throws \RuntimeException
     */
    public function isSingleTrackDeletionAllowed(string $carrierCode): bool
    {
        $config = $this->getCarrierConfiguration($carrierCode);
        return $config->isSingleTrackDeletionAllowed();
    }
}
