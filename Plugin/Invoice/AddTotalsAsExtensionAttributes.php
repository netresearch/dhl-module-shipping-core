<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Invoice;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Sales\Api\Data\InvoiceExtensionInterfaceFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class AddTotalsAsExtensionAttributes
 *
 * The additional totals columns in `sales_invoice` are necessary
 * for totals calculations but are not part of the `InvoiceInterface`.
 * In order to make the totals available for reading, this class shifts
 * them to extension attributes.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class AddTotalsAsExtensionAttributes
{
    /**
     * @var InvoiceExtensionInterfaceFactory
     */
    private $invoiceExtensionFactory;

    /**
     * AddTotalsAsExtensionAttributes constructor.
     * @param InvoiceExtensionInterfaceFactory $orderExtensionFactory
     */
    public function __construct(InvoiceExtensionInterfaceFactory $orderExtensionFactory)
    {
        $this->invoiceExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Shift totals columns to extension attributes when reading a single invoice.
     *
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
     */
    public function afterGet(InvoiceRepositoryInterface $subject, InvoiceInterface $invoice): InvoiceInterface
    {
        $extensionAttributes = $invoice->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->invoiceExtensionFactory->create();
        }
        /** @var Invoice $invoice */
        $extensionAttributes->setBaseDhlgwAdditionalFee(
            $invoice->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $extensionAttributes->setBaseDhlgwAdditionalFeeInclTax(
            $invoice->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $extensionAttributes->setDhlgwAdditionalFee(
            $invoice->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setDhlgwAdditionalFeeInclTax(
            $invoice->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );
        $invoice->setExtensionAttributes($extensionAttributes);

        return $invoice;
    }

    /**
     * Shift totals columns to extension attributes when reading a list of invoices.
     *
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceSearchResultInterface $searchResult
     * @return InvoiceSearchResultInterface
     */
    public function afterGetList(
        InvoiceRepositoryInterface $subject,
        InvoiceSearchResultInterface $searchResult
    ): InvoiceSearchResultInterface {
        foreach ($searchResult->getItems() as $invoice) {
            $this->afterGet($subject, $invoice);
        }

        return $searchResult;
    }
}
