<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\ItemValidator;

use Magento\Framework\Phrase;

trait DhlSection
{
    public function getSectionCode(): string
    {
        return Section::CODE;
    }

    public function getSectionName(): Phrase
    {
        return __('Post & DHL Shipping');
    }
}
