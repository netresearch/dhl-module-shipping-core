<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Tax;

use Dhl\ShippingCore\Model\AdditionalFee\Total;
use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Sales\Model\Order;
use Magento\Tax\Api\Data\OrderTaxDetailsItemInterface;
use Magento\Tax\Api\OrderTaxManagementInterface;

/**
 * For invoice handling fix the tax amounts from the service fee.
 */
class AddFeeTaxAmounts
{
    /**
     * @var OrderTaxManagementInterface
     */
    private $orderTaxManagement;

    /**
     * AddFeeTaxAmounts constructor.
     *
     * @param OrderTaxManagementInterface $orderTaxManagement
     */
    public function __construct(OrderTaxManagementInterface $orderTaxManagement)
    {
        $this->orderTaxManagement = $orderTaxManagement;
    }

    /**
     * @param \Magento\Tax\Helper\Data $subject
     * @param array $result
     * @param Order|Order\Invoice|Order\Creditmemo $source
     * @return mixed[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCalculatedTaxes(\Magento\Tax\Helper\Data $subject, array $result, $source): array
    {
        if ($source instanceof Order ||
            $source->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME) === null
        ) {
            // no total in sales document, do nothing
            return $result;
        }
        $order = $source->getOrder();
        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($order->getId());

        // Fetch original tax items
        /** @var OrderTaxDetailsItemInterface[] $items */
        $items = $orderTaxDetails->getItems() ?? [];
        $feeTax = array_filter(
            $items,
            function (OrderTaxDetailsItemInterface $item) {
                return $item->getType() === Total::DHLGW_FEE_TAX_TYPE;
            }
        );
        /** @var OrderTaxDetailsItemInterface $feeTax */
        $feeTax = array_shift($feeTax);
        if (!$feeTax) {
            // no fee tax amount registered, abort
            return $result;
        }
        foreach ($feeTax->getAppliedTaxes() as $tax) {
            // update tax classes (full tax summary) with the amounts from the order
            foreach ($result as $index => $resultTax) {
                if ($resultTax['percent'] === (string) $tax->getPercent()) {
                    $resultTax['tax_amount'] += $tax->getAmount();
                    $resultTax['base_tax_amount'] += $tax->getBaseAmount();
                    $result[$index] = $resultTax;
                }
            }
        }

        return $result;
    }
}
