<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Dhl\ShippingCore\Api\Util\UnitConverterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Psr\Log\LoggerInterface;

class TotalsManager
{
    const ADDITIONAL_FEE_FIELD_NAME = 'dhlgw_additional_fee';
    const ADDITIONAL_FEE_INCL_TAX_FIELD_NAME = 'dhlgw_additional_fee_incl_tax';
    const ADDITIONAL_FEE_BASE_FIELD_NAME = 'base_dhlgw_additional_fee';
    const ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME = 'base_dhlgw_additional_fee_incl_tax';

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * @var DisplayObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TotalsManager constructor.
     *
     * @param UnitConverterInterface $unitConverter
     * @param DisplayObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        UnitConverterInterface $unitConverter,
        DisplayObjectFactory $dataObjectFactory,
        LoggerInterface $logger
    ) {
        $this->unitConverter = $unitConverter;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * @param Total $total
     * @param float $baseFee
     * @param float $baseFeeInclTax
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @return Total
     */
    public function addFeeToTotal(
        Total $total,
        float $baseFee,
        float $baseFeeInclTax,
        string $baseCurrency,
        string $quoteCurrency
    ): Total {
        try {
            $fee = $this->unitConverter->convertMonetaryValue(
                $baseFee,
                $baseCurrency,
                $quoteCurrency
            );
            $feeInclTax = $this->unitConverter->convertMonetaryValue(
                $baseFeeInclTax,
                $baseCurrency,
                $quoteCurrency
            );
        } catch (NoSuchEntityException $e) {
            $msg = "An error occurred while converting fee amount from {$baseCurrency} to {$quoteCurrency}.";
            $this->logger->error("$msg {$e->getLogMessage()}");

            return $total;
        }

        $total->setTotalAmount(self::ADDITIONAL_FEE_FIELD_NAME, $fee);
        $total->setBaseTotalAmount(self::ADDITIONAL_FEE_FIELD_NAME, $baseFee);
        $total->setData(self::ADDITIONAL_FEE_FIELD_NAME, $fee);
        $total->setData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME, $feeInclTax);
        $total->setData(self::ADDITIONAL_FEE_BASE_FIELD_NAME, $baseFee);
        $total->setData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME, $baseFeeInclTax);

        return $total;
    }

    /**
     * @param Quote|Order|Total $source
     * @param Creditmemo|Invoice|Quote|Order $destination
     */
    public function transferAdditionalFees($source, $destination)
    {
        $amount = $source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
        $amountInclTax = $source->getData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME);
        $baseAmount = $source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);
        $baseAmountInclTax = $source->getData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME);

        if ($baseAmount === null) {
            return;
        }

        $destination->setData(self::ADDITIONAL_FEE_FIELD_NAME, $amount);
        $destination->setData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME, $amountInclTax);
        $destination->setData(self::ADDITIONAL_FEE_BASE_FIELD_NAME, $baseAmount);
        $destination->setData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME, $baseAmountInclTax);

        if ($destination instanceof Invoice || $destination instanceof Creditmemo) {
            if (!$destination->isLast()) {
                /**
                 * If sales document is not the final entity on the order Magento is confused about tax amounts,
                 * fix this here by adding the total incl tax to the grand total
                 * and the fee tax amounts to the total tax amount
                 */
                $baseFeeTaxAmount = (float) $source->getData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
                                    - (float) $source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);
                $feeTaxAmount = (float) $source->getData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
                                - (float) $source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
                $destination->setTaxAmount($source->getTaxAmount() + $feeTaxAmount);
                $destination->setBaseTaxAmount($source->getBaseTaxAmount() + $baseFeeTaxAmount);

                $destination->setBaseGrandTotal(
                    $destination->getBaseGrandTotal()
                    + $destination->getData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
                );
                $destination->setGrandTotal(
                    $destination->getGrandTotal()
                    + $destination->getData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
                );
            } else {
                /**
                 * If this is the last instance of the sales entity type on that order, just add the fee (excl tax)
                 * to the grand total
                 */
                $destination->setBaseGrandTotal(
                    $destination->getBaseGrandTotal() + $destination->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME)
                );
                $destination->setGrandTotal(
                    $destination->getGrandTotal() + $destination->getData(self::ADDITIONAL_FEE_FIELD_NAME)
                );
            }
        }
    }

    /**
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @param string $code
     * @param string $label
     * @return DisplayObject|null
     */
    public function createTotalDisplayObject($source, string $code, string $label)
    {
        $amount = (float) $source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
        $amountInclTax = (float) $source->getData(self::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME);
        $baseAmount = (float) $source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);
        $baseAmountInclTax = (float) $source->getData(self::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME);

        if ($baseAmount === 0) {
            return null;
        }

        return $this->dataObjectFactory->create(
            [
                'data' => [
                    'code' => $code,
                    'value' => $amount,
                    'value_incl_tax' => $amountInclTax,
                    'base_value' => $baseAmount,
                    'base_value_incl_tax' => $baseAmountInclTax,
                    'label' => __($label),
                ],
            ]
        );
    }
}
