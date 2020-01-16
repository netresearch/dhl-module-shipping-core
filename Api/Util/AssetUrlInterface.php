<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Util;

/**
 * Utility for loading a static view file URL
 *
 * @api
 * @author Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link   https://www.netresearch.de/
 */
interface AssetUrlInterface
{
    /**
     * Obtain the configured theme's frontend URL for a given asset ID.
     *
     * @param string $assetId
     * @return string
     */
    public function get(string $assetId): string;
}
