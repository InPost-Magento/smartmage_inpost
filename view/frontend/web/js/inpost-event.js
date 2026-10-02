(function () {
    'use strict';

    function initInPost() {
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
