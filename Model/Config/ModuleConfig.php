<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Netresearch\ShippingCore\Api\InfoBox\VersionInterface;

class ModuleConfig implements VersionInterface
{
    private const METAPACKAGE_NAME = 'dhl/shipping-m2';

    public const CONFIG_PATH_TERMS_OF_TRADE = 'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/terms_of_trade';

    /**
     * Obtain the DHL Shipping metapackage version number.
     *
     * Since this package does not provide much more than the Post & DHL
     * configuration section, there are not many changes to be expected
     * and the package's own version number is of no interest to anyone.
     * Hence, we use the General Configuration header to display the
     * metapackage version if available.
     *
     * @return string
     */
    public function getModuleVersion(): string
    {
        if (!class_exists('\Composer\InstalledVersions')) {
            return '';
        }

        try {
            return \Composer\InstalledVersions::getPrettyVersion(self::METAPACKAGE_NAME);
        } catch (\OutOfBoundsException $exception) {
            return '';
        }
    }
}
