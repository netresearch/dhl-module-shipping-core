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
 * @package  Dhl\ShippingCore\Model
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/<
 */
class CreditmemoTotal extends AbstractTotal
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
     * @param Creditmemo $creditmemo
     * @return $this|AbstractTotal
     */
    public function collect(Creditmemo $creditmemo)
    {
        $this->totalsManager->transferAdditionalFees(
            $creditmemo->getOrder(),
            $creditmemo
        );

        return $this;
    }
}
