(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsHandler = function($scope, $) {
        var element_id = $scope.data('id');

        $scope.on('open-loader', function() {
            $scope.find('.egrid-products-wrapper').addClass('loading');
        });

        $scope.on('close-loader', function() {
            $scope.find('.egrid-products-wrapper').removeClass('loading');
        });

        $scope.on('load', function() {
            var filters = $scope.data('filters') || {};
            var orderby = $scope.data('orderby') || '';
            var order = $scope.data('order') || '';
            var page = $scope.data('page') || 1;
            var columns = $scope.data('columns') || '';
            var loadmore = $scope.data('loadmore') || false;
            if (page == 1) {
                loadmore = false;
            }

            // Store current scroll position
            var scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

            $.ajax({
                url: egrid_products.ajax_url,
                type: 'POST',
                beforeSend: function() {
                    $scope.trigger('open-loader');
                },
                data: {
                    action: 'egrid_products',
                    element_id: element_id,
                    post_id: egrid_products.post_id,
                    page: page,
                    orderby: orderby,
                    order: order,
                    columns: columns,
                    loadmore: loadmore,
                    filters: JSON.stringify(filters),
                    settings: JSON.stringify(getSettings()),
                }
            }).done(function(res) {
                if (res.success) {
                    var $newScope = $(res.data);
                    if (loadmore == true) {
                        $scope.find('[egrid-products-loadmore]').data('current-page', page);
                        $scope.find('.egrid-products-content .products').append($newScope.find('.products').html());
                    } else {
                        $newScope.data('filters', filters);
                        $newScope.data('orderby', orderby);
                        $newScope.data('order', order);
                        $newScope.data('columns', columns);
                        $scope.find('.egrid-products-wrapper').replaceWith($newScope.find('.egrid-products-wrapper'));
                        
                        // Restore scroll position instead of animating to top
                        window.scrollTo({
                            top: scrollPosition,
                            behavior: 'instant'
                        });
                    }
                    if (typeof wc_add_to_cart_variation_params !== 'undefined') {
                        $('.variations_form').each(function() {
                            $(this).wc_variation_form();
                        });
                    }
                    elementorFrontend.elementsHandler.runReadyTrigger($scope);
                } else {
                    console.log(res.message);
                }
            }).fail(function(res) {
                console.log(res);
            }).always(function() {
                $scope.trigger('close-loader');
            });
        });

        function getSettings(setting) {
            let settings = {};
            const modelCID = $scope.data('model-cid') || '',
                isEdit = $scope.hasClass('elementor-element-edit-mode');
            if (isEdit && modelCID) {
                const data = elementorFrontend.config.elements.data[modelCID],
                    attributes = data.attributes;
                let type = attributes.widgetType || attributes.elType;
                if (attributes.isInner) {
                    type = 'inner-' + type;
                }
                let dataKeys = elementorFrontend.config.elements.keys[type];
                if (!dataKeys) {
                    dataKeys = elementorFrontend.config.elements.keys[type] = [];
                    $.each(data.controls, (name, control) => {
                        if (control.frontend_available) {
                            dataKeys.push(name);
                        }
                    });
                }
                $.each(data.getActiveControls(), function(controlKey) {
                    if (-1 !== dataKeys.indexOf(controlKey)) {
                        let value = attributes[controlKey];
                        if (value.toJSON) {
                            value = value.toJSON();
                        }
                        settings[controlKey] = value;
                    }
                });
            } else {
                settings = $scope.data('settings') || {};
            }
            return getItems(settings, setting);
        }

        function getItems(items, itemKey) {
            if (itemKey) {
                const keyStack = itemKey.split('.'),
                    currentKey = keyStack.splice(0, 1);
                if (!keyStack.length) {
                    return items[currentKey];
                }
                if (!items[currentKey]) {
                    return;
                }
                return this.getItems(items[currentKey], keyStack.join('.'));
            }
            return items;
        }
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsHandler);
    });
})(jQuery);