<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class OfflineCarriers
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link http://www.netresearch.de/
 */
class OfflineCarriers implements ArrayInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Carriers constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function toOptionArray(): array
    {
        $result = [];
        $carriers = $this->scopeConfig->getValue('carriers');
        if ($carriers) {
            $carriers = array_filter(
                $carriers,
                function ($carrier) {
                    // Only use offline carriers
                    return !array_key_exists('is_online', $carrier) || (bool)$carrier['is_online'] === false;
                }
            );
            foreach (array_keys($carriers) as $carrierCode) {
                $result[] = [
                    'value' => $carrierCode,
                    'label' => ucfirst((string)$carrierCode),
                ];
            }
        }

        return $result;
    }
}
