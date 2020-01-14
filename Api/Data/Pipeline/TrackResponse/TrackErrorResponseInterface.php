<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Magento\Framework\Phrase;

/**
 * Interface TrackErrorResponseInterface
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
