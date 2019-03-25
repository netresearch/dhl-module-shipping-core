<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
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
     * @var CheckoutDataProvider
     */
    private $checkoutDataProvider;

    /**
     * @var CheckoutDataHydrator
     */
    private $checkoutDataHydrator;

    /**
     * CheckoutDataManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param CheckoutDataHydrator $checkoutDataHydrator
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->checkoutDataHydrator = $checkoutDataHydrator;
    }

    /**
     * @param int $quoteId
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getData(int $quoteId, string $countryId, string $postalCode): CheckoutDataInterface
    {
        $quote = $this->quoteRepository->get($quoteId);
        $data = $this->checkoutDataProvider->getData($countryId, $quote->getStoreId(), $postalCode);

        return $this->checkoutDataHydrator->toObject($data);
    }

    /**
     * Persist service selection with reference to a Quote Address ID.
     *
     * @param int $quoteId
     * @param ServiceSelectionInterface[] $serviceSelection
     */
    public function setServiceSelection(int $quoteId, array $serviceSelection)
    {
        // @TODO Persist service selection to DB (@see DHLGW-202)

        return;
    }
}
