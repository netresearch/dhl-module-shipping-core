<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\CheckoutManagementInterface;
use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingDataInterfaceFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;

/**
 * Class CheckoutDataHydrator
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataHydrator
{
    /**
     * @var ShippingDataInterfaceFactory
     */
    private $checkoutDataFactory;

    /**
     * @var ServiceOutputProcessor
     */
    private $serviceOutputProcessor;

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
     * @param ShippingDataInterfaceFactory $checkoutDataFactory
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param \JsonMapper $jsonMapper
     * @param ConfigInterface $diConfig
     */
    public function __construct(
        ShippingDataInterfaceFactory $checkoutDataFactory,
        ServiceOutputProcessor $serviceOutputProcessor,
        \JsonMapper $jsonMapper,
        ConfigInterface $diConfig
    ) {
        $this->checkoutDataFactory = $checkoutDataFactory;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->jsonMapper = $jsonMapper;
        $this->diConfig = $diConfig;
    }

    /**
     * Convert a plain nested array of scalar types into a ShippingDataInterface object.
     *
     * @param array $data
     * @return ShippingDataInterface
     * @throws InputException
     */
    public function toObject(array $data): ShippingDataInterface
    {
        $this->configureJsonMapper();
        try {
            /** @var ShippingDataInterface $checkoutData */
            $checkoutData = $this->jsonMapper->map($data, $this->checkoutDataFactory->create(['carriers' => []]));

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
