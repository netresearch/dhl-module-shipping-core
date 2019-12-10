<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentDate\Validator;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\ShipmentDate\DayValidatorInterface;
use Magento\Framework\Locale\ResolverInterfaceFactory;
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
     * @param \DateTime $dateTime
     * @param mixed $store
     *
     * @return AbstractProvider|null
     */
    private function getHolidayProvider(\DateTime $dateTime, $store = null)
    {
        try {
            $year      = (int) $dateTime->format('Y');
            $locale    = $this->localeResolverFactory->create()->getLocale();
            $countryId = $this->config->getOriginCountry($store);

            return Yasumi::createByISO3166_2($countryId, $year, $locale);
        } catch (\Exception $e) {
            return null;
        }
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
        $holidayProvider = $this->getHolidayProvider($dateTime, $store);
        return $holidayProvider === null ? true : !$holidayProvider->isHoliday($dateTime);
    }
}
