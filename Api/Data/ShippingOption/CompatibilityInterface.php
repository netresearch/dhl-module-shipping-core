<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface CompatibilityInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CompatibilityInterface
{
    /**
     * May return the unique ID of the compatibility rule.
     * If the rule does not have a unique id, it will return ''.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns a list of shipping option codes or compound codes ({shippingOptionCode}.{inputCode})
     * that should be affected by this rule.
     *
     * When not using a compound code to target a specific input, all inputs of the shipping option will be treated as
     * subjects.
     *
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * Error message to display if the compatibility rule is violated.
     * Will replace "%1" with a list of subjects.
     *
     * @example "The shipping options %1 require each other."
     * @example "Please choose only one of %1."
     *
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * Returns a list of shipping option codes or compund codes ({shippingOptionCode}.{inputCode})
     * that will influence the rule's subjects, but will never be disabled or hidden
     * by other subjects or masters (by this rule!). Use this to establish a hierarchy for
     * compatibility rules, e.g. one shipping option depending upon another.
     *
     * @example Given:
     *          $incompatibilityRule = false;
     *          $masters = ['shippingOption1'];
     *          $subjects = ['shippingOption2', 'shippingOption3'] = true;
     *      When nothing is selected, only 'shippingOption1' will be visible/enabled. When selecting 'shippingOption1',
     * shipping options
     *      'shippingOption2' and 'shippingOption3' will become visible/enabled, too.
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

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id);

    /**
     * @param string[] $subjects
     *
     * @return void
     */
    public function setSubjects(array $subjects);

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage(string $errorMessage);

    /**
     * @param array $masters
     *
     * @return void
     */
    public function setMasters(array $masters);

    /**
     * @param bool $incompatibilityRule
     *
     * @return void
     */
    public function setIncompatibilityRule(bool $incompatibilityRule);

    /**
     * @param bool $hideSubjects
     *
     * @return void
     */
    public function setHideSubjects(bool $hideSubjects);
}
