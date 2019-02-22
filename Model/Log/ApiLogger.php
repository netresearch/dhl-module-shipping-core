<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Log;

use Magento\Framework\Logger\Monolog;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ApiLogger extends Monolog
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
     * ApiLogger constructor.
     * @param string $logEnabledConfigPath
     * @param string $logLevelConfigPath
     * @param ScopeConfigInterface $scopeConfig
     * @param $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        $name,
        string $logEnabledConfigPath,
        string $logLevelConfigPath,
        ScopeConfigInterface $scopeConfig,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);

        $this->logEnabledConfigPath = $logEnabledConfigPath;
        $this->logLevelConfigPath = $logLevelConfigPath;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param mixed[] $context
     * @return bool
     */
    public function log($level, $message, array $context = []): bool
    {
        $loggingEnabled = (bool) $this->scopeConfig->getValue($this->logEnabledConfigPath);
        $logLevel = (int) $this->scopeConfig->getValue($this->logLevelConfigPath);

        if ($loggingEnabled && $logLevel === $level) {
            return parent::log($level, $message, $context);
        }

        return false;
    }
}
