define([
        'jquery',
        'ko',
        'underscore',
        'uiRegistry',
        'mageUtils'
    ], function ($, ko, _, uiRegistry, utils) {
        'use strict';

        return function (target) {
            return target.extend({
                applyActionAndResetSelected: function (action, data) {
                    var itemsType = data.excludeMode ? 'excluded' : 'selected',
                        selections = {};

                    selections[itemsType] = data[itemsType];

                    if (!selections[itemsType].length) {
                        selections[itemsType] = false;
                    }

                    _.extend(selections, data.params || {});

                    utils.submit({
                        url: action.url,
                        data: selections
                    });

                    var gridCheckbox = $('.data-grid-checkbox-cell').find('.admin__control-checkbox');
                    if ( gridCheckbox.length > 0 ) {
                        ko.dataFor(gridCheckbox[0]).selected([]);
                    }
                }
            });
        }
    }
);
