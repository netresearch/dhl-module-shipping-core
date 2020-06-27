<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest\Validator;

use Dhl\ShippingCore\Api\Pipeline\ShipmentRequest\RequestValidatorInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Shipping\Model\Shipment\Request;

class CompositeValidator implements RequestValidatorInterface
{
    /**
     * @var RequestValidatorInterface[]
     */
    private $validators;

    /**
     * @param RequestValidatorInterface[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    public function validate(Request $shipmentRequest)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($shipmentRequest);
        }
    }
}
