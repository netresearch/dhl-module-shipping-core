<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterfaceFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManager\ConfigInterface;

/**
 * Class CheckoutDataHydrator
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataHydrator
{
    /**
     * @var CheckoutDataInterfaceFactory
     */
    private $checkoutDataFactory;

    /**
     * @var \JsonMapper
     */
    private $jsonMapper;

    /**
     * @var ConfigInterface
     */
    private $diConfig;

    /**
     * CheckoutDataHydrator constructor.
     *
     * @param CheckoutDataInterfaceFactory $checkoutDataFactory
     * @param \JsonMapper $jsonMapper
     * @param ConfigInterface $diConfig
     */
    public function __construct(
        CheckoutDataInterfaceFactory $checkoutDataFactory,
        \JsonMapper $jsonMapper,
        ConfigInterface $diConfig
    ) {
        $this->checkoutDataFactory = $checkoutDataFactory;
        $this->jsonMapper = $jsonMapper;
        $this->diConfig = $diConfig;
    }

    /**
     * Convert a plain nested array of scalar types into a CheckoutDataInterface object.
     *
     * @param array $data
     * @return CheckoutDataInterface
     * @throws InputException
     */
    public function toObject(array $data): CheckoutDataInterface
    {
        $this->configureJsonMapper();
        try {
            /** @var CheckoutDataInterface $checkoutData */
            $checkoutData = $this->jsonMapper->map($data, $this->checkoutDataFactory->create());

            return $checkoutData;
        } catch (\Exception $exception) {
            throw new InputException(__('Error: Invalid checkout data input array given.'), $exception);
        }
    }

    private function configureJsonMapper()
    {
        $this->jsonMapper->bExceptionOnUndefinedProperty = true;
        $this->jsonMapper->bEnforceMapType = false;
        $this->jsonMapper->bIgnoreVisibility = true;
        $this->jsonMapper->bStrictObjectTypeChecking = true;
        $this->jsonMapper->bStrictNullTypes = true;
        $preferencesWithLeadingBackslash = [];
        foreach ($this->diConfig->getPreferences() as $interface => $class) {
            /** @see https://github.com/cweiske/jsonmapper/issues/111 */
            $preferencesWithLeadingBackslash['\\' . $interface] = '\\' . $class;
        }
        $this->jsonMapper->classMap = $preferencesWithLeadingBackslash;
    }
}
