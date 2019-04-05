<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Rest;

<<<<<<< Updated upstream
=======
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;

>>>>>>> Stashed changes
/**
 * Interface GuestCartServiceManagementInterface
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
interface GuestCheckoutDataManagementInterface
{
    /**
     * @param string $cartId
<<<<<<< Updated upstream
     * @param \Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface[] $serviceSelection
=======
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     */
    public function getData(string $cartId, string $countryId, string $postalCode): CheckoutDataInterface;

    /**
     * @param string $cartId
     * @param \Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface[] $serviceSelection
>>>>>>> Stashed changes
     * @return void
     */
    public function setServiceSelection(string $cartId, array $serviceSelection);
}
