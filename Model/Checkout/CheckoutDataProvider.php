<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Magento\Framework\Config\ReaderInterface;

/**
 * Class CheckoutDataProvider
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutDataProvider
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     * @return array
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): array
    {
        $data = $this->reader->read();

        return $data;
    }
}
