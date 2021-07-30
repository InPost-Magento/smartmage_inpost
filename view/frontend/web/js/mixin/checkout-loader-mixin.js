define([
    'jquery',
    'rjsResolver',
    'inPostPaczkomaty'
], function ($,resolver, inPostPaczkomaty) {
    'use strict';

    return function (target) {

        function hideLoader($loader) {
            $loader.parentNode.removeChild($loader);

            inPostPaczkomaty.init();
        }

        target = function (config, $loader) {
            resolver(hideLoader.bind(null, $loader));
        };

        return target;
    }

});
