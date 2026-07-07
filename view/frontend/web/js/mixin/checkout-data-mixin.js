define([
    'jquery',
    'jquery/jquery-storageapi'
], function ($) {
    'use strict';

    var cacheKey = 'checkout-data',
        storage = $.initNamespaceStorage('mage-cache-storage').localStorage,

        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        initData = function () {
            return {
                shippingInPostPointData: null,
                shippingInPostModeData: null,
                shippingInPostContextPoints: {}
            };
        },

        getData = function () {
            var data = storage.get(cacheKey);

            if ($.isEmptyObject(data)) {
                data = initData();
                saveData(data);
            }

            if (!data.shippingInPostContextPoints) {
                data.shippingInPostContextPoints = {};
            }

            return data;
        };

    return function (checkoutData) {
        var mixin = {
            setShippingInPostPoint: function (data) {
                var obj = getData();

                obj.shippingInPostPointData = data;
                saveData(obj);
            },

            getShippingInPostPoint: function () {
                return getData().shippingInPostPointData;
            },

            setShippingInPostMode: function (data) {
                var obj = getData();

                obj.shippingInPostModeData = data;
                saveData(obj);
            },

            getShippingInPostMode: function () {
                return getData().shippingInPostModeData;
            },

            setShippingInPostContextPoint: function (contextKey, data) {
                var obj = getData();

                obj.shippingInPostContextPoints[contextKey] = data;
                saveData(obj);
            },

            getShippingInPostContextPoint: function (contextKey) {
                return getData().shippingInPostContextPoints[contextKey] || null;
            },

            clearShippingInPostContextPoint: function (contextKey) {
                var obj = getData();

                delete obj.shippingInPostContextPoints[contextKey];
                saveData(obj);
            }
        };

        return $.extend(checkoutData, mixin);
    };
});
