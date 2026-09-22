requirejs([
    'domReady!'
], function () {
    'use strict';

    requirejs([
        'inPostPaczkomaty'
    ], function (inPostPaczkomaty) {
        inPostPaczkomaty.init();
    });
});
