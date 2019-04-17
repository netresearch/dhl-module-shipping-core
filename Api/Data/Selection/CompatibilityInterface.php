<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface CompatibilityInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CompatibilityInterface
{
    /**
     * Returns a list of service codes or compound codes ({serviceCode}.{inputCode})
     * that should be affected by this rule.
     *
     * When not using a compound code to target a specific input, all inputs of the service will be treated as
     * subjects.
     *
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

    /**
     * Returns a list of service codes or compund codes ({serviceCode}.{inputCode})
     * that will influence the rule's subjects, but will never be disabled or hidden
     * by other subjects or masters (by this rule!). Use this to establish a hierarchy for
     * compatibility rules, e.g. one service depending upon another.
     *
     * @example Given:
     *          $incompatibilityRule = false;
     *          $masters = ['service1'];
     *          $subjects = ['service2', 'service3'] = true;
     *      When nothing is selected, only 'service1' will be visible/enabled. When selecting 'service1', services
     *      'service2' and 'service3' will become visible/enabled, too.
     * @return string[]
     */
    public function getMasters(): array;

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
}
