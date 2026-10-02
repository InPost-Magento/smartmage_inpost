requirejs([
    'jquery'
], function ($) {
    'use strict';

    $(document).ready(function () {
        if (!window.checkoutConfig || !window.checkoutConfig.smartmageInpostCheckoutEnabled) {
            return;
        }

        requirejs(['inPostPaczkomaty'], function (inPostPaczkomaty) {
            inPostPaczkomaty.init();
        });
    });
});
