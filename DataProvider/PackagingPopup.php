<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class PackagingPopup
 *
 * @package Dhl\ShippingCore\DataProvider
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class PackagingPopup extends AbstractDataProvider
{
    public function getData(): array
    {
        return [
            'service' => $this->getServiceInputs(),
            'package' => array_merge($this->getPackageInputs(), $this->getExportPackageInputs()),
            'items' => array_merge($this->getItemInputs(), $this->getExportItemInputs()),
        ];
    }

    private function getServiceInputs(): array
    {
        /** @TODO: use provider classes for input retrieval */
        return [
            'Hello',
            'Darkness',
            'my',
            'old',
            'friend',
        ];
    }

    private function getPackageInputs(): array
    {
        /** @TODO provide default package inputs */
        return [];
    }

    private function getExportPackageInputs(): array
    {
        /** @TODO: use provider classes for input retrieval */
        return [];
    }

    private function getItemInputs(): array
    {
        /** @TODO: provide default inputs */
        return [];
    }

    private function getExportItemInputs(): array
    {
        /** @TODO: use provider classes for input retrieval */
        return [];
    }

}