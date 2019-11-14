<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Creditmemo;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtensionInterfaceFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterface;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Class AddTotalsAsExtensionAttributes
 *
 * The additional totals columns in `sales_creditmemo` are necessary
 * for totals calculations but are not part of the `CreditmemoInterface`.
 * In order to make the totals available for reading, this class shifts
 * them to extension attributes.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class AddTotalsAsExtensionAttributes
{
    /**
     * @var CreditmemoExtensionInterfaceFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * AddTotalsAsExtensionAttributes constructor.
     * @param CreditmemoExtensionInterfaceFactory $creditmemoExtensionFactory
     */
    public function __construct(CreditmemoExtensionInterfaceFactory $creditmemoExtensionFactory)
    {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * Shift totals columns to extension attributes when reading a single credit memo.
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoInterface
     */
    public function afterGet(
        CreditmemoRepositoryInterface $subject,
        CreditmemoInterface $creditmemo
    ): CreditmemoInterface {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->creditmemoExtensionFactory->create();
        }
        /** @var Creditmemo $creditmemo */
        $extensionAttributes->setBaseDhlgwAdditionalFee(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $extensionAttributes->setDhlgwAdditionalFee(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $extensionAttributes->setBaseDhlgwAdditionalFeeInclTax(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $extensionAttributes->setDhlgwAdditionalFeeInclTax(
            $creditmemo->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );
        $creditmemo->setExtensionAttributes($extensionAttributes);

        return $creditmemo;
    }

    /**
     * Shift totals columns to extension attributes when reading a list of credit memos.
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoSearchResultInterface $searchResult
     * @return CreditmemoSearchResultInterface
     */
    public function afterGetList(
        CreditmemoRepositoryInterface $subject,
        CreditmemoSearchResultInterface $searchResult
    ): CreditmemoSearchResultInterface {
        foreach ($searchResult->getItems() as $creditmemo) {
            $this->afterGet($subject, $creditmemo);
        }

        return $searchResult;
    }
}
