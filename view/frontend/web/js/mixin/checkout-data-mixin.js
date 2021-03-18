define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'jquery/jquery-storageapi'
], function ($, storage) {
    'use strict';

    var cacheKey = 'checkout-data',

        /**
         * @param {Object} data
         */
        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        /**
         * @return {*}
         */
        initData = function () {
            return {
                'shippingInPostPointData': null,
                'shippingInPostModeData': null,
            };
        },

        /**
         * @return {*}
         */
        getData = function () {
            var data = storage.get(cacheKey)();

            if ($.isEmptyObject(data)) {
                data = $.initNamespaceStorage('mage-cache-storage').localStorage.get(cacheKey);

                if ($.isEmptyObject(data)) {
                    data = initData();
                    saveData(data);
                }
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
        };
        return $.extend(checkoutData, mixin);
    };
});
