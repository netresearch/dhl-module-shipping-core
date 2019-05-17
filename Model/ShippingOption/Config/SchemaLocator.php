<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * Class SchemaLocator
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * Module configuration file reader
     *
     * @var DirReader
     */
    private $moduleReader;

    /**
     * SchemaLocator constructor.
     *
     * @param DirReader $moduleReader
     */
    public function __construct(DirReader $moduleReader)
    {
        $this->moduleReader = $moduleReader;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema(): string
    {
        return $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Dhl_ShippingCore') . '/shipping_options.xsd';
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema(): string
    {
        return $this->getSchema();
    }
}
