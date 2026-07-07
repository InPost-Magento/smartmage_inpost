define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'mage/translate',
    'Magento_Checkout/js/checkout-data',
    'Smartmage_Inpost/js/inpost-geowidget-coordinator',
    'inPostSdk'
], function ($, quote, shippingService, $t, checkoutData, coordinator) {
    'use strict';

    var pickupMethodConfigs = {
        inpostlocker_standardcod: 'parcelCollectPayment',
        inpostlocker_standard: 'parcelCollect',
        inpostlocker_economic: 'parcelCollect',
        inpostlocker_economiccod: 'parcelCollectPayment',
        inpostlocker_standardeow: 'parcelCollect247',
        inpostlocker_standardeowcod: 'parcelCollect247'
    };

    var allInpostMethods = [
        'inpostlocker_standardcod',
        'inpostlocker_standard',
        'inpostlocker_standardeow',
        'inpostlocker_standardeowcod',
        'inpostlocker_economic',
        'inpostlocker_economiccod',
        'inpostcourier_c2c',
        'inpostcourier_c2ccod',
        'inpostcourier_standard',
        'inpostcourier_standardcod',
        'inpostcourier_express1000',
        'inpostcourier_express1200',
        'inpostcourier_express1700',
        'inpostcourier_palette',
        'inpostcourier_alcohol'
    ];

    function clone(value) {
        return value ? JSON.parse(JSON.stringify(value)) : null;
    }

    function buildMethodValue(carrierCode, methodCode) {
        return carrierCode + '_' + methodCode;
    }

    return {
        apiEndpointProduction: 'https://api-pl-points.easypack24.net/v1',
        apiEndpointTesting: 'https://sandbox-api-shipx-pl.easypack24.net/v1',
        apiToken: window.checkoutConfig.geowidget_token,
        providerId: 'smartmage-poland',
        initialized: false,
        listenersBound: false,
        shippingSubscriptionBound: false,
        providerRegistered: false,
        pointRequestCache: {},

        isPickupMethod: function (carrierCode, methodCode) {
            return Object.prototype.hasOwnProperty.call(pickupMethodConfigs, buildMethodValue(carrierCode, methodCode));
        },

        requiresParcelLocker: function (methodCode) {
            return methodCode === 'standardcod' ||
                methodCode === 'standardeowcod' ||
                methodCode === 'economiccod';
        },

        getMode: function () {
            return window.checkoutConfig.inpost_mode === 'test' ? 'test' : 'prod';
        },

        getSdkUrl: function () {
            return this.getMode() === 'test'
                ? 'https://sandbox-easy-geowidget-sdk.easypack24.net/inpost-geowidget.js'
                : 'https://geowidget.inpost.pl/inpost-geowidget.js';
        },

        getCssUrl: function () {
            return 'https://geowidget.inpost.pl/inpost-geowidget.css';
        },

        getShippingCountry: function () {
            var shippingAddress = quote.shippingAddress();

            return shippingAddress && shippingAddress.countryId ? shippingAddress.countryId : 'PL';
        },

        createContext: function (carrierCode, methodCode) {
            return {
                providerId: this.providerId,
                carrierCode: carrierCode,
                methodCode: methodCode,
                methodValue: buildMethodValue(carrierCode, methodCode),
                countryId: this.getShippingCountry()
            };
        },

        getCurrentContext: function () {
            var method = quote.shippingMethod();

            if (!method || !this.isPickupMethod(method.carrier_code, method.method_code)) {
                return null;
            }

            return this.createContext(method.carrier_code, method.method_code);
        },

        isContextSupported: function (context) {
            return !!context &&
                context.countryId === 'PL' &&
                Object.prototype.hasOwnProperty.call(pickupMethodConfigs, context.methodValue);
        },

        getContextKey: function (context) {
            return coordinator.getContextKey(this.providerId, context);
        },

        getStoragePayload: function (context, point) {
            var payload = clone(point) || {};

            payload._inpostContext = {
                providerId: this.providerId,
                carrierCode: context.carrierCode,
                methodCode: context.methodCode,
                countryId: context.countryId
            };

            return payload;
        },

        isPayloadCompatible: function (payload, context) {
            var pointContext;

            if (!payload || !context || context.countryId !== 'PL') {
                return false;
            }

            pointContext = payload._inpostContext;

            if (!pointContext) {
                return false;
            }

            return pointContext.providerId === this.providerId &&
                pointContext.carrierCode === context.carrierCode &&
                pointContext.methodCode === context.methodCode &&
                pointContext.countryId === context.countryId;
        },

        getContextPoint: function (context) {
            var contextKey = this.getContextKey(context);
            var payload = contextKey ? checkoutData.getShippingInPostContextPoint(contextKey) : null;

            if (this.isPayloadCompatible(payload, context)) {
                return payload;
            }

            return null;
        },

        getLegacyPoint: function (context) {
            var currentMethod = quote.shippingMethod();
            var payload = checkoutData.getShippingInPostPoint();

            if (!context || context.countryId !== 'PL' || !payload || !payload.name) {
                return null;
            }

            if (payload._inpostContext && !this.isPayloadCompatible(payload, context)) {
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
        },

        getCurrentValidationPoint: function () {
            var context = this.getCurrentContext();

            if (!this.isContextSupported(context)) {
                return null;
            }

            return this.getContextPoint(context) || this.getLegacyPoint(context);
        },

        fetchPointById: function (pointId) {
            var self = this;

            if (!pointId) {
                return Promise.resolve(null);
            }

            if (!self.pointRequestCache[pointId]) {
                self.pointRequestCache[pointId] = $.ajax({
                    url: (self.getMode() === 'prod' ? self.apiEndpointProduction : self.apiEndpointTesting) + '/points/' + pointId,
                    type: 'GET'
                }).then(function (result) {
                    if (result && result.status !== 404) {
                        return result;
                    }

                    return null;
                }, function () {
                    return null;
                });
            }

            return self.pointRequestCache[pointId];
        },

        setPoint: function (dataToSend) {
            return $.ajax({
                type: 'POST',
                url: window.checkoutConfig.base_url + 'inpost/locker/save',
                data: {
                    inpost_locker_id: dataToSend
                },
                dataType: 'json'
            }).then(function (data) {
                if (data.status === 1) {
                    return true;
                }

                return $.Deferred().reject(false);
            });
        },

        getMethodInput: function (methodValue) {
            return $('input[type="radio"][value="' + methodValue + '"]').first();
        },

        getMethodRow: function (methodValue) {
            return this.getMethodInput(methodValue).closest('tr');
        },

        getCarrierCell: function (row) {
            return row.find('td.col-carrier').first();
        },

        ensureWrapper: function (methodValue) {
            var row = this.getMethodRow(methodValue);
            var carrierCell;
            var wrapper;

            if (!row.length) {
                return $();
            }

            carrierCell = this.getCarrierCell(row);
            wrapper = row.find('[data-inpost-wrapper-for="' + methodValue + '"]');

            if (!wrapper.length) {
                wrapper = $('<div/>', {
                    'class': 'inpost-carrier-wrapper',
                    'data-inpost-wrapper': pickupMethodConfigs[methodValue],
                    'data-inpost-wrapper-for': methodValue
                });

                carrierCell.append(wrapper);
            }

            return wrapper;
        },

        insertLogoAndComment: function (methodValue) {
            var row = this.getMethodRow(methodValue);
            var carrierCell;
            var parts;
            var carrierCode;
            var methodCode;
            var logoKey;
            var commentKey;
            var logoUrl;
            var comment;
            var titleWrapper;
            var logoElement;
            var commentElement;

            if (!row.length) {
                return;
            }

            carrierCell = this.getCarrierCell(row);
            parts = methodValue.split('_');
            carrierCode = parts.shift();
            methodCode = parts.join('_');
            logoKey = carrierCode + '_' + methodCode + '_' + carrierCode;
            commentKey = logoKey + '_method_comment';
            logoUrl = window.checkoutConfig[logoKey];
            comment = window.checkoutConfig[commentKey];
            titleWrapper = carrierCell.find('.carrier-title-wrapper');

            if (!titleWrapper.length) {
                titleWrapper = $('<div/>', { 'class': 'carrier-title-wrapper' });
                carrierCell.prepend(titleWrapper);
            }

            logoElement = carrierCell.find('[data-inpost-logo-for="' + methodValue + '"]');

            if (logoUrl) {
                if (!logoElement.length) {
                    logoElement = $('<div/>', { 'data-inpost-logo-for': methodValue });
                    titleWrapper.prepend(logoElement);
                }

                logoElement.html('<img src="' + logoUrl + '" alt="" title=""/>');
            } else if (logoElement.length) {
                logoElement.remove();
            }

            commentElement = carrierCell.find('[data-inpost-comment-for="' + methodValue + '"]');

            if (comment) {
                if (!commentElement.length) {
                    commentElement = $('<div/>', {
                        'data-inpost-comment-for': methodValue,
                        'class': 'inpost-method-comment'
                    });
                    commentElement.insertBefore(this.ensureWrapper(methodValue));
                }

                commentElement.html(comment);
            } else if (commentElement.length) {
                commentElement.remove();
            }
        },

        renderPointMarkup: function (wrapper, methodValue, point) {
            var context = this.createContext('inpostlocker', methodValue.replace('inpostlocker_', ''));
            var buttonText = point ? $t('Change pickup point') : $t('Please select a pickup point');
            var html = '';

            if (point) {
                html += '<div data-inpost-point-data class="point-data">';
                html += '<p>' + $t('Selected pickup point:') + '</p>';
                html += '<p>' + point.name + ' | ' + point.address_details.city + ', ' + point.address.line1 + '</p>';
                html += '</div>';
            }

            html += '<button data-inpost-select-point ' +
                'data-inpost-provider="' + this.providerId + '" ' +
                'data-inpost-method-value="' + methodValue + '" ' +
                'data-inpost-carrier-code="' + context.carrierCode + '" ' +
                'data-inpost-method-code="' + context.methodCode + '" ' +
                'class="action secondary small" type="button" title="' + buttonText + '">' +
                buttonText +
                '</button>';

            wrapper.html(html);
        },

        renderMethod: function (methodValue) {
            var parts;
            var context;
            var wrapper;
            var point;

            this.insertLogoAndComment(methodValue);

            if (!Object.prototype.hasOwnProperty.call(pickupMethodConfigs, methodValue)) {
                return;
            }

            parts = methodValue.split('_');
            context = this.createContext(parts[0], parts.slice(1).join('_'));

            if (!this.isContextSupported(context)) {
                return;
            }

            wrapper = this.ensureWrapper(methodValue);
            point = this.getContextPoint(context);

            if (!point) {
                point = this.getLegacyPoint(context);
            }

            this.renderPointMarkup(wrapper, methodValue, point);
        },

        hydrateCurrentPoint: function () {
            var self = this;
            var context = this.getCurrentContext();
            var pointId;

            if (!self.isContextSupported(context) || self.getContextPoint(context)) {
                return Promise.resolve(self.getContextPoint(context));
            }

            if (self.getLegacyPoint(context)) {
                return Promise.resolve(self.getLegacyPoint(context));
            }

            pointId = window.checkoutConfig.quoteData && window.checkoutConfig.quoteData.inpost_locker_id;

            if (!pointId) {
                return Promise.resolve(null);
            }

            return self.fetchPointById(pointId).then(function (point) {
                var payload;

                if (!point) {
                    return null;
                }

                payload = self.getStoragePayload(context, point);
                checkoutData.setShippingInPostContextPoint(self.getContextKey(context), payload);
                checkoutData.setShippingInPostPoint(payload);

                return payload;
            });
        },

        renderInPostData: function () {
            var self = this;

            checkoutData.setShippingInPostMode(self.getMode());

            return self.hydrateCurrentPoint().then(function () {
                allInpostMethods.forEach(function (methodValue) {
                    self.renderMethod(methodValue);
                });
            });
        },

        selectShippingMethod: function (context) {
            var input = this.getMethodInput(context.methodValue);

            if (input.length && !input.is(':checked')) {
                input.prop('checked', true).trigger('click');
            }
        },

        persistSelection: function (context, point) {
            var payload = this.getStoragePayload(context, point);
            var contextKey = this.getContextKey(context);
            var self = this;

            checkoutData.setShippingInPostContextPoint(contextKey, payload);
            checkoutData.setShippingInPostPoint(payload);

            return this.setPoint(point.name).then(function () {
                self.renderMethod(context.methodValue);
                return payload;
            });
        },

        openWidget: function (context) {
            this.selectShippingMethod(context);
            coordinator.syncContext(this.providerId, context);
            coordinator.open(this.providerId, context);
        },

        bindListeners: function () {
            var self = this;

            if (self.listenersBound) {
                return;
            }

            self.listenersBound = true;

            $(document).on('click', '[data-inpost-select-point][data-inpost-provider="' + self.providerId + '"]', function (event) {
                var button = $(event.currentTarget);
                var context = self.createContext(
                    button.data('inpost-carrier-code'),
                    button.data('inpost-method-code')
                );

                event.preventDefault();
                self.openWidget(context);
            });
        },

        registerProvider: function () {
            var self = this;

            if (self.providerRegistered) {
                return;
            }

            self.providerRegistered = true;

            coordinator.registerProvider({
                id: self.providerId,
                legacyEventName: 'onpointselect',
                selectionEventName: 'onpointselect',
                getCssUrl: function () {
                    return self.getCssUrl();
                },
                getScriptUrl: function () {
                    return self.getSdkUrl();
                },
                getWidgetAttributes: function (context) {
                    return {
                        token: self.apiToken,
                        language: 'pl',
                        config: pickupMethodConfigs[context.methodValue],
                        onpoint: 'onpointselect'
                    };
                },
                onSelected: function (point, context) {
                    self.persistSelection(context, point);
                }
            });
        },

        bindShippingUpdates: function () {
            var self = this;

            if (self.shippingSubscriptionBound) {
                return;
            }

            self.shippingSubscriptionBound = true;

            shippingService.isLoading.subscribe(function (isLoading) {
                if (!isLoading) {
                    self.renderInPostData();
                }
            });
        },

        init: function () {
            if (this.initialized) {
                return;
            }

            this.initialized = true;
            this.registerProvider();
            this.bindListeners();
            this.bindShippingUpdates();
            this.renderInPostData();
        }
    };
});
