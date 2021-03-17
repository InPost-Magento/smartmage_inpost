define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-service',
        'inPostPaczkomaty'
    ], function (
        $,
        ko,
        quote,
        $t,
        shippingService,
        inPostPaczkomaty
    ) {
        'use strict';

        return function (target) {
            return target.extend({
                errorValidationMessage: ko.observable(false),
                inPostPoint: $('[data-shipping-inpost-selected-point]'),

                initialize: function() {

                    this._super();

                    shippingService.isLoading.subscribe(function (isLoading) {
                        if (!isLoading) {
                            inPostPaczkomaty.init();
                        }
                    });

                    inPostPaczkomaty.selectInPostPoint();
                    inPostPaczkomaty.hideInPostModalMap();
                },

                validateShippingInformation: function() {
                    var self = this;

                    if (quote.shippingMethod().method_code === 'standard' || quote.shippingMethod().method_code === 'standardcod' || quote.shippingMethod().method_code === 'standardeow' || quote.shippingMethod().method_code === 'standardeowcod' ) {
                        inPostPaczkomaty.getPoint().then(function(pointId) {
                            if(pointId.length === 0) {
                                self.errorValidationMessage(
                                    $t('Please select a pickup point')
                                );
                                return false;

                            } else {
                                if(quote.shippingMethod().method_code === 'standardcod' || quote.shippingMethod().method_code === 'standardeowcod') {
                                    inPostPaczkomaty.getPointInformation(pointId).then(function(pointData) {
                                        if(!pointData.type.includes('parcel_locker')) {
                                            self.errorValidationMessage(
                                                $t('The selected point does not support the cash on delivery method')
                                            );
                                            return false;
                                        }
                                    });
                                }
                            }
                        });
                    }
                    this._super();
                },
            });
        }
    }
);
