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
 * Class AllowedProducts
 *
 * @package Dhl\ShippingCore\Model\Rate\Processor
 */
class AllowedProducts implements RateProcessorInterface
{
    /**
     * @var RateConfigInterface
     */
    private $rateConfig;

    /**
     * AllowedProducts constructor.
     *
     * @param RateConfigInterface $config
     */
    public function __construct(RateConfigInterface $config)
    {
        $this->rateConfig = $config;
    }

    /**
     * @inheritdoc
     */
    public function processMethods(array $methods, RateRequest $request = null, $carrierCode = null): array
    {
        $result = [];
        foreach ($methods as $method) {
            if ($this->isEnabledProduct($method, $carrierCode)) {
                $result[] = $method;
            }
        }

        return $result;
    }

    /**
     * Returns whether the product is enabled in the configuration or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    private function isEnabledProduct(Method $method, $carrierCode): bool
    {
        $allowedDomestic      = $this->rateConfig->getAllowedDomesticProducts($carrierCode);
        $allowedInternational = $this->rateConfig->getAllowedInternationalProducts($carrierCode);
        $allowedProducts      = array_merge($allowedDomestic, $allowedInternational);

        return \in_array($method->getData('method'), $allowedProducts, true);
    }
}
