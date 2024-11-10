(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsCategoryFilterHandler = function($scope, $) {
        var els = $scope.find('[egrid-products-category-filter]');
        if(els.length == 0){
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('[egrid-products-category-filter]');
        }
        var filters = $scope.data('filters') || {};
        var categories = filters.categories || [];
        els.each(function(index, el) {
            el = $(el);
            if (el.is('select')) {
                el.on('change', function() {
                    categories = el.val();

                    // Ensure categories is always an array
                    if (categories && !Array.isArray(categories)) {
                        categories = [categories];
                    }

                    filters.categories = categories;
                    $scope.data('filters', filters);
                    $scope.data('page', 1);
                    $scope.trigger('load');
                });
                el.selectWoo({
                    minimumResultsForSearch: 5,
                    width: '100%',
                    placeholder: 'Select a category',
                    allowClear: true,
                        // Add matcher to filter out uncategorized
                    matcher: function(params, data) {
                        // Return null to skip "uncategorized" options
                        if (data.text.toLowerCase() === 'uncategorised' || data.text.toLowerCase() === 'uncategorized') {
                            return null;
                        }
                        
                        // Use default matcher for other options
                        return data;
    }
                });
            } else {
                el.on('click', function(e) {
                    e.preventDefault();

                    if (el.hasClass('chosen')) {
                        categories = [];
                        el.removeClass('chosen');
                    } else {
                        categories = [el.attr("href").replace('#', '')];
                        el.addClass('chosen');
                    }

                    filters.categories = categories;
                    $scope.data('filters', filters);
                    $scope.data('page', 1);
                    $scope.trigger('load');
                });
            }
        });

        // Initialize SelectWoo for dropdown
        $('select[name="product_cat"]').selectWoo({
            minimumResultsForSearch: 5,
            width: '100%',
            placeholder: 'Select a category',
            allowClear: true,
            matcher: function(params, data) {
                // Return null to skip "uncategorized" options
                if (data.text.toLowerCase() === 'uncategorised' || data.text.toLowerCase() === 'uncategorized') {
                    return null;
                }
                
                // Use default matcher for other options
                return data;
            }
        }).on('change', function() {
            var selectedValue = $(this).val();
            
            // Close drawer when option is cleared (selectedValue is null/empty)
            if (!selectedValue) {
                // Close the dropdown
                $(this).blur();
                
                // If using Elementor's drawer
                if (typeof elementorFrontend !== 'undefined' && elementorFrontend.elements.$body.hasClass('e-n-menu-active')) {
                    elementorFrontend.elements.$body.removeClass('e-n-menu-active');
                }
            }
            
            // Update URL with selected category
            var currentUrl = new URL(window.location.href);
            if (selectedValue) {
                currentUrl.searchParams.set('product_cat', selectedValue);
            } else {
                currentUrl.searchParams.delete('product_cat');
            }
            window.history.pushState({}, '', currentUrl);
            
            // Trigger filter update
            $scope.trigger('load');
        });

        // Set initial value if exists in URL
        var urlParams = new URLSearchParams(window.location.search);
        var initialCategory = urlParams.get('product_cat');
        if (initialCategory) {
            $('select[name="product_cat"]').val(initialCategory).trigger('change.select2');
        }
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsCategoryFilterHandler);
    });
})(jQuery);
