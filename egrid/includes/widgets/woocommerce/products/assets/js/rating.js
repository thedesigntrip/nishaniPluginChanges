(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsRatingHandler = function($scope, $) {
        var els = $scope.find('[egrid-products-rating-filter]');
        if(els.length == 0){
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('[egrid-products-rating-filter]');
        }
        var filters = $scope.data('filters') || {};
        var rating = filters.rating || '';
        els.each(function(index, el) {
            el = $(el);
            el.on('click', function(e) {
                e.preventDefault();

                if (el.hasClass('chosen')) {
                    rating = '';
                    el.removeClass('chosen');
                } else {
                    rating = el.attr("href").replace('#', '');
                    el.addClass('chosen');
                }

                filters.rating = rating;
                $scope.data('filters', filters);
                $scope.data('page', 1);
                $scope.trigger('load');
            });
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsRatingHandler);
    });
})(jQuery);