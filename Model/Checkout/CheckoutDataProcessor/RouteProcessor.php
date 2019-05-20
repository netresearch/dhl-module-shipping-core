<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor;

use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class RouteProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class RouteProcessor extends AbstractProcessor
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * RouteProcessor constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Remove all shipping options that do not match the route (origin and destination) of the current checkout.
     *
     * @param array optionsData
     * @param string $countryId     Destination country code
     * @param string $postalCode    Destination postal code
     * @param int|null $scopeId
     * @return array
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $shippingOrigin = strtolower($this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_COUNTRY_ID,
            'website',
            $scopeId
        ));
        $countryId = strtolower($countryId);

        foreach ($optionsData as $optionCode => $shippingOption) {
            $matchesRoute = $this->checkIfOptionMatchesRoute($shippingOption, $shippingOrigin, $countryId);
            if (!$matchesRoute) {
                unset($optionsData[$optionCode]);
            }
        }

        return $optionsData;
    }

    /**
     * @param $shippingOption
     * @param $shippingOrigin
     * @param string $countryId
     * @return bool
     */
    private function checkIfOptionMatchesRoute($shippingOption, $shippingOrigin, string $countryId): bool
    {
        if (!isset($shippingOption['routes'])) {
            // Option matches all routes
            return true;
        }
        $matchingRoutes = array_filter(
            $shippingOption['routes'],
            function ($route) use ($shippingOrigin, $countryId) {
                $originMatches = strtolower($route['origin']) === $shippingOrigin;
                $destinationMatches = isset($route['destinations'])
                    ? in_array(
                        $countryId,
                        array_map('strtolower', $route['destinations']),
                        true
                    )
                    : true;
                return $originMatches && $destinationMatches;
            }
        );

        return !empty($matchingRoutes);
    }
}
