<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Invoice Total.
 *
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class InvoiceTotal extends AbstractTotal
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * InvoiceTotal constructor.
     *
     * @param TotalsManager $totalsManager
     * @param array $data
     */
    public function __construct(TotalsManager $totalsManager, array $data = [])
    {
        $this->totalsManager = $totalsManager;

        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice): self
    {
        /** @var Invoice $previousInvoice */
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
            if ((float) $previousInvoice->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME) > 0) {
                // in case the additional fee has already been invoiced, do not add it to another invoice
                return $this;
            }
        }

        $this->totalsManager->transferAdditionalFees(
            $invoice->getOrder(),
            $invoice
        );

        return $this;
    }
}
