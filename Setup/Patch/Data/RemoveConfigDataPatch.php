<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Dhl\ShippingCore\Setup\Module\Uninstaller;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class RemoveConfigDataPatch implements PatchRevertableInterface, DataPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    #[\Override]
    public static function getDependencies(): array
    {
        return [];
    }

    #[\Override]
    public function getAliases(): array
    {
        return [];
    }

    #[\Override]
    public function apply()
    {
        return;
    }

    /**
     * Remove any configuration that is managed by this extension
     */
    #[\Override]
    public function revert()
    {
        Uninstaller::deleteConfig($this->schemaSetup);
    }
}
