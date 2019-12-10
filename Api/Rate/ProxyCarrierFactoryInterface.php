<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Rate;

use Magento\Framework\Exception\NotFoundException;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Interface ProxyCarrierFactoryInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Paul Siedler <paul.siedler@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface ProxyCarrierFactoryInterface
{
    /**
     * Creates a carrier model with a partially overwritten config (enforces carrier to be active)
     *
     * @param string $carrierCode Carrier model for which the config should be mocked
     * @return AbstractCarrierInterface
     * @throws NotFoundException Requested carrier not found
     * @throws \Exception Object manager / factory error
     */
    public function create(string $carrierCode): AbstractCarrierInterface;
}
