<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface CompatibilityInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface CompatibilityInterface
{
    /**
     * Return true if the compatibility rule describes an incompatibilty between subjects.
     * Otherwise, the subjects should be treated as requiring each other.
     *
     * @return bool
     */
    public function isIncompatibilityRule(): bool;

    /**
     * Return true to enforce this rule by hiding instead of just disabling items.
     *
     * @return bool
     */
    public function isHideSubjects(): bool;

    /**
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * Error message to display if the compatibility rule is violated.
     * Will replace "%1" with a list of subjects.
     *
     * @example "The services %1 require each other."
     * @example "Please choose only one of %1."
     *
     * @return string
     */
    public function getErrorMessage(): string;
}
