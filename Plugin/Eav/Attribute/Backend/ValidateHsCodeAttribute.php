<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Eav\Attribute\Backend;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\LocalizedException;

class ValidateHsCodeAttribute
{
    /**
     * @param AbstractBackend $backendModel
     * @param bool $result
     * @param mixed $eavEntity
     * @return bool
     * @throws LocalizedException
     */
    public function afterValidate(AbstractBackend $backendModel, bool $result, $eavEntity): bool
    {
        if (!$eavEntity instanceof Product) {
            return $result;
        }

        $attrCode = $backendModel->getAttribute()->getAttributeCode();
        if ($attrCode !== Constants::ATTRIBUTE_CODE_TARIFF_NUMBER) {
            return $result;
        }

        $value = $eavEntity->getData($attrCode);
        if (!$value) {
            return $result;
        }

        $label = $backendModel->getAttribute()->getData('frontend_label');

        if (!is_numeric($value)) {
            throw new LocalizedException(__('The value of attribute "%1" must be numeric.', $label));
        }

        // only allow digits that have a length of 6, 8 or 10.
        if (!\in_array(strlen((string) $value), [6, 8, 10], true)) {
            throw new LocalizedException(
                __('The value of attribute "%1" must be either 6, 8 or 10 digits long.', $label)
            );
        }

        return $result;
    }
}
