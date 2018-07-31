define([
    'Magento_Ui/js/modal/modal-component'
], function (Modal) {
    'use strict';

    return Modal.extend({
        initialize: function () {
            window.packaging = this;
            this._super();
            console.log(this);
        },

        showWindow: function () {
            debugger;
            this.initModal();
            this.openModal();
        }
    })
});
