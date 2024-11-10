<?php
// Round values to nearest 10 by default.
$step = max(apply_filters('woocommerce_price_filter_widget_step', 10), 1);

// Find min and max price in current result set.
$prices = egrid_get_filtered_price();
$min_price = $prices->min_price;
$max_price = $prices->max_price;

// Check to see if we should add taxes to the prices if store are excl tax but display incl.
$tax_display_mode = get_option('woocommerce_tax_display_shop');

if (wc_tax_enabled() && !wc_prices_include_tax() && 'incl' === $tax_display_mode) {
    $tax_class = apply_filters('woocommerce_price_filter_widget_tax_class', ''); // Uses standard tax class.
    $tax_rates = WC_Tax::get_rates($tax_class);

    if ($tax_rates) {
        $min_price += WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($min_price, $tax_rates));
        $max_price += WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($max_price, $tax_rates));
    }
}

$min_price = apply_filters('woocommerce_price_filter_widget_min_amount', floor($min_price / $step) * $step);
$max_price = apply_filters('woocommerce_price_filter_widget_max_amount', ceil($max_price / $step) * $step);

$current_min_price = $min_price;
$current_max_price = $max_price;
if (isset($_POST['filters'])) {
    $filters = json_decode(stripslashes($_POST['filters']), true);
    $prices = isset($filters['prices']) ? $filters['prices'] : [];
    if(!empty($prices)){
        $current_min_price = isset($prices['min_price']) ? floor(floatval(wp_unslash($prices['min_price'])) / $step) * $step : $min_price;
        $current_max_price = isset($prices['max_price']) ? ceil(floatval(wp_unslash($prices['max_price'])) / $step) * $step : $max_price;
    }
}
?>
<div class="egrid-products-price-filter widget woocommerce widget_price_filter">
    <div class="widget-inner">
        <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
        <?php
        wc_get_template(
            'content-widget-price-filter.php',
            array(
                'form_action' => '',
                'step' => $step,
                'min_price' => $min_price,
                'max_price' => $max_price,
                'current_min_price' => $current_min_price,
                'current_max_price' => $current_max_price,
            )
        );
        ?>
    </div>
</div>