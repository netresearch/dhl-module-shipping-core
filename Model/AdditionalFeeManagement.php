<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\AdditionalFeeConfigurationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;

/**
 * Class AdditionalFeeManagement
 *
 * @package Dhl\ShippingCore\Model
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 */
class AdditionalFeeManagement
{
    /**
     * @var AdditionalFeeConfigurationInterface[]
     */
    private $additionalFeeConfiguration;

    /**
     * AdditionalFeeManagement constructor.
     *
     * @param array $additionalFeeConfiguration
     */
    public function __construct($additionalFeeConfiguration = [])
    {
        $this->additionalFeeConfiguration = $additionalFeeConfiguration;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isActive(Quote $quote): bool
    {
        $carrierCode = strtok((string) $quote->getShippingAddress()->getShippingMethod(), '_');
        if (!$carrierCode) {
            return false;
        }

        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->isActive($quote);
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    /**
     * @param Quote $quote
     * @return float
     */
    public function getTotalAmount(Quote $quote): float
    {
        $carrierCode = strtok((string) $quote->getShippingAddress()->getShippingMethod(), '_');
        if (!$carrierCode) {
            return 0.0;
        }

        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->getServiceCharge($quote);
        } catch (\RuntimeException $e) {
            return 0.0;
        }
    }

    public function getLabel(string $carrierCode): Phrase
    {
        try {
            $configuration = $this->getConfigurationForCarrierCode($carrierCode);

            return $configuration->getLabel();
        } catch (\RuntimeException $e) {
            return __('Additional Fee');
        }
    }

    /**
     * @param string $carrierCode
     * @return AdditionalFeeConfigurationInterface
     * @throws \RuntimeException
     */
    private function getConfigurationForCarrierCode(string $carrierCode): AdditionalFeeConfigurationInterface
    {
        foreach ($this->additionalFeeConfiguration as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }

        throw new \RuntimeException('No configuration found for given carrier code.');
    }
}
