<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface PackagingOptionReaderInterface
 *
 * @package Dhl\ShippingCore\Api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface PackagingOptionReaderInterface
{
    /**
     * Read a package option from the packaging options identified by option code and input code.
     *
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getPackageOptionValue(string $optionCode, string $inputCode);

    /**
     * Read an item value from the packaging options.
     *
     * @param int $orderItemId
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getItemOptionValue(int $orderItemId, string $optionCode, string $inputCode);

    /**
     * Read a service value from the packaging options identified by service code and input code.
     *
     * @param string $serviceCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getServiceOptionValue(string $serviceCode, string $inputCode);

    /**
     * Read all service options.
     *
     * @return string[][]
     * @throws LocalizedException
     */
    public function getServiceOptionValues(): array;
}
