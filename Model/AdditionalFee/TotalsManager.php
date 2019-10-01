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
use Psr\Log\LoggerInterface;

/**
 * Class TotalsManager
 *
 * @package Dhl\ShippingCore\Model\AdditionalFee
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class TotalsManager
{
    const ADDITIONAL_FEE_FIELD_NAME = 'dhlgw_additional_fee';
    const ADDITIONAL_FEE_BASE_FIELD_NAME = 'dhlgw_additional_base_fee';

    /**
     * @var UnitConverter
     */
    private $unitConverter;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TotalsManager constructor.
     *
     * @param UnitConverter $unitConverter
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        UnitConverter $unitConverter,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger
    ) {
        $this->unitConverter = $unitConverter;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
    }

    /**
     * @param Total $total
     * @param float $baseFee
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @return Total
     */
    public function addFeeToTotal(
        Total $total,
        float $baseFee,
        string $baseCurrency,
        string $quoteCurrency
    ): Total {
        try {
            $fee = $this->unitConverter->convertMonetaryValue(
                $baseFee,
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

        if ($baseAmount === null) {
            return;
        }

        $destination->setData(self::ADDITIONAL_FEE_FIELD_NAME, (float)$amount);
        $destination->setData(self::ADDITIONAL_FEE_BASE_FIELD_NAME, (float)$baseAmount);

        if (!($destination instanceof Order)) {
            $destination->setGrandTotal($destination->getGrandTotal() + (float)$amount);
            $destination->setBaseGrandTotal($destination->getBaseGrandTotal() + (float)$baseAmount);
        }
    }

    /**
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @param string $code
     * @param string $label
     * @return DataObject|null
     */
    public function createTotalDisplayObject($source, string $code, string $label)
    {
        $fee = $source->getData(self::ADDITIONAL_FEE_FIELD_NAME);
        $baseFee = $source->getData(self::ADDITIONAL_FEE_BASE_FIELD_NAME);

        if ($baseFee === null) {
            return null;
        }

        return $this->dataObjectFactory->create(
            [
                'data' => [
                    'code' => $code,
                    'value' => (float)$fee,
                    'base_value' => (float)$baseFee,
                    'label' => $label
                ]
            ]
        );
    }
}
