define([
    'rjsResolver'
], function (resolver) {
    'use strict';

    return function (target) {

        function hideLoader($loader) {
            $loader.parentNode.removeChild($loader);

            if (!window.checkoutConfig || !window.checkoutConfig.smartmageInpostCheckoutEnabled) {
                return;
            }

            requirejs(['inPostPaczkomaty'], function (inPostPaczkomaty) {
                inPostPaczkomaty.init();
            });
        }

        target = function (config, $loader) {
            resolver(hideLoader.bind(null, $loader));
        };

        return target;
    };
});
