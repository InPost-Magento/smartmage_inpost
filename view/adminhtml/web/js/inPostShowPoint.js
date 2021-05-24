requirejs([
    'jquery',
    'ko',
    'inPostSdk',
], function ($, ko) {
    'use strict';

    var inPostModal = {
        apiEndpointProduction: 'https://api-pl-points.easypack24.net/v1',
        apiEndpointTesting: 'https://sandbox-api-shipx-pl.easypack24.net/v1',

        getMode: function() {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "POST",
                    url: '/inpost/locker/getmode',
                    dataType: 'json',
                }).done(function(data) {
                    resolve(data.mode);
                });
            });
        },

        config: function(mode, pointsTypes) {
            var self = this;

            return new Promise(function(resolve, reject) {
                easyPack.init({
                    apiEndpoint: (mode === 'prod' ? self.apiEndpointProduction : self.apiEndpointTesting),
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

        Modal: function() {
            return new Promise(function(resolve, reject) {
                easyPack.modalMap(function(point, modal) {
                    var targetLocker = $('input[name="shipment_fieldset[target_locker]"]');
                    var btnShowPoint = $('[data-inpost-select-point]');

                    if(targetLocker.length) {
                        ko.dataFor(targetLocker.get(0)).value(point.name);
                        btnShowPoint.attr('data-inpost-select-point', point.name);
                    }

                    $('body').removeClass('overlay-modal-carrier');
                    modal.closeModal();
                    inPostModal.closeModal();


                }, {width: document.documentElement.clientWidth, height: document.documentElement.clientHeight});

                resolve('True');
            });
        },

        closeModal: function() {
            return new Promise(function(resolve, reject) {
                var modalMapInPost = $('#widget-modal');

                if(modalMapInPost.length) {
                    modalMapInPost.parent().remove();
                }
                resolve('true');
            });
        },

        closeModalEvent: function() {
            $(document).on('click', '#widget-modal .widget-modal__close', function(e) {
                e.preventDefault();
                $('body').removeClass('overlay-modal-carrier');
            });
        },

        showModal: function() {
            var self = this;

            $(document).on('click', '[data-inpost-select-point]', function(e) {
                e.preventDefault();
                var point = $(this).attr('data-inpost-select-point');
                var configType = $(this).data('inpost-point-type');

                self.getMode().then(function(mode) {
                    self.config(mode, (configType ? configType.split('-') : ['parcel_locker', 'pop'])).then(function() {
                        self.Modal().then(function() {
                            if(point.length > 0) {
                                easyPack.map.searchLockerPoint(point);
                            }
                            var modalMapInPost = $('#widget-modal');
                            $('body').addClass('overlay-modal-carrier');
                            modalMapInPost.parent().css('background', 'rgba(0,0,0, .6)');
                            modalMapInPost.parent().css('overflow-y', 'auto');
                            modalMapInPost.addClass('modalMapInPost');
                        });
                    });
                });
            });
        },

        init: function() {
            this.showModal();
            this.closeModalEvent();
        }
    };

    $(document).ready(function() {
        inPostModal.init();
    })
});