(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsOrderHandler = function($scope, $) {
        var els = $scope.find('select[name="orderby"]');
        els.each(function(index, el) {
            el = $(el);
            el.off('change');
            el.closest('form').on('submit', function () {
                return false;
            });
            el.on('change', function(e) {
                e.preventDefault();

                var orderVal = el.val();
                var orderby = $scope.data('orderby') || '';
                var order = $scope.data('order') || '';
                if(typeof orderVal != 'undefined' && orderVal != ''){
                    order = 'DESC';
                    orderby = orderVal;
                    if(orderVal == 'price' || orderVal == 'title'){
                        order = 'ASC';
                    }
                    if(orderVal == 'price-desc'){
                        orderby = 'price';
                    }
                    if(orderVal == 'title-desc'){
                        orderby = 'title';
                    }
                }

                $scope.data('orderby', orderby);
                $scope.data('order', order);
                $scope.data('page', 1);
                $scope.trigger('load');
            });
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsOrderHandler);
    });
})(jQuery);