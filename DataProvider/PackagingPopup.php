<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;

class PackagingPopup extends AbstractDataProvider
{
    public function getData(): array
    {
        return [ 'items' => [
            'Hello',
            'Darkness',
            'my',
            'old',
            'friend'
            ],
        ];
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return;
    }

}