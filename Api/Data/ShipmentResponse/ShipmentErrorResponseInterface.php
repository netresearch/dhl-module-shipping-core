<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentResponse;

use Magento\Framework\Phrase;

/**
 * Interface ErrorResponseInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ShipmentErrorResponseInterface extends ShipmentResponseInterface
{
    const ERRORS = 'errors';

    /**
     * @return Phrase
     */
    public function getErrors(): Phrase;
}
