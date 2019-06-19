<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\TrackResponse;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackErrorResponseInterface;
use Magento\Framework\Phrase;

/**
 * TrackErrorResponse
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
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
