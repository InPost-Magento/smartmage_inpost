define([], function () {
    'use strict';

    if (window.inpostMode === 'test') {
        require(['inpost-geowidget-sandbox']);
    } else if (window.inpostMode === 'prod') {
        require(['inpost-geowidget']);
    }

    return {};
});
