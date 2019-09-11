<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Dhl\ShippingCore\Api\UnitConverterInterface;
use Dhl\ShippingCore\Model\AdditionalFeeManagement;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order;

/**
 * Sales Order Total.
 *
 * @package  Dhl\ShippingCore\Model
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Total extends Address\Total\AbstractTotal
{
    const SERVICE_CHARGE_TOTAL_CODE = 'dhlgw_additional_fee';

    /**
     * @var string
     */
    protected $_code = self::SERVICE_CHARGE_TOTAL_CODE;

    /**
     * @var AdditionalFeeManagement
     */
    private $additionalFeeManagement;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * Total constructor.
     *
     * @param AdditionalFeeManagement $additionalFeeManagement
     * @param UnitConverterInterface $unitConverter
     * @param TotalsManager $totalsManager
     */
    public function __construct(
        AdditionalFeeManagement $additionalFeeManagement,
        UnitConverterInterface $unitConverter,
        TotalsManager $totalsManager
    ) {
        $this->additionalFeeManagement = $additionalFeeManagement;
        $this->unitConverter = $unitConverter;
        $this->totalsManager = $totalsManager;
    }

    /**
     * @param string|null $shippingMethod
     * @return string
     */
    public function getLabel(string $shippingMethod = null): string
    {
        if ($shippingMethod === null) {
            /**
             * We are usually in control of calls to this function and can
             * pass a shipping method. For the theoretical edge case where
             * Magento Core calls this method, we return an empty string.
             */
            return '';
        }
        $carrierCode = strtok($shippingMethod, '_');
        return $this->additionalFeeManagement->getLabel($carrierCode);
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return self
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total): self
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->additionalFeeManagement->isActive($quote)) {
            return $this;
        }

        $baseFee = $this->additionalFeeManagement->getTotalAmount($quote);
        try {
            $total = $this->totalsManager->addFeeToTotal(
                $total,
                $baseFee,
                $quote->getBaseCurrencyCode(),
                $quote->getQuoteCurrencyCode()
            );

            $this->totalsManager->transferAdditionalFees($total, $quote);
        } catch (NoSuchEntityException $e) {
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Address\Total $total
     * @return mixed[]
     */
    public function fetch(Quote $quote, Address\Total $total): array
    {
        $result = [];
        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress->getId() || !$this->additionalFeeManagement->isActive($quote)) {
            return $result;
        }

        $baseFee = $this->additionalFeeManagement->getTotalAmount($quote);
        $fee = $this->unitConverter->convertMonetaryValue(
            $baseFee,
            $quote->getBaseCurrencyCode(),
            $quote->getQuoteCurrencyCode()
        );

        if ($fee > 0.0) {
            $result = [
                'code' => $this->getCode(),
                'title' => __($this->getLabel($quote->getShippingAddress()->getShippingMethod())),
                'value' => $fee,
            ];
        }

        return $result;
    }

    /**
     * Generate an object that is used by the Magento core
     * to render the custom total.
     *
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @return DataObject
     */
    public function createTotalDisplayObject($source): DataObject
    {
        if ($source->getOrder()) {
            $shippingMethod = $source->getOrder()->getShippingMethod();
        } else {
            $shippingMethod = $source->getShippingMethod();
        }

        return $this->totalsManager->createTotalDisplayObject(
            $source,
            $this->getCode(),
            $this->getLabel($shippingMethod)
        );
    }
}
