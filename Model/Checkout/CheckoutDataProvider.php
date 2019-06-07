<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Model\ShippingDataHydrator;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Exception\LocalizedException;

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
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param CheckoutDataCompositeProcessor $compositeProcessor
     * @param ShippingDataHydrator $shippingDataHydrator
     */
    public function __construct(
        ReaderInterface $reader,
        CheckoutDataCompositeProcessor $compositeProcessor,
        ShippingDataHydrator $shippingDataHydrator
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
     * @throws LocalizedException
     */
    public function getData(string $countryCode, int $storeId, string $postalCode): ShippingDataInterface
    {
        $shippingDataArray = $this->reader->read('frontend');
        $shippingData = $this->shippingDataHydrator->toObject($shippingDataArray);

        foreach ($shippingData->getCarriers() as $carrierData) {
            $carrierData->setServiceOptions(
                $this->compositeProcessor->processShippingOptions(
                    $carrierData->getServiceOptions(),
                    $countryCode,
                    $postalCode,
                    $storeId
                )
            );
            $this->compositeProcessor->processMetadata(
                $carrierData->getMetadata(),
                $countryCode,
                $postalCode,
                $storeId
            );
            $carrierData->setCompatibilityData(
                $this->compositeProcessor->processCompatibilityData(
                    $carrierData->getCompatibilityData(),
                    $countryCode,
                    $postalCode,
                    $storeId
                )
            );
        }

        return $shippingData;
    }
}
