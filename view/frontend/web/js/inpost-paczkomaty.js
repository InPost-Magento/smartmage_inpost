define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
    'inPostSdk',
], function ($, Component, quote, shippingService, $t, checkoutData, fullScreenLoader) {
    'use strict';

    return {
        apiEndpointProduction: 'https://api-pl-points.easypack24.net/v1',
        apiEndpointTesting: 'https://sandbox-api-shipx-pl.easypack24.net/v1',
        apiToken: window.checkoutConfig.geowidget_token,

        inPostAllMethod: function(type) {
            const method = [
                ['#label_'+ type +'_standardcod_inpostlocker'],
                ['#label_'+ type +'_standard_inpostlocker'],
                ['#label_'+ type +'_standardeow_inpostlocker'],
                ['#label_'+ type +'_standardeowcod_inpostlocker'],
                ['#label_'+ type +'_economic_inpostlocker'],
                ['#label_'+ type +'_economiccod_inpostlocker'],
                ['#label_'+ type +'_c2c_inpostcourier'],
                ['#label_'+ type +'_c2ccod_inpostcourier'],
                ['#label_'+ type +'_standard_inpostcourier'],
                ['#label_'+ type +'_standardcod_inpostcourier'],
                ['#label_'+ type +'_express1000_inpostcourier'],
                ['#label_'+ type +'_express1200_inpostcourier'],
                ['#label_'+ type +'_express1700_inpostcourier'],
                ['#label_'+ type +'_palette_inpostcourier'],
                ['#label_'+ type +'_alcohol_inpostcourier']
            ];

            return method;
        },

        inPostMethod: function() {
            const method = [
                ['#label_method_standardcod_inpostlocker', 'parcelCollectPayment'],
                ['#label_method_standard_inpostlocker', 'parcelCollect'],
                ['#label_method_economic_inpostlocker', 'parcelCollect'],
                ['#label_method_economiccod_inpostlocker', 'parcelCollectPayment'],
                ['#label_method_standardeow_inpostlocker', 'parcelCollect247'],
                ['#label_method_standardeowcod_inpostlocker', 'parcelCollect247']
            ];

            return method;
        },

        getMode: function() {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "POST",
                    url: window.checkoutConfig.base_url + 'inpost/locker/getmode',
                    dataType: 'json',
                }).done(function(data) {
                    checkoutData.setShippingInPostMode(data.mode);
                    resolve(data.mode);
                });
            });
        },

        setPoint: function(dataToSend) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "POST",
                    url: window.checkoutConfig.base_url + 'inpost/locker/save',
                    data: {
                        'inpost_locker_id': dataToSend
                    },
                    dataType: 'json',
                }).done(function(data) {
                    if (data.status === 1) {
                        resolve('True');
                    } else {
                        reject('False');
                    }
                });
            });
        },

        getPoint: function() {
            return new Promise(function(resolve, reject) {
                resolve(window.checkoutConfig.quoteData.inpost_locker_id);
            });
        },

        getPointInformation: function(pointId) {
            const self = this;

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: (checkoutData.getShippingInPostMode() === 'prod' ? self.apiEndpointProduction : self.apiEndpointTesting) + '/points/' + pointId,
                    type: 'GET',
                }).done(function(result) {
                    if (result.status !== 404) {
                        checkoutData.setShippingInPostPoint(result);
                        resolve(result);
                    } else {
                        reject(result);
                    }
                });
            });
        },

        wrapperPointHtml: function(inPostCarrier, pointType) {
            const html = '<div data-inpost-wrapper="'+ pointType +'" class="inpost-carrier-wrapper"></div>';
            inPostCarrier.find('div').remove();
            if (inPostCarrier.find('[data-inpost-wrapper]').length === 0) {
                inPostCarrier.append(html);
            }
        },

        cleanPointDataHtml: function() {
            return new Promise(function(resolve, reject) {
                const self = $('[data-inpost-wrapper]');

                if (self.length > 0) {
                    self.html('');
                }
                resolve(true);
            });
        },

        pointDataHtml: function(point, selectPoint, insert) {
            return new Promise(function(resolve, reject) {
                const self = $('[data-inpost-wrapper]');
                let html = '<div data-inpost-point-data class="point-data">';
                html += '<p>'+ $t('Selected pickup point:') +'</p>';
                html += '<p>'+ point.name +' | ' + point.address_details.city + ', '+ point.address.line1 +'</p>';
                html += '</div>';
                html += selectPoint;

                if (insert) {
                    self.prepend(html);
                    resolve(html);
                } else {
                    resolve(html);
                }
            });
        },

        selectPointHtml: function(point) {
            return '<button data-inpost-select-point class="action secondary small" title="'+
                (point ? $t('Change pickup point') : $t('Please select a pickup point')) +
                '">'+ (point ? $t('Change pickup point') : $t('Please select a pickup point')) +'</button>';
        },

        insertData: function(carrierMethod, pointData) {
            const self = this;
            const wrapperPoint = carrierMethod.find('[data-inpost-wrapper]');
            let html = '';

            if (wrapperPoint.length) {
                if (pointData) {
                    self.pointDataHtml(pointData, self.selectPointHtml(true), false).then(function(htmlDataPoint) {
                        wrapperPoint.append(htmlDataPoint);
                    });
                } else {
                    html = self.selectPointHtml(false);
                    wrapperPoint.append(html);
                }
            }
        },

        insertLogoInPost: function(timeout) {
            const self = this;
            let timeoutIncrement = timeout ? timeout : 1;

            return new Promise(function(resolve, reject) {
                let found = false;
                $.each(self.inPostAllMethod('method'), function(index, value) {
                    const codeMethod = value[0].split('_');
                    const logoWrapper = $(value[0]).find('[data-inpost-logo-'+codeMethod[2]+'-'+codeMethod[3]+']');

                    if ($(value[0]).length) {
                        found = true;
                        if (logoWrapper.length > 0) {
                            logoWrapper.remove();
                        }
                        $(value[0]).prepend(
                            '<div data-inpost-logo-'+codeMethod[2]+'-'+codeMethod[3]+'><img src="'
                                + window.checkoutConfig[codeMethod[3]+'_'+codeMethod[2]+'_'+codeMethod[3]]
                            + '" alt="" title=""/></div>'
                        );
                    }
                });

                $.each(self.inPostAllMethod('carrier'), function(index, value) {
                    if ($(value[0]).length) {
                        $(value[0]).html('');
                    }
                });

                if(!found && timeoutIncrement < 10) {
                    // in case of delay in loading the shipping method, call self.insertLogoInPost() with delay 2s
                    setTimeout(function() {
                        self.insertLogoInPost(timeoutIncrement + 1);
                    }, 2000);
                }

                resolve(true);
            });
        },

        createModal: function(pointType) {
            let html = '<div data-inpost-modal class="inpost-modal">';
                html += '<div class="inpost-modal__container">';
                html += '<div data-inpost-modal-btn-close class="btn-close"></div>';
                html += '<inpost-geowidget onpoint="onpointselect" token="'+ this.apiToken +'" language="pl" country="PL" config="'+ pointType +'"></inpost-geowidget>';
                html += '</div>';
                html += '</div>';

            $('body').append(html);
        },

        renderInPostDataHtml: function(pointData, timeout) {
            const self = this;
            let timeoutIncrement = timeout ? timeout : 1;

            return new Promise(function(resolve) {
                let found = false;
                $.each(self.inPostMethod(), function(index, value) {
                    if ($(value[0]).length) {
                        found = true;
                        self.wrapperPointHtml($(value[0]), value[1]);
                        self.insertData($(value[0]), (pointData ? pointData : ''));
                    }
                });
                if (!found && timeoutIncrement < 10) {
                    // in case of delay in loading the shipping method, call self.renderInPostDataHtml() with delay 2s
                    setTimeout(function() {
                        self.renderInPostDataHtml(pointData, timeoutIncrement + 1);
                    }, 2000);
                }
                resolve(true);
            });
        },

        renderInPostData: function() {
            const self = this;

            return new Promise(function(resolve) {
                fullScreenLoader.startLoader();
                self.getPoint().then(function(pointId) {
                    if (pointId) {
                        self.getPointInformation(pointId).then(
                            function(pointData) {
                                self.renderInPostDataHtml(pointData).then(function() {
                                    resolve(true);
                                });
                            },
                            function(pointData) {
                                self.renderInPostDataHtml(false).then(function() {
                                    resolve(true);
                                });
                            }
                        );
                    } else {
                        self.renderInPostDataHtml().then(function() {
                            resolve(true);
                        });
                    }
                });
            });
        },


        init: function() {
            const self = this;
            shippingService.isLoading.subscribe(function (isLoading) {
                if (!isLoading) {
                    self.renderInPostData().then(function() {
                        self.insertLogoInPost().then(function() {
                            fullScreenLoader.stopLoader();
                        });
                    });
                }
            });

            self.getMode().then(function() {
                self.renderInPostData().then(function () {
                    self.insertLogoInPost().then(function () {
                        fullScreenLoader.stopLoader();
                    });
                });
            });
        }
    }
});
