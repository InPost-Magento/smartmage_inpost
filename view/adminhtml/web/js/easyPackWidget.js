define([
    'jquery',
    'inPostSdk',
    'configEasyPack'
], function ($) {

    var initializeDW = {
        getPointData: 'https://api-shipx-pl.easypack24.net/v1/points/',
        hideInputDefaultSendingPoint: function(fieldWrapper) {
            return new Promise(function(resolve, reject) {
                fieldWrapper.find('input').css('display', 'none');

                resolve('True');
            });
        },

        createWrapperEasyPack: function(fieldWrapper, wrapperEasyPackWidget) {
            return new Promise(function(resolve, reject) {
                fieldWrapper.prepend('<div id="'+ wrapperEasyPackWidget +'"></div>');

                resolve('True');
            });
        },

        getPointAddress: function(point) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: initializeDW.getPointData + point,
                    type: 'GET',
                }).done(function(result) {
                    if(result.status !== 404) {
                        resolve(result.address.line1 + ', ' + result.address.line2 + ', ' + result.name);
                    }
                });
            });
        },

        initializeDropdownWidget: function(wrapperEasyPackWidget, defaultSendingPointValue) {
            return new Promise(function(resolve, reject) {
                easyPack.dropdownWidget(wrapperEasyPackWidget, function(point) {
                    defaultSendingPointValue.val(point.name);
                });

                resolve('True');
            });
        },

        init: function(fieldWrapper) {
            var self = this;
            var wrapperDefaultSendingPoint = $('#row_carriers_inpost_inpostlocker_'+ fieldWrapper +'_default_sending_point td.value');
            var wrapperEasyPackWidget = 'inpost_carrier_'+ fieldWrapper +'_default_sending_point';
            var defaultSendingPointValue = $('#carriers_inpost_inpostlocker_'+ fieldWrapper +'_default_sending_point');

            self.hideInputDefaultSendingPoint(wrapperDefaultSendingPoint).then(function() {
                self.createWrapperEasyPack(wrapperDefaultSendingPoint, wrapperEasyPackWidget).then(function() {
                    self.initializeDropdownWidget(wrapperEasyPackWidget, defaultSendingPointValue).then(function() {
                        if(defaultSendingPointValue.val().length) {
                            self.getPointAddress(defaultSendingPointValue.val()).then(function(result) {
                                $('#'+ wrapperEasyPackWidget).find('.easypack-dropdown__select span').first().html(result);
                            });
                        }
                    });
                });
            });
        }
    };

    var jsInPostDropdownWidget = function(config) {
        initializeDW.init(config.wrapper);
    };

    return jsInPostDropdownWidget;
});