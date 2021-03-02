var config = {
    map: {
        '*': {
            'easyPackWidget' : 'Smartmage_Inpost/js/easyPackWidget',
            'configEasyPack' : 'Smartmage_Inpost/js/configEasyPack',
        }
    },
    paths: {
        'inPostSdk': [
            'https://geowidget.easypack24.net/js/sdk-for-javascript'
        ],
    },

    shim: {
        'easyPackWidget': ['jquery', 'inPostSdk', 'configEasyPack'],
    }
};
