<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region;
use Magento\Framework\Exception\RuntimeException;
use Yasumi\Yasumi;

class HolidayCalculator
{
    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var Region
     */
    private $regionResource;

    /**
     * Mapping of Magento's region codes to ISO 3166-2 region codes.
     *
     * @var \string[][]
     */
    private $regionCodes = [
        'DE' => [
            'NDS' => 'NI',
            'BAW' => 'BW',
            'BAY' => 'BY',
            'BER' => 'BE',
            'BRG' => 'BB',
            'BRE' => 'HB',
            'HAM' => 'HH',
            'HES' => 'HE',
            'MEC' => 'MV',
            'NRW' => 'NW',
            'RHE' => 'RP',
            'SAR' => 'SL',
            'SAS' => 'SN',
            'SAC' => 'ST',
            'SCN' => 'SH',
            'THE' => 'TH,'
        ]
    ];

    public function __construct(RegionFactory $regionFactory, Region $regionResource)
    {
        $this->regionFactory = $regionFactory;
        $this->regionResource = $regionResource;
    }

    /**
     * Check if given date is a holiday based on Magento \Magento\Directory information.
     *
     * @param \DateTimeInterface $date
     * @param string $countryCode
     * @param int $regionId
     *
     * @return bool
     * @throws RuntimeException
     */
    public function isHoliday(\DateTimeInterface $date, string $countryCode, int $regionId): bool
    {
        $isoCode = $countryCode;
        $year = (int)$date->format('Y');

        $regionCodes = $this->regionCodes[$countryCode] ?? [];

        if (!empty($regionCodes)) {
            $region = $this->regionFactory->create();
            $this->regionResource->load($region, $regionId);

            if (isset($regionCodes[$region->getCode()])) {
                $isoCode = sprintf('%s-%s', $countryCode, $regionCodes[$region->getCode()]);
            }
        }

        try {
            $holidayProvider = Yasumi::createByISO3166_2($isoCode, $year);
            return $holidayProvider->isHoliday($date);
        } catch (\Exception $exception) {
            throw new RuntimeException(
                __('Unable to retrieve year %1 holidays for region %2.', $year, $isoCode),
                $exception
            );
        }
    }
}
