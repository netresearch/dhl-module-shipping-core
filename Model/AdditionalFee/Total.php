<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Dhl\ShippingCore\Api\TaxConfigInterface;
use Dhl\ShippingCore\Api\UnitConverterInterface;
use Dhl\ShippingCore\Model\AdditionalFeeManagement;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

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
    const DHLGW_FEE_TAX_TYPE = 'dhlgw_fee';

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
     * @var TaxHelper
     */
    private $taxHelper;

    /**
     * @var TaxConfigInterface
     */
    private $taxConfig;

    /**
     * @var TaxCalculationInterface
     */
    private $taxCalculation;

    /**
     * Total constructor.
     *
     * @param AdditionalFeeManagement $additionalFeeManagement
     * @param UnitConverterInterface $unitConverter
     * @param TotalsManager $totalsManager
     * @param TaxHelper $taxHelper
     * @param TaxConfigInterface $taxConfig
     * @param TaxCalculationInterface $taxCalculation
     */
    public function __construct(
        AdditionalFeeManagement $additionalFeeManagement,
        UnitConverterInterface $unitConverter,
        TotalsManager $totalsManager,
        TaxHelper $taxHelper,
        TaxConfigInterface $taxConfig,
        TaxCalculationInterface $taxCalculation
    ) {
        $this->additionalFeeManagement = $additionalFeeManagement;
        $this->unitConverter = $unitConverter;
        $this->totalsManager = $totalsManager;
        $this->taxHelper = $taxHelper;
        $this->taxConfig = $taxConfig;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * @param string|null $shippingMethod
     * @return Phrase
     */
    public function getLabel(string $shippingMethod = null): Phrase
    {
        if ($shippingMethod === null) {
            /**
             * We are usually in control of calls to this function and can
             * pass a shipping method. For the theoretical edge case where
             * Magento Core calls this method, we return an empty string.
             */
            return _('');
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

        if ($baseFee > 0) {
            $taxClass = $this->taxHelper->getShippingTaxClass($quote->getStoreId());

            $taxRate = $this->taxCalculation->getCalculatedRate($taxClass);
            if ($this->taxConfig->isShippingPriceInclTax($quote->getStoreId())) {
                // price includes tax, deduct tax from total
                $baseFeeInclTax = $baseFee;
                $baseFee = $baseFee * 100 / ($taxRate + 100);
            } else {
                $baseFeeInclTax = $baseFee * ($taxRate + 100) / 100;
            }

            $total = $this->totalsManager->addFeeToTotal(
                $total,
                $baseFee,
                $baseFeeInclTax,
                $quote->getBaseCurrencyCode(),
                $quote->getQuoteCurrencyCode()
            );

            /**
             * add additional tax information to quote
             */
            $additionalFeeTaxInfo = [
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => self::DHLGW_FEE_TAX_TYPE,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $total->getBaseTotalAmount(
                    self::SERVICE_CHARGE_TOTAL_CODE
                ),
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $total->getTotalAmount(
                    self::SERVICE_CHARGE_TOTAL_CODE
                ),
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => false,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => self::SERVICE_CHARGE_TOTAL_CODE,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE => null,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => 1,
                CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $taxClass,
            ];

            /** @var string[][] $associates */
            $associates = $quote->getShippingAddress()->getAssociatedTaxables() ?? [];
            $associates[] = $additionalFeeTaxInfo;
            $quote->getShippingAddress()->setAssociatedTaxables($associates);

            $this->totalsManager->transferAdditionalFees($total, $quote);
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
                /**
                 * We need to use a Phrase object here, otherwise we get no title
                 *
                 * @see \Magento\Quote\Model\Cart\TotalsConverter::process
                 */
                'title' => $this->getLabel($shippingAddress->getShippingMethod()),
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
     * @return DisplayObject|null
     */
    public function createTotalDisplayObject($source)
    {
        if ($source->getOrder()) {
            $shippingMethod = $source->getOrder()->getShippingMethod();
        } else {
            $shippingMethod = $source->getShippingMethod();
        }

        return $this->totalsManager->createTotalDisplayObject(
            $source,
            $this->getCode(),
            $this->getLabel($shippingMethod)->render()
        );
    }
}
