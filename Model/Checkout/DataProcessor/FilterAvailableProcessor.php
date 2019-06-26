<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Model\Checkout\CheckoutArrayProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class FilterAvailableProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class FilterAvailableProcessor implements CheckoutArrayProcessorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * DefaultConfigValueProcessor constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns the specified configuration value.
     *
     * @param string       $configPath
     * @param string|null $store
     *
     * @return bool
     */
    private function getConfigValue(string $configPath, $store = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Filters the shipping data, removing those entries which are disabled in configuration.
     *
     * @param mixed[] $shippingData
     * @param int $storeId
     *
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, int $storeId): array
    {
        foreach ($shippingData['carriers'] as $carrierCode => $carrier) {
            foreach ($carrier as $optionKey => $optionValues) {
                if (!is_array($optionValues)) {
                    continue;
                }

                foreach ($optionValues as $code => $values) {
                    if (isset($values['available'])) {
                        $available = $this->getConfigValue($values['available'], $storeId);

                        if (!$available) {
                            unset($shippingData['carriers'][$carrierCode][$optionKey][$code]);
                        } else {
                            unset($shippingData['carriers'][$carrierCode][$optionKey][$code]['available']);
                        }
                    }
                }
            }
        }

        return $shippingData;
    }
}
