requirejs([
    'jquery',
    'ko',
    'inPostGeoWidget',
], function ($, ko) {
    'use strict';

    var inPostModal = {
        apiToken: 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJzQlpXVzFNZzVlQnpDYU1XU3JvTlBjRWFveFpXcW9Ua2FuZVB3X291LWxvIn0.eyJleHAiOjE5NjYwODA1MjcsImlhdCI6MTY1MDcyMDUyNywianRpIjoiMWI3ZTlmYzEtNjk5Ny00MzEyLTgzMWMtN2Q4ZTY5MWJmMDQ5IiwiaXNzIjoiaHR0cHM6Ly9sb2dpbi5pbnBvc3QucGwvYXV0aC9yZWFsbXMvZXh0ZXJuYWwiLCJzdWIiOiJmOjEyNDc1MDUxLTFjMDMtNGU1OS1iYTBjLTJiNDU2OTVlZjUzNTpNeFVub2t4dTdOOW9ZTmpQWFFXVE1CWWkyYUtxX05PQUNxMFYwZHlLOWUwIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoic2hpcHgiLCJzZXNzaW9uX3N0YXRlIjoiN2FhMWQwMjYtNTIxZi00YWNhLTk5YzctNTI3ODc4MjBkZjZjIiwiYWNyIjoiMSIsInNjb3BlIjoib3BlbmlkIGFwaTphcGlwb2ludHMiLCJhbGxvd2VkX3JlZmVycmVycyI6IiIsInV1aWQiOiJlOTAxNmU1Yy0xNzdkLTQ5NTgtYmY3NS1kYzE2NmVjMWJiN2YifQ.Zo5djVDHcAathxsI1zdiBOmrkY_jm4HDqLLfIPG7e5xcBlxorVxtcNKUm7_wezUHfTECuOqAxFCjuzB89jBKC5TtY8lJgmbm8bWWiDpyJ6mULjkeA-PGIw351wKGH46wH472RR06PUjYZx2B5WpLCFquZNPeIItsJVtsYg52JpAwiYyfYmm4g1CFk3tU7TtXy7GAYfkdZ5aGBsO_r0_sS6BmOfjd_UCo0egcWLRFW_yVR88uhQdtY0GjGkwzg0p7klSsZPCUQjOCTtZKYhoMvKHnV8sAhwgPg9h2VqyXj07DnKqlUHnaX5l0nlqt3zp5FfVoPhmB0oAPTSYQFSHVDQ',

        createModal: function() {
            let html = '<div data-inpost-modal class="inpost-modal is-active">';
            html += '<div class="inpost-modal__container">';
            html += '<div data-inpost-modal-btn-close class="btn-close"></div>';
            html += '<inpost-geowidget onpoint="onpointselect" token="'+ this.apiToken +'" language="pl"  config="parcelCollectPayment"></inpost-geowidget>';
            html += '</div>';
            html += '</div>';

            $('body').append(html);
        },

        closeModal: function() {
            $(document).ready(function() {
                $(document).on('click', '[data-inpost-modal-btn-close]', function() {
                    const modalWrapper = $('[data-inpost-modal]');

                    modalWrapper.removeClass('is-active');
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

                modalWrapper.removeClass('is-active');
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
        }
    };

    $(document).ready(function() {
        inPostModal.init();
    })
});
