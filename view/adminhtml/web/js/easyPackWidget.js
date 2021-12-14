define([
    'jquery',
    'inPostSdk',
], function ($) {
    var initializeDW = {
        status: null,
        queue: [],
        getPointData: 'https://api-shipx-pl.easypack24.net/v1/points/',
        hideInputDefaultSendingPoint: function(fieldWrapper) {
            return new Promise(function(resolve, reject) {
                fieldWrapper.find('input').addClass('inpost-field-hide');

                resolve('True');
            });
        },

        createWrapperEasyPack: function(fieldWrapper, wrapperEasyPackWidget, pointsTypes, pointFunctions) {
            return new Promise(function(resolve, reject) {
                fieldWrapper.prepend('<div id="'+ wrapperEasyPackWidget +'" data-inpost-carrier-default-points-types="'+ pointsTypes +'" data-inpost-carrier-default-points-functions="'+ pointFunctions +'"></div>');

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

        configEasyPack: function(pointsTypes, pointFunctions) {
            return new Promise(function(resolve, reject) {
                easyPack.init({
                    instance: 'pl',
                    mapType: 'osm',
                    searchType: 'osm',
                    points: {
                        types: pointsTypes,
                        functions: pointFunctions 
                    },
                    map: {
                        useGeolocation: true,
                        initialTypes: pointsTypes
                    }
                });

                resolve('True');
            });

        },

        initializeDropdownWidget: function(wrapperEasyPackWidget, defaultSendingPointValue, pointsTypes) {
            return new Promise(function(resolve, reject) {

                easyPack.dropdownWidget(wrapperEasyPackWidget, function(point) {
                    defaultSendingPointValue.val(point.name);
                });

                resolve('True');
            });
        },

        reInitializeDropdownWidget: function() {
            var self = $('[data-inpost-carrier-default-points-types]');

            if(self.length === 1) {
                $(document).on('click', '[data-inpost-carrier-default-points-types]', function() {
                    var pointsTypes = $(this).data('inpost-carrier-default-points-types');
                    var createArrayPointTypes = pointsTypes.split(',');
                    var pointFunctions = $(this).data('inpost-carrier-default-points-functions');
                    var createArrayPointFunctions = pointsTypes.split(',');

                    initializeDW.configEasyPack(createArrayPointTypes, createArrayPointFunctions).then(function() {  });
                });
            }
        },

        init: function(fieldWrapper, pointsTypes, pointFunctions) {

            var self = this;
            var wrapperDefaultSendingPoint = $('#row_carriers_'+ fieldWrapper +'_default_sending_point td.value');
            var wrapperEasyPackWidget = 'inpost_carrier_'+ fieldWrapper +'_default_sending_point';
            var defaultSendingPointValue = $('#carriers_'+ fieldWrapper +'_default_sending_point');

            return new Promise(function(resolve, reject) {
                self.hideInputDefaultSendingPoint(wrapperDefaultSendingPoint).then(function() {
                    self.createWrapperEasyPack(wrapperDefaultSendingPoint, wrapperEasyPackWidget, pointsTypes, pointFunctions).then(function() {
                        self.configEasyPack(pointsTypes, pointFunctions).then(function() {
                            self.initializeDropdownWidget(wrapperEasyPackWidget, defaultSendingPointValue, pointsTypes).then(function() {

                                if(defaultSendingPointValue.val().length) {
                                    self.getPointAddress(defaultSendingPointValue.val()).then(function(result) {
                                        $('#'+ wrapperEasyPackWidget).find('.easypack-dropdown__select span').first().html(result);
                                    });
                                }

                                self.reInitializeDropdownWidget();
                                resolve('True');
                            });
                        });
                    });
                });
            });
        },

        inProgress: function() {
            if(this.queue.length > 0) {
                if(this.status == null) {
                    this.status = 'process';

                    var shifted = initializeDW.queue.shift();

                    initializeDW.init(shifted[0], shifted[1]).then(function() {
                        initializeDW.status = null;
                        initializeDW.inProgress();
                    });
                }
            }
        },

        enqueue: function(wrapper, pointsTypes) {
            this.queue.push([wrapper, pointsTypes]);
            this.inProgress();
        }
    };

    var jsInPostDropdownWidget = function(config) {
        initializeDW.enqueue(config.wrapper, config.points);
    };

    return jsInPostDropdownWidget;
});
