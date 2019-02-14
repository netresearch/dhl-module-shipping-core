<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Magento\Framework\Escaper;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class CheckoutDataManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class CheckoutDataManagement implements CheckoutDataManagementInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var CheckoutDataProvider
     */
    private $checkoutDataProvider;

    /**
     * CheckoutDataManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param Escaper $escaper
     * @param CheckoutDataProvider $checkoutDataProvider
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Escaper $escaper,
        CheckoutDataProvider $checkoutDataProvider
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->escaper = $escaper;
        $this->checkoutDataProvider = $checkoutDataProvider;
    }

    /**
     * @param int $quoteId
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData(int $quoteId, string $countryId, string $postalCode): CheckoutDataInterface
    {
        $quote = $this->quoteRepository->get($quoteId);

        $data = $this->checkoutDataProvider->getData($countryId, $quote->getStoreId(), $postalCode);

        return $data;
    }

    /**
     * Persist service selection with reference to a Quote Address ID.
     *
     * @param int $cartId
     * @param \Magento\Framework\Api\AttributeInterface[] $serviceSelection
     * @throws \Magento\Framework\Exception\NoSuchEntityException;
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setData(int $quoteId, array $serviceSelection)
    {
        $quote = $this->quoteRepository->get($cartId);
        $quoteAddressId = $quote->getShippingAddress()->getId();

        $this->serviceSelectionRepository->deleteByQuoteAddressId($quoteAddressId);

        foreach ($serviceSelection as $service) {
            $model = $this->serviceSelectionFactory->create();
            $model->setData(
                [
                    'parent_id' => $quoteAddressId,
                    'service_code' => $service->getAttributeCode(),
                    'service_value' => array_map(
                        function ($value) {
                            return $this->escaper->escapeHtml($value);
                        },
                        $service->getValue()
                    ),
                ]
            );
            $this->serviceSelectionRepository->save($model);
        }
    }
}
