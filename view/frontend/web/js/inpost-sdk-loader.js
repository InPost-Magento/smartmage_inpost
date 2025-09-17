define([], function () {
    'use strict';

    function loadInpostScript() {
        if (window.checkoutConfig.inpost_mode === 'test') {
            require(['https://sandbox-easy-geowidget-sdk.easypack24.net/inpost-geowidget.js']);
        } else if (window.checkoutConfig.inpost_mode === 'prod') {
            require(['https://geowidget.inpost.pl/inpost-geowidget.js']);
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
