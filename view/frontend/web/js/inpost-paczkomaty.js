define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate',
    'Magento_Checkout/js/checkout-data',
    'inPostSdk',
], function ($, Component, quote, shippingService, $t, checkoutData) {
    'use strict';

    return {
        inPostPointData: 'https://api-shipx-pl.easypack24.net/v1/points/',

        inPostMethod: function() {
            var method = [
                ['#label_method_standardcod_inpostlocker', 'parcel_locker'],
                ['#label_method_standard_inpostlocker', 'parcel_locker-pop'],
                ['#label_method_standardeow_inpostlocker', 'parcel_locker-pop'],
                ['#label_method_standardeowcod_inpostlocker', 'parcel_locker'],
            ];

            return method;
        },

        setPoint: function(dataToSend) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "POST",
                    url: '/inpost/locker/save',
                    data: {
                        'inpost_locker_id': dataToSend
                    },
                    dataType: 'json',
                }).done(function(data) {
                    if(data.status === 1) {
                        resolve('True');
                    } else {
                        reject('False');
                    }
                });
            });
        },

        getPoint: function() {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "POST",
                    url: '/inpost/locker/get',
                    dataType: 'json',
                }).done(function(data) {
                    resolve(data.inpost_locker_id);
                });
            });
        },

        getPointInformation: function(pointId) {
            var self = this;

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: self.inPostPointData + pointId,
                    type: 'GET',
                }).done(function(result) {
                    if(result.status !== 404) {
                        checkoutData.setShippingInPostPoint(result);
                        resolve(result);
                    }
                });
            });
        },

        wrapperPointHtml: function(inPostCarrier, pointType) {
            return new Promise(function(resolve, reject) {
                var html = '<div data-inpost-wrapper="'+ pointType +'" class="inpost-carrier-wrapper"></div>';

                if(inPostCarrier.find('[data-inpost-wrapper]').length === 0) {
                    inPostCarrier.append(html);

                    resolve('True');
                }
            });
        },

        cleanPointDataHtml: function() {
            return new Promise(function(resolve, reject) {
                var self = $('[data-inpost-wrapper]');

                if(self.length > 0) {
                    self.html('');
                }
                resolve('True');
            });
        },

        pointDataHtml: function(point, selectPoint, insert) {
            return new Promise(function(resolve, reject) {
                var self = $('[data-inpost-wrapper]');
                var html = '<div data-inpost-point-data class="point-data">';
                html += '<p>'+ $t('Selected pickup point:') +'</p>';
                html += '<p>'+ point.name +' | ' + point.address_details.city + ', '+ point.address.line1 +'</p>';
                html += '</div>';
                html += selectPoint;

                if(insert) {
                    self.prepend(html);
                    resolve(html);
                } else {
                    resolve(html);
                }
            });
        },

        selectPointHtml: function(point) {
            return '<a data-inpost-select-point href="" title="'+ (point ? $t('Change pickup point') : $t('Please select a pickup point')) +'">'+ (point ? $t('Change pickup point') : $t('Please select a pickup point')) +'</a>';
        },

        insertData: function(carrierMethod, pointData) {
            var self = this;
            return new Promise(function(resolve, reject) {

                var wrapperPoint = carrierMethod.find('[data-inpost-wrapper]');
                var html = '';
                if(wrapperPoint.length) {
                    if(pointData) {
                        self.pointDataHtml(pointData, self.selectPointHtml(true), false).then(function(htmlDataPoint) {
                            wrapperPoint.append(htmlDataPoint);

                            resolve('True');
                        });
                    } else {
                        html = self.selectPointHtml(false);
                        wrapperPoint.append(html);

                        resolve('True');
                    }
                }
            });
        },

        InPostConfig: function(pointsTypes) {
            return new Promise(function(resolve, reject) {
                easyPack.init({
                    apiEndpoint: 'https://api-pl-points.easypack24.net/v1',
                    defaultLocale: 'pl',
                    mapType: 'osm',
                    searchType: 'osm',
                    points: {
                        types: pointsTypes,
                    },
                    map: {
                        useGeolocation: true,
                        initialTypes: pointsTypes
                    }
                });

                resolve('True');
            });
        },

        inPostModalMap: function() {
            var self = this;

            return new Promise(function(resolve, reject) {
                easyPack.modalMap(function(point, modal) {
                    $('body').removeClass('overlay-modal-carrier');
                    modal.closeModal();

                    self.setPoint(point.name).then(function() {
                        self.cleanPointDataHtml().then(function() {
                            self.pointDataHtml(point, self.selectPointHtml(true), true).then(function() {
                                checkoutData.setShippingInPostPoint(point);
                            });
                        });
                    });

                }, {width: document.documentElement.clientWidth, height: document.documentElement.clientHeight});

                resolve('True');
            });
        },
        removeInPostModalMap: function() {
            return new Promise(function(resolve, reject) {
                var modalMapInPost = $('#widget-modal');

                if(modalMapInPost.length) {
                    modalMapInPost.parent().remove();
                }
                resolve('true');
            });
        },

        hideInPostModalMap: function() {
            $(document).on('click', '#widget-modal .widget-modal__close', function(e) {
                $('body').removeClass('overlay-modal-carrier');
            });
        },

        selectInPostPoint: function() {
            var self = this;

            $(document).on('click', '[data-inpost-select-point]', function(e) {
                e.preventDefault();

                var getPointType = $(this).parent().data('inpost-wrapper');
                var configPointType = getPointType.split('-');

                self.removeInPostModalMap().then(function() {
                    self.InPostConfig(configPointType).then(function() {
                        self.inPostModalMap().then(function() {
                            var modalMapInPost = $('#widget-modal');
                            $('body').addClass('overlay-modal-carrier');
                            modalMapInPost.parent().css('background', 'rgba(0,0,0, .6)');
                            modalMapInPost.parent().css('overflow-y', 'auto');
                            modalMapInPost.addClass('modalMapInPost');

                            self.getPoint().then(function(selectedPoint) {
                                if(selectedPoint) {
                                    easyPack.map.searchLockerPoint(selectedPoint);
                                } else {
                                    var postcode = ($('[name="postcode"]').val()) ? $('[name="postcode"]').val() : '';
                                    var city = ($('[name="city"]').val()) ? $('[name="city"]').val() : '';

                                    if(postcode || city) {
                                        var searchBy = postcode + ' ' + city;
                                        easyPack.map.searchPlace(searchBy);
                                    }
                                }
                            });
                        });
                    });
                });
            });
        },

        renderInPostData: function() {
            var self = this;

            self.getPoint().then(function(pointId) {
                if(pointId) {
                    self.getPointInformation(pointId).then(function(pointData) {
                        $.each(self.inPostMethod(), function(index, value) {
                            if($(value[0]).length) {
                                self.wrapperPointHtml($(value[0]), value[1]).then(function() {
                                    self.insertData($(value[0]), pointData).then(function() {});
                                });
                            }
                        });
                    });
                } else {
                    $.each(self.inPostMethod(), function(index, value) {
                        if($(value[0]).length) {
                            self.wrapperPointHtml($(value[0]), value[1]).then(function() {
                                self.insertData($(value[0]), '').then(function() {});
                            });
                        }
                    });
                }
            });
        },

        init: function() {
            var self = this;

            shippingService.isLoading.subscribe(function (isLoading) {
                if (!isLoading) {
                    self.renderInPostData();
                }
            });
            self.renderInPostData();
        }
    }
});
