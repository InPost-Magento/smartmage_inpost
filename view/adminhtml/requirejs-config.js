var config = {
    map: {
        '*': {
            'easyPackWidget' : 'Smartmage_Inpost/js/easyPackWidget',
            'inPostShowModal' : 'Smartmage_Inpost/js/inPostShowPoint',
        }
    },
    paths: {
        'inPostSdk': [
            'https://geowidget.easypack24.net/js/sdk-for-javascript'
        ],
        'inPostGeoWidget': [
            'https://geowidget.inpost.pl/inpost-geowidget'
        ],
    },
    config: {
        mixins: {
            'Magento_Ui/js/grid/massactions': {
                'Smartmage_Inpost/js/grid/massactions-mixin': true
            },
        }
    },
    shim: {
        'easyPackWidget': ['jquery', 'inPostSdk'],
        'inPostShowModal': ['jquery', 'inPostSdk'],
    }
};
