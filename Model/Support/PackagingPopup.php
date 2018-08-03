<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Support;

/**
 * Class PackagingPopup
 *
 * @package Dhl\ShippingCore\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 *
 */
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
    public function __construct(array $supportMap)
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
