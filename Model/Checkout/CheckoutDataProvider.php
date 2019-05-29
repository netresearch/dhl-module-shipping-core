<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Exception\InputException;

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
     * The option group relevant for the checkout.
     */
    const GROUPNAME = PackagingDataProvider::GROUP_SERVICE;

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var CheckoutDataCompositeProcessor
     */
    private $compositeProcessor;

    /**
     * @var CheckoutDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param CheckoutDataCompositeProcessor $compositeProcessor
     * @param CheckoutDataHydrator $shippingDataHydrator
     */
    public function __construct(
        ReaderInterface $reader,
        CheckoutDataCompositeProcessor $compositeProcessor,
        CheckoutDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeProcessor = $compositeProcessor;
        $this->shippingDataHydrator = $shippingDataHydrator;
    }

    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     * @return ShippingDataInterface
     * @throws InputException
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): ShippingDataInterface
    {
        $shippingData = $this->reader->read('frontend');

        if (!isset($shippingData['carriers'])) {
            $shippingData['carriers'] = [];
        }

        foreach ($shippingData['carriers'] as $carrierCode => $carrierData) {
            $carrierData[self::GROUPNAME] = $this->compositeProcessor->processShippingOptions(
                $carrierData[self::GROUPNAME] ?? [],
                $countryCode,
                $postalCode,
                $storeId
            );
            $carrierData['metaData'] = $this->compositeProcessor->processMetadata(
                $carrierData['metaData'] ?? [],
                $countryCode,
                $postalCode,
                $storeId
            );
            $carrierData['compatibilityData'] = $this->compositeProcessor->processCompatibilityData(
                $carrierData['compatibilityData'] ?? [],
                $countryCode,
                $postalCode,
                $storeId
            );

            $shippingData['carriers'][$carrierCode] = $carrierData;
        }

        return $checkoutData;
    }
}
