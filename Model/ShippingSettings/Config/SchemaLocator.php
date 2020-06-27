<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * Module configuration file reader
     *
     * @var Reader
     */
    private $reader;

    /**
     * SchemaLocator constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema(): string
    {
        return $this->reader->getModuleDir(Dir::MODULE_ETC_DIR, 'Dhl_ShippingCore') . '/shipping_settings.xsd';
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
