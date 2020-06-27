<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

/**
 * @api
 */
interface OrderItemAttributesInterface
{
    const ITEM_ID = 'item_id';
    const DG_CATEGORY = 'dangerous_goods_category';
    const TARIFF_NUMBER = 'tariff_number';
    const EXPORT_DESCRIPTION = 'export_description';
    const COUNTRY_OF_MANUFACTURE = 'country_of_manufacture';

    /**
     * @return int
     */
    public function getItemId(): int;

    /**
     * @return string
     */
    public function getDgCategory(): string;

    /**
     * @return string
     */
    public function getTariffNumber(): string;

    /**
     * @return string
     */
    public function getExportDescription(): string;

    /**
     * @return string
     */
    public function getCountryOfManufacture(): string;

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId);

    /**
     * @param string|null $dgCategory
     */
    public function setDgCategory(string $dgCategory = null);

    /**
     * @param string|null $tariffNumber
     */
    public function setTariffNumber(string $tariffNumber = null);

    /**
     * @param string|null $exportDescription
     */
    public function setExportDescription(string $exportDescription = null);

    /**
     * @param string|null $countryOfManufacture
     */
    public function setCountryOfManufacture(string $countryOfManufacture = null);
}
