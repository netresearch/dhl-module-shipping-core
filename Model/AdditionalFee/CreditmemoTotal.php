<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Creditmemo Total.
 *
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/<
 */
class CreditmemoTotal extends AbstractTotal
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * CreditmemoTotal constructor.
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
     * @param Creditmemo $creditmemo
     * @return self|AbstractTotal
     */
    public function collect(Creditmemo $creditmemo)
    {
        foreach ($creditmemo->getOrder()->getCreditmemosCollection() as $previousCreditmemo) {
            if ((float) $previousCreditmemo->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME) > 0) {
                // in case the additional fee has already been refunded, do not add it to another creditmemo
                return $this;
            }
        }

        $this->totalsManager->transferAdditionalFees(
            $creditmemo->getOrder(),
            $creditmemo
        );

        return $this;
    }
}
