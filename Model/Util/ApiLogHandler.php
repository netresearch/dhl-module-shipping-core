<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;

/**
 * Class ApiLogHandler
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ApiLogHandler extends Base
{
    /**
     * @var string
     */
    private $logEnabledConfigPath;

    /**
     * @var string
     */
    private $logLevelConfigPath;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ApiLogHandler constructor.
     *
     * @param DriverInterface $filesystem
     * @param ApiLogAnonymizer $anonymizer
     * @param string $logEnabledConfigPath
     * @param string $logLevelConfigPath
     * @param ScopeConfigInterface $scopeConfig
     * @param string|null $filePath
     * @param string|null $fileName
     * @throws \Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        ApiLogAnonymizer $anonymizer,
        string $logEnabledConfigPath,
        string $logLevelConfigPath,
        ScopeConfigInterface $scopeConfig,
        string $filePath = null,
        string $fileName = null
    ) {
        parent::__construct($filesystem, $filePath, $fileName);

        $this->logEnabledConfigPath = $logEnabledConfigPath;
        $this->logLevelConfigPath = $logLevelConfigPath;
        $this->scopeConfig = $scopeConfig;

        $this->pushProcessor($anonymizer);
    }

    /**
     * @inheritDoc
     */
    public function isHandling(array $record): bool
    {
        $loggingEnabled = (bool) $this->scopeConfig->getValue($this->logEnabledConfigPath);
        $logLevel = (int) $this->scopeConfig->getValue($this->logLevelConfigPath);

        return $loggingEnabled && $record['level'] >= $logLevel && parent::isHandling($record);
    }
}
