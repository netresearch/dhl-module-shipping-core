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
     * @var CheckoutDataCompositeProcessor
     */
    private $compositeProcessor;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param CheckoutDataCompositeProcessor $compositeProcessor
     */
    public function __construct(ReaderInterface $reader, CheckoutDataCompositeProcessor $compositeProcessor)
    {
        $this->reader = $reader;
        $this->compositeProcessor = $compositeProcessor;
    }

    /**
     * @param string $countryCode
     * @param int $storeId
     * @param string $postalCode
     * @return array
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): array
    {
        $checkoutData = $this->reader->read('frontend');

        foreach ($checkoutData['carriers'] as $carrierCode => $carrierData) {
            foreach (['packageLevelOptions', 'itemLevelOptions'] as $group) {
                $carrierData[$group] = $this->compositeProcessor->processShippingOptions(
                    $carrierData[$group] ?? [],
                    $countryCode,
                    $postalCode,
                    $storeId
                );
            }
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

            $checkoutData['carriers'][$carrierCode] = $carrierData;
        }

        return $checkoutData;
    }
}
