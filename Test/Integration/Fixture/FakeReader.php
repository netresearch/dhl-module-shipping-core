<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Magento\Framework\Config\ReaderInterface;

class FakeReader implements ReaderInterface
{
    /**
     * @param null $scope
     * @return array
     */
    public function read($scope = null)
    {
        return \json_decode(file_get_contents(__DIR__ . '/Data/fake_packaging_data.json'), true);
    }
}
