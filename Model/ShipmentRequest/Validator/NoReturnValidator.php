<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest\Validator;

use Dhl\ShippingCore\Api\RequestValidatorInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Shipping\Model\Shipment\ReturnShipment;

/**
 * Class NoReturnValidator
 *
 * Validate that no return shipment label is requested.
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class NoReturnValidator implements RequestValidatorInterface
{
    public function validate(Request $shipmentRequest)
    {
        if (($shipmentRequest->getData('is_return') || $shipmentRequest instanceof ReturnShipment)) {
            throw new ValidatorException(__('Return shipments are not supported.'));
        }
    }
}
