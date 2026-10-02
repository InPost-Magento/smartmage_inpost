define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Smartmage_Inpost/js/inpost-checkout-state'
], function (
    ko,
    quote,
    $t,
    inpostCheckoutState
) {
    'use strict';

    var providerId = inpostCheckoutState.getDefaultProviderId();

    return function (target) {
        return target.extend({
            errorValidationMessage: ko.observable(false),

            validateShippingInformation: function () {
                var shippingMethod = quote.shippingMethod();
                var pointData;

                if (shippingMethod) {
                    pointData = inpostCheckoutState.getCurrentValidationPoint(providerId);

                    if (inpostCheckoutState.isPickupMethod(shippingMethod.carrier_code, shippingMethod.method_code)) {
                        if (!pointData || !pointData.name) {
                            this.errorValidationMessage($t('Please select a pickup point'));
                            return false;
                        }

                        if (inpostCheckoutState.requiresParcelLocker(shippingMethod.method_code) &&
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
