<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\TrackResponse;

use Magento\Framework\Phrase;

/**
 * Interface ErrorResponseInterface
 *
 * @api
 */
interface TrackErrorResponseInterface extends TrackResponseInterface
{
    const ERRORS = 'errors';

    /**
     * @return Phrase
     */
    public function getErrors(): Phrase;
}
