<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\ShippingOption;

/**
 * Carrier code, option code, and input code definitions for use in the shipping_settings.xml files.
 */
class Codes
{
    /**
     * The input type for the special shopfinder component.
     */
    const INPUT_TYPE_SHOPFINDER = 'shopfinder';

    /**
     * The carrier code for the template carrier
     */
    const CARRIER_BASE = 'base';

    const PACKAGING_OPTION_PACKAGE_DETAILS = 'packageDetails';
    const PACKAGING_INPUT_PRODUCT_CODE = 'productCode';
    const PACKAGING_INPUT_CUSTOM_PACKAGE_ID = 'customPackageId';
    const PACKAGING_INPUT_PACKAGING_WEIGHT = 'packagingWeight';
    const PACKAGING_INPUT_WEIGHT = 'weight';
    const PACKAGING_INPUT_WEIGHT_UNIT = 'weightUnit';
    const PACKAGING_INPUT_SIZE_UNIT = 'sizeUnit';
    const PACKAGING_INPUT_WIDTH = 'width';
    const PACKAGING_INPUT_HEIGHT = 'height';
    const PACKAGING_INPUT_LENGTH = 'length';

    const PACKAGING_OPTION_PACKAGE_CUSTOMS = 'packageCustoms';
    const PACKAGING_INPUT_CUSTOMS_VALUE = 'customsValue';
    const PACKAGING_INPUT_EXPORT_DESCRIPTION = 'exportDescription';
    const PACKAGING_INPUT_TERMS_OF_TRADE = 'termsOfTrade';
    const PACKAGING_INPUT_CONTENT_TYPE = 'contentType';
    const PACKAGING_INPUT_EXPLANATION = 'explanation';
    const PACKAGING_INPUT_DG_CATEGORY = 'dgCategory';

    const ITEM_OPTION_DETAILS = 'details';
    const ITEM_INPUT_PRODUCT_ID = 'productId';
    const ITEM_INPUT_PRODUCT_NAME = 'productName';
    const ITEM_INPUT_PRICE = 'price';
    const ITEM_INPUT_QTY = 'qty';
    const ITEM_INPUT_QTY_TO_SHIP = 'qtyToShip';
    const ITEM_INPUT_WEIGHT = 'weight';

    const ITEM_OPTION_ITEM_CUSTOMS = 'itemCustoms';
    const ITEM_INPUT_CUSTOMS_VALUE = 'customsValue';
    const ITEM_INPUT_HS_CODE = 'hsCode';
    const ITEM_INPUT_COUNTRY_OF_ORIGIN = 'countryOfOrigin';
    const ITEM_INPUT_EXPORT_DESCRIPTION = 'exportDescription';

    const SHOPFINDER_INPUT_COMPANY = 'company';
    const SHOPFINDER_INPUT_LOCATION_TYPE = 'locationType';
    const SHOPFINDER_INPUT_LOCATION_NUMBER = 'locationNumber';
    const SHOPFINDER_INPUT_LOCATION_ID = 'locationId';
    const SHOPFINDER_INPUT_STREET = 'street';
    const SHOPFINDER_INPUT_POSTAL_CODE = 'postalCode';
    const SHOPFINDER_INPUT_CITY = 'city';
    const SHOPFINDER_INPUT_COUNTRY_CODE = 'countryCode';
}
