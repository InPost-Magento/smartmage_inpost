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
        inPostPaczkomaty
    ) {
        'use strict';

        return function (target) {
            return target.extend({
                errorValidationMessage: ko.observable(false),
                inPostPoint: $('[data-shipping-inpost-selected-point]'),

                initialize: function() {
                    this._super();

                    inPostPaczkomaty.selectInPostPoint();
                    inPostPaczkomaty.hideInPostModalMap();
                },

                validateShippingInformation: function() {

                    this._super();
                    var self = this;

                    if(quote.shippingMethod()) {
                        if (quote.shippingMethod().method_code === 'standard' || quote.shippingMethod().method_code === 'standardcod' || quote.shippingMethod().method_code === 'standardeow' || quote.shippingMethod().method_code === 'standardeowcod' ) {
                            var pointDataDB = checkoutData.getShippingInPostPoint();

                            if( typeof pointDataDB === 'undefined' || pointDataDB === null ){
                                self.errorValidationMessage(
                                    $t('Please select a pickup point')
                                );
                                return false;

                            } else {
                                if(pointDataDB.name.length === 0) {
                                    self.errorValidationMessage(
                                        $t('Please select a pickup point')
                                    );
                                    return false;

                                } else {
                                    if(quote.shippingMethod().method_code === 'standardcod' || quote.shippingMethod().method_code === 'standardeowcod') {
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

                        return true;
                    }
                },
            });
        }
    }
);
