<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\DayValidator;

use DateTime;
use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\DayValidatorInterface;
use Exception;
use Magento\Framework\Locale\ResolverInterfaceFactory;
use Magento\Sales\Model\Order;
use Yasumi\Provider\AbstractProvider;
use Yasumi\Yasumi;

/**
 * NoHoliday validator class. This class checks if the given date/time is NOT a holiday.
 *
 * @package Dhl\ShippingCore\Model\DayFilter
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class NoHoliday implements DayValidatorInterface
{
    const DEFAULT_COUNTRY = 'US';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ResolverInterfaceFactory
     */
    private $localeResolverFactory;

    /**
     * NoHoliday constructor.
     *
     * @param ConfigInterface $config
     * @param ResolverInterfaceFactory $localeResolverFactory
     */
    public function __construct(
        ConfigInterface $config,
        ResolverInterfaceFactory $localeResolverFactory
    ) {
        $this->config = $config;
        $this->localeResolverFactory = $localeResolverFactory;
    }

    /**
     * Returns the holiday provider instance.
     *
     * @param Order $order
     * @param DateTime $dateTime
     *
     * @return AbstractProvider|null
     */
    private function getHolidayProvider(Order $order, DateTime $dateTime)
    {
        try {
            $year      = (int) $dateTime->format('Y');
            $locale    = $this->localeResolverFactory->create()->getLocale();
            $countryId = $this->config->getOriginCountry($order->getStoreId());

            return Yasumi::createByISO3166_2($countryId, $year, $locale);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Returns TRUE if the date is NOT a holiday otherwise FALSE.
     *
     * @param Order    $order    The current order
     * @param DateTime $dateTime The date/time object to check
     *
     * @return bool
     */
    public function validate(Order $order, DateTime $dateTime): bool
    {
        $holidayProvider = $this->getHolidayProvider($order, $dateTime);
        return $holidayProvider === null ? true : !$holidayProvider->isHoliday($dateTime);
    }
}
