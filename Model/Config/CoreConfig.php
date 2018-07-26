<?php
/**
 * ${MODULE_LONG}
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category  ${MODULE}
 * @package   Dhl\ShippingCore\Model\Config
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

namespace Dhl\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CoreConfig
 *
 * @package Dhl\ShippingCore\Model
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class CoreConfig implements CoreConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CoreConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
    public function getCodMethods($store = null): array
    {
        return explode(
            ',',
            $this->scopeConfig->getValue(
                self::CONFIG_XML_PATH_COD_METHODS,
                ScopeInterface::SCOPE_WEBSITE,
                $store
            )
        );
    }

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, $store = null): bool
    {
        return \in_array($methodCode, $this->getCodMethods($store), true);
    }

    /**
     * Get terms of trade.
     *
     * @param null $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_TERMS_OF_TRADE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the cut off time.
     *
     * @param null $store
     * @return string
     */
    public function getCutOffTime($store = null): string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_CUT_OFF_TIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
