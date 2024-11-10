(function($) {
    /**
     * @param $scope The Widget wrapper element as a jQuery element
     * @param $ The jQuery alias
     */
    var WidgetEGridProductsPriceFilterHandler = function($scope, $) {
        if (typeof egrid_price_filter_params === 'undefined') {
            return false;
        }

        $scope.on('price_slider_create price_slider_slide', function(event, min, max) {

            $scope.find('.price_slider_amount span.from').html(accounting.formatMoney(min, {
                symbol: egrid_price_filter_params.currency_format_symbol,
                decimal: egrid_price_filter_params.currency_format_decimal_sep,
                thousand: egrid_price_filter_params.currency_format_thousand_sep,
                precision: egrid_price_filter_params.currency_format_num_decimals,
                format: egrid_price_filter_params.currency_format
            }));

            $scope.find('.price_slider_amount span.to').html(accounting.formatMoney(max, {
                symbol: egrid_price_filter_params.currency_format_symbol,
                decimal: egrid_price_filter_params.currency_format_decimal_sep,
                thousand: egrid_price_filter_params.currency_format_thousand_sep,
                precision: egrid_price_filter_params.currency_format_num_decimals,
                format: egrid_price_filter_params.currency_format
            }));

            $scope.trigger('price_slider_updated', [min, max]);
        });

        function init_price_filter() {
            $scope.find('input#min_price, input#max_price').hide();
            $scope.find('.price_slider, .price_label').show();

            var min_price = $scope.find('.price_slider_amount #min_price').data('min'),
                max_price = $scope.find('.price_slider_amount #max_price').data('max'),
                step = $scope.find('.price_slider_amount').data('step') || 1,
                current_min_price = $scope.find('.price_slider_amount #min_price').val(),
                current_max_price = $scope.find('.price_slider_amount #max_price').val()
                timer = 0;

            $scope.find('.price_slider:not(.ui-slider)').slider({
                range: true,
                animate: true,
                min: min_price,
                max: max_price,
                step: step,
                values: [current_min_price, current_max_price],
                create: function() {
                    $scope.find('.price_slider_amount #min_price').val(current_min_price);
                    $scope.find('.price_slider_amount #max_price').val(current_max_price);

                    $scope.trigger('price_slider_create', [current_min_price, current_max_price]);
                },
                slide: function(event, ui) {
                    $scope.find('input#min_price').val(ui.values[0]);
                    $scope.find('input#max_price').val(ui.values[1]);

                    $scope.trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
                },
                change: function(event, ui) {
                    $scope.trigger('price_slider_change', [ui.values[0], ui.values[1]]);

                    if(timer){
                        clearTimeout(timer); 
                    }
                    timer = setTimeout(function () {
                        var filters = $scope.data('filters') || {};
                        var prices = filters.prices || {};
                        prices.min_price = $scope.find('input#min_price').val();
                        prices.max_price = $scope.find('input#max_price').val();
                        filters.prices = prices;
                        $scope.data('filters', filters);
                        $scope.data('page', 1);
                        $scope.trigger('load');
                    }, 500);
                }
            });
        }

        init_price_filter();
        $scope.on('init_price_filter', init_price_filter);

        var els = $scope.find('.egrid-products-price-filter [type=submit]');
        if(els.length == 0){
            var widgetId = $scope.data('id');
            els = $('#egrid-products-filters-' + widgetId).find('.egrid-products-price-filter [type=submit]');
        }

        els.on('click', function () {
            var filters = $scope.data('filters') || {};
            var prices = filters.prices || {};
            prices.min_price = $scope.find('input#min_price').val();
            prices.max_price = $scope.find('input#max_price').val();
            filters.prices = prices;
            $scope.data('filters', filters);
            $scope.data('page', 1);
            $scope.trigger('load');

            return false;
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/egrid-products.default', WidgetEGridProductsPriceFilterHandler);
    });
})(jQuery);