(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsFilterPopupHandler = function($scope, $) {
        var widgetId = $scope.data('id');
        var enableFilterPopup = getSettings('enable_filter_popup');
        var filtersModal = $scope.find('#egrid-products-filters-modal-' + widgetId);

        $scope.find('.egrid-modal-switch').click(function(e) {
            e.preventDefault();
            var modal = $(this).data('target');
            $(this).toggleClass('open');
            $(modal).toggleClass('open');
            $('html').toggleClass('cms-modal-opened');
            $('body').find('.egrid-modal-overlay').toggleClass('open');
        });
        $scope.find('.egrid-modal-close').on('click', function(e) {
            e.preventDefault();

            $(this).parents('.egrid-modal').removeClass('open');
             $('html').removeClass('cms-modal-opened');
            $('body').find('.egrid-modal-overlay').removeClass('open');
        });
        $('body').find('.egrid-modal-overlay').on('click', function(e) {
            e.preventDefault();
            $(this).removeClass('open');
            $('html').removeClass('cms-modal-opened');
            $('body').find('.egrid-modal.open').removeClass('open');
        });

        var isOpen = $('body').find('#egrid-products-filters-modal-' + widgetId + '.clone').hasClass('open');
        $('body').find('#egrid-products-filters-modal-' + widgetId + '.clone').remove();
        if(enableFilterPopup == 'yes'){
            filtersModal.appendTo($('body'));
            filtersModal.addClass('clone');
            if(isOpen){
                filtersModal.addClass('open');
            }
        }

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
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsFilterPopupHandler);
    });
})(jQuery);