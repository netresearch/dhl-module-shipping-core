<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Magento\Framework\Config\FileIterator;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * Class FileResolver
 */
class FileResolver implements FileResolverInterface
{
    /**
     * Module configuration file reader
     *
     * @var DirReader
     */
    private $moduleReader;

    /**
     * Constructor
     *
     * @param DirReader $moduleReader
     */
    public function __construct(DirReader $moduleReader)
    {
        $this->moduleReader = $moduleReader;
    }

    /**
     * Retrieve the list of configuration files with given name that relate to specified scope
     *
     * @param string $filename
     * @param string $scope
     * @return FileIterator
     */
    public function get($filename, $scope): FileIterator
    {
        return $this->moduleReader->getConfigurationFiles($filename);
    }
}
