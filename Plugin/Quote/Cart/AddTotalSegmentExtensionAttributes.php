<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Quote\Cart;

use Dhl\ShippingCore\Model\AdditionalFee\Total;
use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Quote;

/**
 * Class AddTotalSegmentExtensionAttributes
 *
 * Add additional data to the service charge total segment of the quote
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class AddTotalSegmentExtensionAttributes
{
    /**
     * @var TotalSegmentExtensionFactory
     */
    private $extensionAttributeFactory;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * AddTotalSegmentExtensionAttributes constructor.
     *
     * @param TotalSegmentExtensionFactory $extensionAttributeFactory
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        TotalSegmentExtensionFactory $extensionAttributeFactory,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->extensionAttributeFactory = $extensionAttributeFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param CartTotalRepositoryInterface|Quote $subject
     * @param TotalsInterface $result
     * @param int $cartId
     * @return TotalsInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(
        CartTotalRepositoryInterface $subject,
        TotalsInterface $result,
        int $cartId
    ): TotalsInterface {
        if (!array_key_exists(Total::SERVICE_CHARGE_TOTAL_CODE, $result->getTotalSegments())) {
            return $result;
        }

        $feeSegment = $result->getTotalSegments()[Total::SERVICE_CHARGE_TOTAL_CODE];
        $extensionAttributes = $feeSegment->getExtensionAttributes();
        if ($extensionAttributes === null) {
            /** @var TotalSegmentExtensionInterface $extensionAttributes */
            $extensionAttributes = $this->extensionAttributeFactory->create();
        }
        $quote = $this->quoteRepository->get($cartId);
        $extensionAttributes->setDhlgwFee(
            (float) $quote->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setDhlgwFeeInclTax(
            (float) $quote->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );
        $feeSegment->setExtensionAttributes($extensionAttributes);
        $result->setTotalSegments(
            array_merge($result->getTotalSegments(), [Total::SERVICE_CHARGE_TOTAL_CODE => $feeSegment])
        );

        return $result;
    }
}
