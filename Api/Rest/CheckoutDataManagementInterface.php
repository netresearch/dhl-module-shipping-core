<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;

/**
 * Interface CartServiceManagementInterface
 *
 * Get Checkout Services
 *
 * @api
 * @package  Dhl\Shipping\Api
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
interface CheckoutDataManagementInterface
{
    /**
     * @param int $quoteId
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     */
    public function getData(int $quoteId, string $countryId, string $postalCode): CheckoutDataInterface;

    /**
     * @param int $quoteId
     * @param \Magento\Framework\Api\AttributeInterface[] $serviceSelection
     * @return void
     */
    public function setData(int $quoteId, array $serviceSelection);
}
