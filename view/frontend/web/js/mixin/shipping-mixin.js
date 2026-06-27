define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'inPostPaczkomaty'
], function (
    $,
    ko,
    quote,
    $t,
    inPostPaczkomaty
) {
    'use strict';

    return function (target) {
        return target.extend({
            errorValidationMessage: ko.observable(false),

            validateShippingInformation: function () {
                var shippingMethod = quote.shippingMethod();
                var pointData;

                if (shippingMethod) {
                    pointData = inPostPaczkomaty.getCurrentValidationPoint();

                    if (inPostPaczkomaty.isPickupMethod(shippingMethod.carrier_code, shippingMethod.method_code)) {
                        if (!pointData || !pointData.name) {
                            this.errorValidationMessage($t('Please select a pickup point'));
                            return false;
                        }

                        if (inPostPaczkomaty.requiresParcelLocker(shippingMethod.method_code) &&
                            (!pointData.type || pointData.type.indexOf('parcel_locker') === -1)
                        ) {
                            this.errorValidationMessage(
                                $t('The selected point does not support the cash on delivery method')
                            );
                            return false;
                        }
                    }
                }

                return this._super();
            }
        });
    };
});
