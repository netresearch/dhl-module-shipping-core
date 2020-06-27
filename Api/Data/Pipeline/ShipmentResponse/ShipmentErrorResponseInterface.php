<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

use Magento\Framework\Phrase;

/**
 * @api
 */
interface ShipmentErrorResponseInterface extends ShipmentResponseInterface
{
    const ERRORS = 'errors';

    /**
     * @return Phrase
     */
    public function getErrors(): Phrase;
}
