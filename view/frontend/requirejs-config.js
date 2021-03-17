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
            'Magento_Checkout/js/checkout-loader': {
                'Smartmage_Inpost/js/mixin/checkout-loader-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Smartmage_Inpost/js/mixin/checkout-data-mixin': true
            }
        }
    },
    paths: {
        'inPostSdk': [
            'https://geowidget.easypack24.net/js/sdk-for-javascript'
        ],
    },
};