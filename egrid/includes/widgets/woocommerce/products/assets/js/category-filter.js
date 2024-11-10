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
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsCategoryFilterHandler);
    });
})(jQuery);