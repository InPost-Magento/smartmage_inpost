requirejs([
    'jquery',
    'ko',
    'inPostGeoWidget',
], function ($, ko) {
    'use strict';

    var inPostModal = {

        createModal: function() {
            let geowidgetConfig = jQuery('[name="order[shipping_method]"][id$="cod"]:checked').length ? 'parcelCollectPayment' : 'parcelCollect';
            let html = '<div data-inpost-modal class="inpost-modal is-active">';
            html += '<div class="inpost-modal__container">';
            html += '<div data-inpost-modal-btn-close class="btn-close"></div>';
            html += '<inpost-geowidget onpoint="onpointselect" token="'+ $('.inpost_shipment-section').data('inposttoken') +'" language="pl"  config="' + geowidgetConfig + '"></inpost-geowidget>';
            html += '</div>';
            html += '</div>';

            $('body').append(html);
        },

        closeModal: function() {
            $(document).ready(function() {
                $(document).on('click', '[data-inpost-modal-btn-close], [data-inpost-modal]', function() {
                    const modalWrapper = $('[data-inpost-modal]');
                    modalWrapper.removeClass('is-active');
                });
                $(document).on('keyup', function(e) {
                    if (e.key == "Escape") {
                        $('[data-inpost-modal]').removeClass('is-active');
                    }
                });
            });
        },

        selectedPoint: function() {
            $(document).on('onpointselect', function(event) {
                const modalWrapper = $('[data-inpost-modal]');
                const point = event.originalEvent.detail
                const targetLocker = $('input[name="shipment_fieldset[target_locker]"]');
                const btnShowPoint = $('[data-inpost-select-point]');

                if (targetLocker.length) {
                    ko.dataFor(targetLocker.get(0)).value(point.name);
                    btnShowPoint.attr('data-inpost-select-point', point.name);
                }
                if ($('input[name="order[inpost_locker_id]"]').length) {
                    $('input[name="order[inpost_locker_id]"]').val(point.name);
                    $('.details-target_point strong').text(point.name);
                    btnShowPoint.attr('data-inpost-select-point', point.name);
                    order.setShippingMethod($('[name="order[shipping_method]"]:checked').val());
                }

                modalWrapper.removeClass('is-active');
            });
        },

        selectedShippingMethod: function() {
            var InPostLockerRadioButtons = $('[id^=s_method_inpostlocker_]');
            InPostLockerRadioButtons.on('click', function(event) {
                var InPostLockerRadioButton = event.target;
                var openMapLink = $(InPostLockerRadioButton).parent().find('a');
                openMapLink.trigger("click");
            });
        },

        showModal: function() {
            const self = this;

            $(document).on('click', '[data-inpost-select-point]', function(e) {
                e.preventDefault();
                const point = $(this).attr('data-inpost-select-point');

                self.createModal(point);
            });
        },

        init: function() {
            this.showModal();
            this.closeModal();
            this.selectedPoint();
            this.selectedShippingMethod();
        }
    };

    $(document).ready(function() {
        inPostModal.init();
    });
});
