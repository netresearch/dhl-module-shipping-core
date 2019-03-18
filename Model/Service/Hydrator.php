<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutData;
use Magento\Framework\ObjectManagerInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Class Hydrator
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class Hydrator
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string $type
     * @param array $data
     * @return object   An instance of type $type
     */
    public function hydrate(string $type, array $data)
    {
        foreach ($data as $argument) {

        }
    }
}
