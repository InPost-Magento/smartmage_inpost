var config = {
    map: {
        '*': {
            'easyPackWidget' : 'Smartmage_Inpost/js/easyPackWidget',
            'inPostShowModal' : 'Smartmage_Inpost/js/inPostShowModal',
            'Magento_Sales/order/create/scripts': 'Smartmage_Inpost/js/order/create/scripts'
        }
    },
    paths: {
        'inPostSdk': 'Smartmage_Inpost/js/inpostSdkAdminLoader',
        'inpost-geowidget-sandbox': 'https://sandbox-global-geowidget-sdk.easypack24.net/inpost-geowidget',
        'inpost-geowidget': 'https://geowidget.inpost-group.com/inpost-geowidget'
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
