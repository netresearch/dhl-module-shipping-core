<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface FootnoteInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface FootnoteInterface
{
    /**
     * @return string
     */
    public function getId(): string;
    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * @return bool
     */
    public function isSubjectsMustBeSelected(): bool;

    /**
     * @return bool
     */
    public function isSubjectsMustBeAvailable(): bool;
}
