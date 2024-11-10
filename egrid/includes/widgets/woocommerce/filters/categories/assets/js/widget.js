(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsCategoriesFilterHandler = function($scope, $) {
        let settings = getSettings();
        let gridId = settings.grid;
        if(typeof gridId == 'undefined' || gridId == ''){
            return false;
        }
        let multiple = settings.multiple == 'yes';

        $scope.on('click', 'a', function(e) {
            e.preventDefault();
        });

        let els = $scope.find('.cat-item');
        if (els.length == 0) {
            let dropdownProductCat = $scope.find('.dropdown_product_cat');
            dropdownProductCat.on('change', function() {
                let gridEl = $('.elementor-element[data-id="' + gridId + '"]');
                let filters = gridEl.data('filters') || {};
                let categories = filters.categories || [];
                categories = $(this).val();

                filters.categories = categories;
                gridEl.data('filters', filters);
                gridEl.data('page', 1);
                gridEl.trigger('load');
            });
            dropdownProductCat.selectWoo({
                minimumResultsForSearch: 5,
                width: '100%',
            });
        } else {
            els.each(function(index, el) {
                el = $(el);
                el.on('click', function(e) {
                    e.preventDefault();
                    let gridEl = $('.elementor-element[data-id="' + gridId + '"]');
                    let filters = gridEl.data('filters') || {};
                    let categories = filters.categories || [];

                    if (el.hasClass('chosen')) {
                        if (!multiple) {
                            categories = [];
                        } else {
                            categories = categories.filter(function(e) { return e !== el.data('value') });
                        }
                        el.removeClass('chosen');
                    } else {
                        if (!multiple) {
                            els.removeClass('chosen');
                            categories = [el.data('value')];
                        } else {
                            categories.push(el.data('value'));
                        }
                        el.addClass('chosen');
                    }

                    filters.categories = categories;
                    gridEl.data('filters', filters);
                    gridEl.data('page', 1);
                    gridEl.trigger('load');
                });
            });
        }
        $scope.on('click', '.cat-item-all', function () {
            $(this).addClass('chosen'); 
            $scope.find('.cat-item').removeClass('chosen');
            let gridEl = $('.elementor-element[data-id="' + gridId + '"]');
            let filters = gridEl.data('filters') || {};
            filters.categories = [];
            gridEl.data('filters', filters);
            gridEl.data('page', 1);
            gridEl.trigger('load');
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
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products-categories-filter.default', WidgetEGridProductsCategoriesFilterHandler);
    });
})(jQuery);