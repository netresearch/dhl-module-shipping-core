<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Quote\Cart;

use Dhl\ShippingCore\Api\Data\Rate\MethodAdditionalInfoInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;
use Magento\Quote\Api\Data\ShippingMethodExtensionInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class ShippingMethodConverterPlugin
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    private $extensionFactory;

    /**
     * The delivery date.
     *
     * @var MethodAdditionalInfoInterface[]
     */
    private $methodAdditionalInfo = [];

    /**
     * DeliveryDate constructor.
     *
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(ShippingMethodExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add additional information to the carrier method data object
     *
     * @param ShippingMethodConverter $subject
     * @param ShippingMethodInterface $result
     *
     * @return ShippingMethodInterface
     */
    public function afterModelToDataObject(
        ShippingMethodConverter $subject,
        ShippingMethodInterface $result
    ): ShippingMethodInterface {
        $carrierMethod = $this->getCarrierMethod($result);

        if (array_key_exists($carrierMethod, $this->methodAdditionalInfo)) {
            /** @var ShippingMethodExtensionInterface $extensibleAttribute */
            $extensibleAttribute = $result->getExtensionAttributes() ?? $this->extensionFactory->create();
            $extensibleAttribute->setAdditionalInfo($this->methodAdditionalInfo[$carrierMethod]);

            $result->setExtensionAttributes($extensibleAttribute);
        }

        return $result;
    }

    /**
     * Add delivery date information to the carrier data object
     *
     * @param Rate $subject
     * @param AbstractResult $result
     *
     * @return void
     */
    public function beforeImportShippingRate(Rate $subject, AbstractResult $result)
    {
        // Store additional info to append in later on to the extension attributes
        if ($result->hasData(MethodAdditionalInfoInterface::ATTRIBUTE_KEY)) {
            /** @var Method $result */
            $carrierMethod = $result->getData('carrier') . '_' . $result->getData('method');
            $this->methodAdditionalInfo[$carrierMethod] = $result->getData(
                MethodAdditionalInfoInterface::ATTRIBUTE_KEY
            );
        }
    }

    /**
     * Creates the carrier_method string that identifies a single method uniquely
     *
     * @param ShippingMethodInterface $result
     * @return string
     */
    private function getCarrierMethod(ShippingMethodInterface $result): string
    {
        return $result->getCarrierCode() . '_' . $result->getMethodCode();
    }
}
