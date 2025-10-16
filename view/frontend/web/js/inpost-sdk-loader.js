define([], function () {
    'use strict';

    function loadInpostScript() {
        if (window.checkoutConfig.inpost_mode === 'test') {
            require(['https://sandbox-global-geowidget-sdk.easypack24.net/inpost-geowidget.js']);
        } else if (window.checkoutConfig.inpost_mode === 'prod') {
            require(['https://geowidget.inpost-group.com/inpost-geowidget.js']);
        }
    }

    function waitForCheckoutConfig() {
        if (window.checkoutConfig && typeof window.checkoutConfig.inpost_mode !== 'undefined') {
            loadInpostScript();
        } else {
            setTimeout(waitForCheckoutConfig, 500);
        }
    }

    waitForCheckoutConfig();

    return {};
});
