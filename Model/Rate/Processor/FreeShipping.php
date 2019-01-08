<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Rate\RateProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * A rate processor to remove the shipping price if certain conditions are met.
 *
 * @package  Dhl\ShippingCore\Model
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class FreeShipping implements RateProcessorInterface
{
    /**
     * The module configuration.
     *
     * @var RateConfigInterface
     */
    private $rateConfig;

    /**
     * Constructor.
     *
     * @param RateConfigInterface $rateConfig The module configuration
     */
    public function __construct(RateConfigInterface $rateConfig)
    {
        $this->rateConfig = $rateConfig;
    }

    /**
     * @inheritdoc
     */
    public function processMethods(array $methods, RateRequest $request = null, $carrierCode = null): array
    {
        if ($request === null) {
            return $methods;
        }
        $productsSubTotal = $this->getBaseSubTotalInclTax($carrierCode, $request);
        $domesticBaseSubTotal = $this->rateConfig->getDomesticFreeShippingSubTotal($carrierCode);
        $internationalBaseSubTotal = $this->rateConfig->getInternationalFreeShippingSubTotal($carrierCode);

        /** @var Method $method */
        foreach ($methods as $method) {
            if ($this->rateConfig->isDomesticFreeShippingEnabled($carrierCode)
                && $this->isEnabledDomesticProduct($method)
            ) {
                $configuredSubTotal = $domesticBaseSubTotal;
            } elseif ($this->rateConfig->isInternationalFreeShippingEnabled($carrierCode)
                      && $this->isEnabledInternationalProduct($method)
            ) {
                $configuredSubTotal = $internationalBaseSubTotal;
            } else {
                continue;
            }

            if ($productsSubTotal >= $configuredSubTotal) {
                $method->setPrice(0.0);
                $method->setCost(0.0);
            }
        }

        return $methods;
    }

    /**
     * Returns the base sub total value including tax. Checks if the value of virtual products should
     * be included in the sum.
     *
     * @param string $carrierCode
     * @param RateRequest $request The rate request
     *
     * @return float
     */
    private function getBaseSubTotalInclTax($carrierCode, RateRequest $request): float
    {
        if ($this->rateConfig->isFreeShippingVirtualProductsIncluded($carrierCode)) {
            return $request->getBaseSubtotalInclTax();
        }

        $baseSubTotal = 0.0;

        if ($request->getAllItems()) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($request->getAllItems() as $item) {
                if (!$item->getProduct()->isVirtual()) {
                    $baseSubTotal += $item->getBasePriceInclTax();
                }
            }
        }

        return $baseSubTotal;
    }

    /**
     * Returns whether the product is enabled in the configuration or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    private function isEnabledDomesticProduct(Method $method): bool
    {
        return \in_array(
            $method->getData('method'),
            $this->rateConfig->getDomesticFreeShippingProducts($method->getData('carrier')),
            true
        );
    }

    /**
     * Returns whether the product is enabled in the configuration or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    private function isEnabledInternationalProduct(Method $method): bool
    {
        return \in_array(
            $method->getData('method'),
            $this->rateConfig->getInternationalFreeShippingProducts($method->getData('carrier')),
            true
        );
    }
}
