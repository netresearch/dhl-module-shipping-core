<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings;

class PackagingPopup
{
    /**
     * @var string[]
     */
    private $supportMap;

    /**
     * PackagingPopup constructor.
     *
     * @param string[] $supportMap
     */
    public function __construct($supportMap = [])
    {
        $this->supportMap = $supportMap;
    }

    /**
     * @param string $carrier
     * @return bool
     */
    public function isSupported(string $carrier) : bool
    {
        return \in_array($carrier, $this->supportMap, true);
    }
}
