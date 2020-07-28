<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentDate\Validator;

use Dhl\ShippingCore\Api\ShipmentDate\DayValidatorInterface;
use Dhl\ShippingCore\Api\ShippingConfigInterface;
use Dhl\ShippingCore\Model\Util\HolidayCalculator;
use Magento\Framework\Exception\RuntimeException;
use Psr\Log\LoggerInterface;

/**
 * NoHoliday validator class. This class checks if the given date/time is NOT a holiday.
 */
class NoHoliday implements DayValidatorInterface
{
    /**
     * @var ShippingConfigInterface
     */
    private $config;

    /**
     * @var HolidayCalculator
     */
    private $holidayCalculator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ShippingConfigInterface $config,
        HolidayCalculator $holidayCalculator,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->holidayCalculator = $holidayCalculator;
        $this->logger = $logger;
    }

    /**
     * Returns TRUE if the date is NOT a holiday otherwise FALSE.
     *
     * @param \DateTime $dateTime The date/time object to check
     * @param mixed $store The store to use for validation
     *
     * @return bool
     */
    public function validate(\DateTime $dateTime, $store = null): bool
    {
        try {
            return !$this->holidayCalculator->isHoliday(
                $dateTime,
                $this->config->getOriginCountry($store),
                $this->config->getOriginRegion($store)
            );
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getLogMessage());

            // failed to retrieve holiday information, must assume the date is not a holiday.
            return true;
        }
    }
}
