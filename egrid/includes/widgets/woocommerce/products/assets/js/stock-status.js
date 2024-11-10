(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsStockStatusFilterHandler = function($scope, $) {
        var els = $scope.find('[egrid-products-stock-status-filter]');
        if(els.length == 0){
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('[egrid-products-stock-status-filter]');
        }
        var filters = $scope.data('filters') || {};
        els.each(function(index, el) {
            el = $(el);
            el.on('click', function(e) {
                e.preventDefault();

                if (el.hasClass('chosen')) {
                    stock_status = '';
                    el.removeClass('chosen');
                } else {
                    stock_status = el.attr("href").replace('#', '');
                    el.addClass('chosen');
                }

                filters.stock_status = stock_status;
                $scope.data('filters', filters);
                $scope.data('page', 1);
                $scope.trigger('load');
            });
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsStockStatusFilterHandler);
    });
})(jQuery);