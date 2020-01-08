<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\OrderExport;

/**
 * Interface ServiceDataInterface
 *
 * A DTO with package parameter service rendering data for carriers that support it
 *
 * @api
 */
interface ServiceDataInterface
{
    const CODE = 'code';
    const DETAILS = 'details';

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @return \Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface[]
     */
    public function getDetails(): array;
}
