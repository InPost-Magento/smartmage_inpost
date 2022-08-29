requirejs([
    'jquery',
    'inPostPaczkomaty',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
],function($, inPostPaczkomaty, checkoutData, fullScreenLoader) {
    'use strict';

    const inPost = {
        closeModal: function() {
            $(document).ready(function() {
                $(document).on('click', '[data-inpost-modal-btn-close]', function() {
                    const modalWrapper = $('[data-inpost-modal]');
                    modalWrapper.remove();
                });
            });
        },

        selectedPoint: function() {
            $(document).on('onpointselect', function(event) {
                const modalWrapper = $('[data-inpost-modal]');
                const point = event.originalEvent.detail;

                inPostPaczkomaty.setPoint(point.name).then(function() {
                    inPostPaczkomaty.cleanPointDataHtml().then(function() {
                        inPostPaczkomaty.pointDataHtml(point, inPostPaczkomaty.selectPointHtml(true), true).then(function() {
                            checkoutData.setShippingInPostPoint(point);
                            modalWrapper.remove();
                            fullScreenLoader.stopLoader();
                        });
                    });
                });
            });
        },

        showMap: function() {
            $(document).on('click', '[data-inpost-select-point]', function(e) {
                e.preventDefault();
                const getPointType = $(this).parent().data('inpost-wrapper');

                inPostPaczkomaty.createModal(getPointType);
            });
        },

        init: function() {
            this.closeModal();
            this.selectedPoint();
            this.showMap();
            inPostPaczkomaty.init();
        }
    }

    $(document).ready(function() {
        inPost.init();
    })
});
