<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest\Validator;

use Dhl\ShippingCore\Api\RequestValidatorInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Class CompositeValidator
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CompositeValidator implements RequestValidatorInterface
{
    /**
     * @var RequestValidatorInterface[]
     */
    private $validators;

    /**
     * @param RequestValidatorInterface[] $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * Validate shipment requests and throw exception on errors.
     *
     * @param Request $shipmentRequest
     * @throws ValidatorException
     *
     * @return void
     */
    public function validate(Request $shipmentRequest)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($shipmentRequest);
        }
    }
}
