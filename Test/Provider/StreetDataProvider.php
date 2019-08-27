<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Provider;

use Symfony\Component\Yaml\Yaml;

/**
 * Class StreetDataProvider
 *
 * @package Dhl\ShippingCore\Test
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class StreetDataProvider
{
    /**
     * @return string[][][]
     */
    public static function getStreetData(): array
    {
        $providerData = [];

        $input = Yaml::parse(file_get_contents(__DIR__ . '/../_files/providers/splitStreet.yaml'));
        $expectations = Yaml::parse(file_get_contents(__DIR__ . '/../_files/expectations/splitStreet.yaml'));
        $indexes = array_keys($input);

        foreach ($indexes as $index) {
            $providerData[$index] = [
                'street' => $input[$index],
                'expected' => $expectations[$index],
            ];
        }

        return $providerData;
    }
}
