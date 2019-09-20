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
 * @package  Dhl\ShippingCore\Model
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class InvoiceTotal extends AbstractTotal
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

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
        $this->totalsManager->transferAdditionalFees(
            $invoice->getOrder(),
            $invoice
        );

        return $this;
    }
}
