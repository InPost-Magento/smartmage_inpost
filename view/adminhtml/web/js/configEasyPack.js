require([
    'jquery',
    'inPostSdk'
], function ($, sdk) {

    var inPost = {
        configEasyPack: function() {
            easyPack.init({
                instance: 'pl',
                mapType: 'osm',
                searchType: 'osm',
                points: {
                    types: ['parcel_locker'],
                },
                map: {
                    useGeolocation: true,
                    initialTypes: ['parcel_locker']
                }
            });
        },

        init: function() {
            this.configEasyPack();
        }
    }

    $(document).ready(function() {
        inPost.init();
    });
});
