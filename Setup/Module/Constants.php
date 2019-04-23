<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

/**
 * Class Constants
 *
 * @package Dhl\ShippingCore\Setup
 */
class Constants
{
    const CHECKOUT_CONNECTION_NAME = 'checkout';

    const SALES_CONNECTION_NAME = 'sales';

    const TABLE_DHLGW_LABEL_STATUS = 'dhlgw_label_status';

    const TABLE_DHLGW_RECIPIENT_STREET ='dhlgw_recipient_street';

    const COLUMN_DHLGW_LABEL_STATUS = 'dhlgw_label_status';

    const TABLE_QUOTE_SHIPPING_OPTION_SELECTION = 'dhlgw_quote_address_shipping_option_selection';

    const TABLE_ORDER_SHIPPING_OPTION_SELECTION = 'dhlgw_order_address_shipping_option_selection';
}
