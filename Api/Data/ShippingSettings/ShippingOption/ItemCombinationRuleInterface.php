<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface ItemCombinationRuleInterface
 *
 * A DTO for an item combination rule that governs how an input value
 * is related to an item's item level input values.
 *
 * @api
 */
interface ItemCombinationRuleInterface
{
    /**
     * The dot separated compound code of the item level inputs
     * that hold the source value for the combination rule.
     *
     * @return string
     */
    public function getSourceItemInputCode(): string;

    /**
     * Contains compound codes of additional package or service level inputs
     * that should also be considered when combining values
     *
     * @return string[]
     */
    public function getAdditionalSourceInputCodes(): array;

    /**
     * The action to perform on the source values. Valid values:
     *
     * - 'add': adds up each value multiplied by the item's quantity,
     * - 'concat': combines the values as comma-separated string.
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * @param string $action
     * @return void
     */
    public function setSourceItemInputCode(string $action);

    /**
     * @param string[] $additionalServiceInputCodes
     * @return mixed
     */
    public function setAdditionalSourceInputCodes(array $additionalServiceInputCodes);

    /**
     * @param string $action
     * @return void
     */
    public function setAction(string $action);
}
