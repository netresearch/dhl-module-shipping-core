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
 * Class ExportDescription
 *
 */
class ExportDescription extends AbstractBackend
{
    const CODE = 'dhlgw_export_description';
    const MAX_LENGTH = 50;

    /**
     * Validate export description input.
     *
     * @param DataObject $object
     * @return bool
     * @throws LocalizedException
     */
    public function validate($object): bool
    {
        $value = $object->getData(self::CODE);
        $frontendLabel = $this->getAttribute()->getData('frontend_label');

        if (strlen((string) $value) > static::MAX_LENGTH) {
            throw new LocalizedException(
                __(
                    'The value of attribute "%1" must not be longer than %2 characters.',
                    $frontendLabel,
                    static::MAX_LENGTH
                )
            );
        }

        return parent::validate($object);
    }
}
