(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsPaginationHandler = function($scope, $) {
        var pageEls = $scope.find('.page-numbers');
        pageEls.on('click', function(e) {
            e.preventDefault();

            pageEls.removeClass('current');
            $(this).addClass('current');
            var page = $(this).attr("href").replace('#', '');
            $scope.data('page', page);
            $scope.trigger('load');
        });

        $scope.find('[egrid-products-loadmore]').on('click', function(e) {
            try {
                var total_pages = parseInt($(this).data('total-pages'));
                var current_page = parseInt($(this).data('current-page'));
                var next_page = current_page + 1;
                if (next_page <= total_pages) {
                    $scope.data('page', next_page);
                    $scope.data('loadmore', true);
                    if (next_page == total_pages) {
                        $(this).remove();
                    }
                    $scope.trigger('load');
                } else {
                    $(this).remove();
                }
            } catch (e) {

            }
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsPaginationHandler);
    });
})(jQuery);