(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsAttributeFilterHandler = function($scope, $) {
        var els = $scope.find('[egrid-products-attribute-filter]');
        if(els.length == 0){
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('[egrid-products-attribute-filter]');
        }
        var filters = $scope.data('filters') || {};
        var attribtues = filters.attributes || {};
        els.each(function(index, el) {
            el = $(el);
            var taxonomy = el.attr('egrid-products-attribute-filter');
            if (el.is('select')) {
                el.on('change', function() {
                    attribtues[taxonomy] = el.val();

                    filters.attributes = attribtues;
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
                        delete attribtues[taxonomy];
                        el.removeClass('chosen');
                    } else {
                        attribtues[taxonomy] = el.attr("href").replace('#', '');
                        el.addClass('chosen');
                    }

                    filters.attributes = attribtues;
                    $scope.data('filters', filters);
                    $scope.data('page', 1);
                    $scope.trigger('load');
                });
            }
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsAttributeFilterHandler);
    });
})(jQuery);