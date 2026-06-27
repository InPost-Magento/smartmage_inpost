define([], function () {
    'use strict';

    var coordinatorKey = '__inpostCheckoutGeowidgetCoordinator';

    function clone(value) {
        return value ? JSON.parse(JSON.stringify(value)) : null;
    }

    function createCoordinator() {
        var providers = {};
        var state = {
            generation: 0,
            activeProviderId: null,
            activeContext: null,
            host: null,
            frameContainer: null,
            frame: null
        };

        function getContextKey(providerId, context) {
            if (!providerId || !context) {
                return null;
            }

            return [
                providerId,
                context.carrierCode || '',
                context.methodCode || '',
                context.countryId || ''
            ].join('::');
        }

        function getActiveProvider() {
            return state.activeProviderId ? providers[state.activeProviderId] : null;
        }

        function ensureHost() {
            var host;
            var closeElements;

            if (state.host && state.frameContainer) {
                return;
            }

            host = document.getElementById('inpost-checkout-geowidget-modal');

            if (!host) {
                host = document.createElement('div');
                host.id = 'inpost-checkout-geowidget-modal';
                host.className = 'inpost-modal inpost-geowidget-modal';
                host.style.display = 'none';
                host.innerHTML =
                    '<div class="modal-overlay" data-role="close"></div>' +
                    '<div class="inpost-modal__container modal-content">' +
                    '<a href="#" class="action-close btn-close" data-role="close"></a>' +
                    '<div class="inpost-geowidget-frame-container"></div>' +
                    '</div>';
                document.body.appendChild(host);
            }

            closeElements = host.querySelectorAll('[data-role="close"]');
            Array.prototype.forEach.call(closeElements, function (element) {
                if (element.dataset.inpostBound === 'true') {
                    return;
                }

                element.dataset.inpostBound = 'true';
                element.addEventListener('click', function (event) {
                    event.preventDefault();
                    close();
                });
            });

            if (host.dataset.inpostEscapeBound !== 'true') {
                host.dataset.inpostEscapeBound = 'true';
                document.addEventListener('keyup', function (event) {
                    if (event.key === 'Escape' && state.host && state.host.style.display !== 'none') {
                        close();
                    }
                });
            }

            state.host = host;
            state.frameContainer = host.querySelector('.inpost-geowidget-frame-container');
        }

        function setVisible(isVisible) {
            ensureHost();
            state.host.style.display = isVisible ? 'block' : 'none';
            state.host.classList.toggle('is-active', isVisible);
            document.body.classList.toggle('_has-inpost-modal', isVisible);
        }

        function destroyFrame() {
            if (state.frame && state.frame.parentNode) {
                state.frame.parentNode.removeChild(state.frame);
            }

            state.frame = null;
        }

        function invalidate() {
            state.generation += 1;
            destroyFrame();
        }

        function close() {
            var provider = getActiveProvider();

            invalidate();
            setVisible(false);

            if (provider && typeof provider.onClose === 'function') {
                provider.onClose(clone(state.activeContext));
            }
        }

        function syncContext(providerId, context) {
            var previousKey = getContextKey(state.activeProviderId, state.activeContext);
            var nextKey = getContextKey(providerId, context);

            if (previousKey && nextKey && previousKey !== nextKey) {
                invalidate();
            }

            state.activeProviderId = providerId;
            state.activeContext = clone(context);
        }

        function createBaseDocument(doc) {
            doc.open();
            doc.write(
                '<!doctype html>' +
                '<html><head><meta charset="utf-8"><style>' +
                'html,body{height:100%;margin:0;padding:0;background:#fff;}' +
                '#inpost-geowidget-root{height:100%;width:100%;}' +
                'inpost-geowidget{display:block;height:100%;width:100%;}' +
                '</style></head><body><div id="inpost-geowidget-root"></div></body></html>'
            );
            doc.close();
        }

        function createStylesheet(doc, href) {
            var link;

            if (!href) {
                return;
            }

            link = doc.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            doc.head.appendChild(link);
        }

        function createScript(doc, src, onLoad, onError) {
            var script = doc.createElement('script');

            script.src = src;
            script.async = true;
            script.onload = onLoad;
            script.onerror = onError;
            doc.head.appendChild(script);
        }

        function mountWidget(frame, provider, context, generation) {
            var doc = frame.contentWindow.document;
            var widget = doc.createElement('inpost-geowidget');
            var attributes = provider.getWidgetAttributes(context) || {};
            var eventName = provider.selectionEventName || 'onpointselect';
            var root = doc.getElementById('inpost-geowidget-root');

            Object.keys(attributes).forEach(function (key) {
                if (attributes[key] !== undefined && attributes[key] !== null) {
                    widget.setAttribute(key, attributes[key]);
                }
            });

            widget.addEventListener(eventName, function (event) {
                if (generation !== state.generation || frame !== state.frame) {
                    return;
                }

                if (provider.legacyEventName) {
                    document.dispatchEvent(new CustomEvent(provider.legacyEventName, {
                        bubbles: true,
                        detail: event.detail
                    }));
                }

                if (typeof provider.onSelected === 'function') {
                    provider.onSelected(event.detail, clone(context));
                }

                close();
            });

            root.innerHTML = '';
            root.appendChild(widget);

            if (typeof provider.onWidgetMounted === 'function') {
                provider.onWidgetMounted(widget, clone(context));
            }
        }

        function open(providerId, context) {
            var provider = providers[providerId];
            var generation;
            var frame;
            var doc;

            if (!provider) {
                throw new Error('Unknown InPost geowidget provider: ' + providerId);
            }

            syncContext(providerId, context);
            state.generation += 1;
            generation = state.generation;

            ensureHost();
            destroyFrame();

            frame = document.createElement('iframe');
            frame.className = 'inpost-geowidget-frame';
            frame.setAttribute('title', 'InPost geowidget');
            frame.setAttribute('scrolling', 'no');
            frame.style.width = '100%';
            frame.style.height = '80vh';
            frame.style.minHeight = '560px';
            frame.style.border = '0';
            frame.style.display = 'block';

            state.frameContainer.innerHTML = '';
            state.frameContainer.appendChild(frame);
            state.frame = frame;

            setVisible(true);

            doc = frame.contentWindow.document;
            createBaseDocument(doc);
            createStylesheet(doc, provider.getCssUrl ? provider.getCssUrl(context) : null);
            createScript(
                doc,
                provider.getScriptUrl(context),
                function () {
                    if (generation !== state.generation || frame !== state.frame) {
                        return;
                    }

                    mountWidget(frame, provider, context, generation);
                },
                function () {
                    if (typeof provider.onError === 'function') {
                        provider.onError(clone(context));
                    }

                    close();
                }
            );
        }

        return {
            registerProvider: function (provider) {
                providers[provider.id] = provider;
                return providers[provider.id];
            },

            getContextKey: getContextKey,

            getActiveContext: function () {
                return clone(state.activeContext);
            },

            getActiveProviderId: function () {
                return state.activeProviderId;
            },

            syncContext: syncContext,
            open: open,
            close: close,
            invalidate: invalidate
        };
    }

    if (!window[coordinatorKey]) {
        window[coordinatorKey] = createCoordinator();
    }

    return window[coordinatorKey];
});
