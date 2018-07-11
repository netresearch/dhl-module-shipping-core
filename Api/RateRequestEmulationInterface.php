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
 * @package   ${NAMESPACE}
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

namespace Dhl\ShippingCore\Api;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class RateRequestService
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
interface RateRequestEmulationInterface
{
    /**
     * @param string $carrierCode Carrier code to emulate
     * @param RateRequest $request Original rate request
     * @return bool|\Magento\Framework\DataObject|null
     */
    public function emulateRateRequest(string $carrierCode, RateRequest $request);
}
