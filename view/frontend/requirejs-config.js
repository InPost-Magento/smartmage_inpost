var config = {
    map: {
        '*': {
            'inPostPaczkomaty' : 'Smartmage_Inpost/js/inpost-paczkomaty',
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Smartmage_Inpost/js/mixin/shipping-mixin': true
            },
        }
    },
    paths: {
        'inPostSdk': [
            'https://geowidget.easypack24.net/js/sdk-for-javascript'
        ],
    },
};


