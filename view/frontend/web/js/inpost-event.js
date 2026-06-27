requirejs([
    'jquery',
    'inPostPaczkomaty'
], function ($, inPostPaczkomaty) {
    'use strict';

    $(document).ready(function () {
        inPostPaczkomaty.init();
    });
});
