<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\DeliveryLocation;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\LocationInterface;
use Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;
use Dhl\ShippingCore\Api\DeliveryLocation\LocationProviderInterface;
use Dhl\ShippingCore\Api\DeliveryLocation\SearchInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Search
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class Search implements SearchInterface
{
    /**
     * @var LocationProviderInterface[]
     */
    private $locationProviders;

    /**
     * Search constructor.
     *
     * @param LocationProviderInterface[] $locationProviders
     */
    public function __construct($locationProviders = [])
    {
        $this->locationProviders = $locationProviders;
    }

    /**
     * @param string $carrierCode
     * @param AddressInterface $searchAddress
     * @return LocationInterface[]
     * @throws LocalizedException
     */
    public function search(string $carrierCode, AddressInterface $searchAddress): array
    {
        foreach ($this->locationProviders as $provider) {
            if ($provider->getCarrierCode() === $carrierCode) {
                return $provider->getLocationsByAddress($searchAddress);
            }
        }

        throw new \RuntimeException('No parcel shop location provider configured');
    }
}
