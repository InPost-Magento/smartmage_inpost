define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/checkout-data',
        'inPostPaczkomaty'
    ], function (
        $,
        ko,
        quote,
        $t,
        shippingService,
        checkoutData,
    ) {
        'use strict';

        return function (target) {
            return target.extend({
                errorValidationMessage: ko.observable(false),
                inPostPoint: $('[data-shipping-inpost-selected-point]'),

                validateShippingInformation: function() {
                    const self = this;

                    if(quote.shippingMethod()) {
                        const ShippingMethodCode = quote.shippingMethod().method_code+'_'+quote.shippingMethod().carrier_code;

                        if (ShippingMethodCode === 'standard_inpostlocker'
                            || ShippingMethodCode === 'standardcod_inpostlocker'
                            || ShippingMethodCode === 'standardeow_inpostlocker'
                            || ShippingMethodCode === 'standardeowcod_inpostlocker'
                        ) {
                            const pointDataDB = checkoutData.getShippingInPostPoint();

                            if( typeof pointDataDB === 'undefined' || pointDataDB === null || pointDataDB.name.length === 0){
                                self.errorValidationMessage(
                                    $t('Please select a pickup point')
                                );
                                return false;

                            } else {
                                if(ShippingMethodCode === 'standardcod_inpostlocker'
                                    || ShippingMethodCode === 'standardeowcod_inpostlocker') {
                                    if(!pointDataDB.type.includes('parcel_locker')) {
                                        self.errorValidationMessage(
                                            $t('The selected point does not support the cash on delivery method')
                                        );
                                        return false;
                                    }
                                }
                            }
                        }
                    }

                    return this._super();
                },
            });
        }
    }
);
