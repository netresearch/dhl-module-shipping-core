define([
        "Magento_Ui/js/form/components/fieldset"
    ], function (Component) {
        return Component.extend({
            defaults: {
                steps: ['items', 'xb package data', 'package data'],
            },
            template: 'Dhl_ShippingCore/test',
            initialize: function () {
                this._super();
            }
        });
    }
);