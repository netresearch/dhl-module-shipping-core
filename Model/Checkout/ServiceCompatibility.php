<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\ServiceCompatibilityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ServiceCompatibility
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class ServiceCompatibility implements ServiceCompatibilityInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]
     */
    private $subject;

    /**
     * ServiceCompatibility constructor.
     *
     * @param string $type
     * @param string[] $subject
     */
    public function __construct(string $type, array $subject)
    {
        $this->type = $type;
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getSubject(): array
    {
        return $this->subject;
    }
}
