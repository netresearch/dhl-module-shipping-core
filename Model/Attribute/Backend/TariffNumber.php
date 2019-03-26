<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TariffNumber
 * @package Dhl\ShippingCore\Model\Attribute\Backend
 */
class TariffNumber extends AbstractBackend
{
    const CODE = 'dhl_tariff_number';

    /**
     * @inheritdoc
     * @param \Magento\Framework\DataObject $object
     */
    public function validate($object): bool
    {
        $value = $object->getData(self::CODE);
        $frontendLabel = $this->getAttribute()->getData('frontend_label');
        if ($value !== '' && !is_numeric($value)) {
            throw new LocalizedException(
                __(
                    'The value of attribute "%1" must be numeric',
                    $frontendLabel
                )
            );
        }
        if (strlen((string)$value) > 11) {
            throw new LocalizedException(
                __(
                    'The value of attribute "%1" must be not be longer than 11 digits',
                    $frontendLabel
                )
            );
        }
        return parent::validate($object);
    }
}
