<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Attribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ExportDescription
 * @package Dhl\ShippingCore\Model\Attribute\Backend
 */
class ExportDescription extends AbstractBackend
{
    const CODE = 'dhl_export_description';
    const MAX_LENGTH = 50;

    /**
     * @param \Magento\Framework\DataObject $object
     * @return bool
     * @throws LocalizedException
     */
    public function validate($object)
    {
        $value = $object->getData(self::CODE);
        $frontendLabel = $this->getAttribute()->getData('frontend_label');
        if (strlen((string)$value) > static::MAX_LENGTH) {
            throw new LocalizedException(
                __(
                    'The value of attribute "%1" must be not be longer than %2 characters',
                    $frontendLabel,
                    static::MAX_LENGTH
                )
            );
        }

        return parent::validate($object);
    }
}
