<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Dhl\ShippingCore\Util\UnitConverter;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class TotalsManager
 *
 * @package Dhl\ShippingCore\Model\AdditionalFee
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class TotalsManager
{
    const ADDITIONAL_FEE_FIELD_NAME = 'dhlgw_additional_fee';
    const ADDITIONAL_FEE_BASE_FIELD_NAME = 'base_dhlgw_additional_fee';

    /**
     * @var UnitConverter
     */
    private $unitConverter;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * TotalsManager constructor.
     *
     * @param UnitConverter $unitConverter
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(UnitConverter $unitConverter, DataObjectFactory $dataObjectFactory)
    {
        $this->unitConverter = $unitConverter;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param Total $total
     * @param float $baseFee
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @return Total
     * @throws NoSuchEntityException
     */
    public function addFeeToTotal(
        Total $total,
        float $baseFee,
        string $baseCurrency,
        string $quoteCurrency
    ): Total {
        $fee = $this->unitConverter->convertMonetaryValue(
            $baseFee,
            $baseCurrency,
            $quoteCurrency
        );

        $total->setTotalAmount(self::ADDITIONAL_FEE_FIELD_NAME, $fee);
        $total->setBaseTotalAmount(self::ADDITIONAL_FEE_FIELD_NAME, $baseFee);
        $total->setData(self::ADDITIONAL_FEE_FIELD_NAME, $fee);
        $total->setData(self::ADDITIONAL_FEE_BASE_FIELD_NAME, $baseFee);

        return $total;
    }

    /**
     * @param Quote|Order|Total $source
     * @param Creditmemo|Invoice|Quote|Order $destination
     */
    public function transferAdditionalFees($source, $destination)
    {
        $amount = $source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
        $baseAmount = $source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);

        if (!$baseAmount || !$amount) {
            return;
        }

        $destination->setData(self::ADDITIONAL_FEE_FIELD_NAME, $amount);
        $destination->setData(self::ADDITIONAL_FEE_BASE_FIELD_NAME, $baseAmount);

        if (!($destination instanceof Order)) {
            $destination->setGrandTotal($destination->getGrandTotal() + $amount);
            $destination->setBaseGrandTotal($destination->getBaseGrandTotal() + $baseAmount);
        }
    }


    /**
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @param string $code
     * @param string $label
     * @return DataObject
     */
    public function createTotalDisplayObject($source, string $code, string $label): DataObject
    {
        $fee = (float)$source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
        $baseFee = (float)$source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);

        return $this->dataObjectFactory->create(
            [
                'data' => [
                    'code' => $code,
                    'value' => $fee,
                    'base_value' => $baseFee,
                    'label' => __($label)
                ]
            ]
        );
    }
}
