(function () {
    'use strict';

    function initInPost() {
        if (!window.checkoutConfig || !window.checkoutConfig.smartmageInpostCheckoutEnabled) {
            return;
        }

        requirejs([
            'inPostPaczkomaty'
        ], function (inPostPaczkomaty) {
            inPostPaczkomaty.init();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInPost, { once: true });
    } else {
        initInPost();
    }
}());
