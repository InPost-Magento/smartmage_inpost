define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Magento_Checkout/js/checkout-data'
], function ($, ko, quote, $t, checkoutData) {
    'use strict';

    const INPOST_METHODS = [
        'standard_inpostlocker',
        'standardcod_inpostlocker',
        'standardeow_inpostlocker',
        'standardeowcod_inpostlocker',
        'economic_inpostlocker',
        'economiccod_inpostlocker'
    ];

    const COD_METHODS = [
        'standardcod_inpostlocker',
        'standardeowcod_inpostlocker',
        'economiccod_inpostlocker'
    ];

    return function (target) {
        return target.extend({
            errorValidationMessage: ko.observable(false),

            validateShippingInformation: function() {
                const shippingMethod = quote.shippingMethod();

                if (!shippingMethod) {
                    return this._super();
                }

                const methodCode = `${shippingMethod.method_code}_${shippingMethod.carrier_code}`;

                if (!INPOST_METHODS.includes(methodCode)) {
                    return this._super();
                }

                const pointData = checkoutData.getShippingInPostPoint();

                if (!pointData?.name?.length) {
                    this.errorValidationMessage($t('Please select a pickup point'));
                    return false;
                }

                if (COD_METHODS.includes(methodCode) && !pointData.type?.includes('parcel_locker')) {
                    this.errorValidationMessage($t('The selected point does not support the cash on delivery method'));
                    return false;
                }

                return this._super();
            }
        });
    };
});
