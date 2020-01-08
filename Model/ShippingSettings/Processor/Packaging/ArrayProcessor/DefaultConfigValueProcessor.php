<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor;

use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsArrayProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class DefaultConfigValueProcessor
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class DefaultConfigValueProcessor implements ShippingOptionsArrayProcessorInterface
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
     * @return string
     */
    private function getConfigValue(string $configPath, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Uses the provided default config value path to read the respective value and pass it into the
     * defaultValue element. Afterwards the "defaultConfigValue" element is removed from the shipping data
     * array.
     *
     * @param mixed[] $shippingData
     * @param ShipmentInterface $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, ShipmentInterface $shipment): array
    {
        foreach ($shippingData['carriers'] as $carrierCode => &$carrier) {
            foreach ($carrier as $optionKey => &$optionValues) {
                if (!is_array($optionValues)) {
                    continue;
                }

                foreach ($optionValues as $code => &$values) {
                    if (!isset($values['inputs'])) {
                        continue;
                    }

                    foreach ($values['inputs'] as $inputCode => &$inputValues) {
                        if (!isset($inputValues['defaultConfigValue'])) {
                            continue;
                        }

                        $configPath = $inputValues['defaultConfigValue'];
                        $inputValues['defaultValue'] = $this->getConfigValue($configPath, $shipment->getStoreId());
                        unset($inputValues['defaultConfigValue']);
                    }
                }
            }
        }

        return $shippingData;
    }
}
