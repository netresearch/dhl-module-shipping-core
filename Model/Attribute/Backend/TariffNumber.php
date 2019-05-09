<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TariffNumber
 *
 * @package Dhl\ShippingCore\Model
 */
class TariffNumber extends AbstractBackend
{
    const CODE = 'dhlgw_tariff_number';

    /**
     * Validate HS Code input.
     *
     * @param DataObject $object
     * @return bool
     * @throws LocalizedException
     */
    public function validate($object): bool
    {
        $value = $object->getData(self::CODE);
        $label = $this->getAttribute()->getData('frontend_label');

        if (isset($value) && $value !== '' && !is_numeric($value)) {
            throw new LocalizedException(__('The value of attribute "%1" must be numeric.', $label));
        }

        if (strlen((string) $value) > 11) {
            throw new LocalizedException(__('The value of attribute "%1" must be no longer than 11 digits.', $label));
        }

        return parent::validate($object);
    }
}
