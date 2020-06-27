<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * Post-process shipping methods as retrieved from the collect rates pipeline.
 *
 * Response processors offer a dedicated way to perform additional actions on the artifacts collected during pipeline
 * execution. The default implementation is a composite processor. There are pre-defined processors available, any
 * further processors which implement this interface may be created and added via configuration.
 *
 * @see CollectRatesPipelineInterface
 *
 * @api
 */
interface RateResponseProcessorInterface
{
    /**
     * Perform read/write actions on the webservice rate result.
     *
     * @param Method[] $methods List of rate methods
     * @param RateRequest|null $request The rate request
     *
     * @return Method[]
     */
    public function processMethods(array $methods, RateRequest $request = null): array;
}
