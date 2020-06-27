<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track\TrackResponse;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Model\Pipeline\Track\TrackResponse\TrackResponse;
use Magento\Framework\Phrase;

class TrackErrorResponse extends TrackResponse implements TrackErrorResponseInterface
{
    /**
     * @return Phrase
     */
    public function getErrors(): Phrase
    {
        return $this->getData(self::ERRORS);
    }
}
