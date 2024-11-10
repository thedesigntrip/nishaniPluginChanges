(function ($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsColumnHandler = function ($scope, $) {
        var els = $scope.find('[data-col]');
        if (els.length == 0) {
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('[data-col]');
        }
        var columns = $scope.data('columns') || {};
        var productsList = $scope.find('.products');
        els.each(function (index, el) {
            el = $(el);
            el.on('click', function (e) {
                e.preventDefault();

                if (el.hasClass('chosen')) {
                    columns = 4;
                } else {
                    columns = el.data('col');
                    ;
                }
                els.removeClass('chosen');
                el.addClass('chosen');

                var classes = productsList.attr('class');
                classes = classes.split(' ');
                classes.forEach(function (c, i) {
                    if (c.match(/^columns-[A-z0-9]*$/g)) {
                        productsList.removeClass(c);
                    }
                });

                productsList.removeClass('columns-' + columns);
                productsList.addClass('columns-' + columns);

                $scope.data('columns', columns);
                // $scope.data('page', 1);
                // $scope.trigger('load');
            });
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsColumnHandler);
    });
})(jQuery);