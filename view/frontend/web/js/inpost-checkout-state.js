define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function (quote, checkoutData) {
    'use strict';

    var defaultProviderId = 'inpost-checkout';
    var legacyProviderIds = ['smartmage-poland'];
    var pickupMethodConfigs = {
        inpostlocker_standardcod: 'parcelCollectPayment',
        inpostlocker_standard: 'parcelCollect',
        inpostlocker_economic: 'parcelCollect',
        inpostlocker_economiccod: 'parcelCollectPayment',
        inpostlocker_standardeow: 'parcelCollect247',
        inpostlocker_standardeowcod: 'parcelCollect247'
    };

    function buildMethodValue(carrierCode, methodCode) {
        return carrierCode + '_' + methodCode;
    }

    function isPickupMethod(carrierCode, methodCode) {
        return Object.prototype.hasOwnProperty.call(pickupMethodConfigs, buildMethodValue(carrierCode, methodCode));
    }

    function requiresParcelLocker(methodCode) {
        return methodCode === 'standardcod' ||
            methodCode === 'standardeowcod' ||
            methodCode === 'economiccod';
    }

    function getShippingCountry() {
        var shippingAddress = quote.shippingAddress();

        return shippingAddress && shippingAddress.countryId ? shippingAddress.countryId : 'PL';
    }

    function createContext(providerId, carrierCode, methodCode) {
        return {
            providerId: providerId,
            carrierCode: carrierCode,
            methodCode: methodCode,
            methodValue: buildMethodValue(carrierCode, methodCode),
            countryId: getShippingCountry()
        };
    }

    function getCurrentContext(providerId) {
        var method = quote.shippingMethod();

        if (!method || !isPickupMethod(method.carrier_code, method.method_code)) {
            return null;
        }

        return createContext(providerId, method.carrier_code, method.method_code);
    }

    function getCompatibleProviderIds(providerId) {
        var providerIds = [providerId || defaultProviderId];

        legacyProviderIds.forEach(function (legacyProviderId) {
            if (providerIds.indexOf(legacyProviderId) === -1) {
                providerIds.push(legacyProviderId);
            }
        });

        return providerIds;
    }

    function isContextSupported(context) {
        return !!context &&
            context.countryId === 'PL' &&
            Object.prototype.hasOwnProperty.call(pickupMethodConfigs, context.methodValue);
    }

    function getContextKey(providerId, context) {
        if (!providerId || !context) {
            return null;
        }

        return [
            providerId,
            context.carrierCode || '',
            context.methodCode || '',
            context.countryId || ''
        ].join('::');
    }

    function isPayloadCompatible(providerId, payload, context) {
        var pointContext;

        if (!payload || !context || context.countryId !== 'PL') {
            return false;
        }

        pointContext = payload._inpostContext;

        if (!pointContext) {
            return false;
        }

        return getCompatibleProviderIds(providerId).indexOf(pointContext.providerId) !== -1 &&
            pointContext.carrierCode === context.carrierCode &&
            pointContext.methodCode === context.methodCode &&
            pointContext.countryId === context.countryId;
    }

    function getContextPoint(providerId, context) {
        var payload = null;

        getCompatibleProviderIds(providerId).some(function (candidateProviderId) {
            var contextKey = getContextKey(candidateProviderId, context);
            var candidatePayload = contextKey ? checkoutData.getShippingInPostContextPoint(contextKey) : null;

            if (isPayloadCompatible(providerId, candidatePayload, context)) {
                payload = candidatePayload;
                return true;
            }

            return false;
        });

        if (payload) {
            return payload;
        }

        return null;
    }

    function getLegacyPoint(providerId, context) {
        var currentMethod = quote.shippingMethod();
        var payload = checkoutData.getShippingInPostPoint();

        if (!context || context.countryId !== 'PL' || !payload || !payload.name) {
            return null;
        }

        if (payload._inpostContext && !isPayloadCompatible(providerId, payload, context)) {
            return null;
        }

        if (!payload._inpostContext && (
            !currentMethod ||
            currentMethod.carrier_code !== context.carrierCode ||
            currentMethod.method_code !== context.methodCode
        )) {
            return null;
        }

        return payload;
    }

    function getCurrentValidationPoint(providerId) {
        var context = getCurrentContext(providerId);

        if (!isContextSupported(context)) {
            return null;
        }

        return getContextPoint(providerId, context) || getLegacyPoint(providerId, context);
    }

    return {
        getDefaultProviderId: function () {
            return defaultProviderId;
        },
        isPickupMethod: isPickupMethod,
        requiresParcelLocker: requiresParcelLocker,
        getShippingCountry: getShippingCountry,
        createContext: createContext,
        getCurrentContext: getCurrentContext,
        isContextSupported: isContextSupported,
        getContextKey: getContextKey,
        isPayloadCompatible: isPayloadCompatible,
        getContextPoint: getContextPoint,
        getLegacyPoint: getLegacyPoint,
        getCurrentValidationPoint: getCurrentValidationPoint
    };
});
