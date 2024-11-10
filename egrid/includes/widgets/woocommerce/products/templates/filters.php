<?php

use EGrid\Widgets\Woocommerce\EGrid_Products;

$enable_filter = $widget->get_settings_for_display('enable_filter', 'no');
$enable_filter = $enable_filter == 'yes';
$filter_classes = apply_filters('egrid_products_filter_classes', [], $widget);
$enable_filter_popup = $widget->get_settings_for_display('enable_filter_popup', false);
$enable_filter_popup = filter_var($enable_filter_popup, FILTER_VALIDATE_BOOLEAN);
?>
<?php if ($enable_filter) { ?>
    <?php if($enable_filter_popup): ?>
        <div id="egrid-products-filters-modal-<?php echo esc_attr($widget->get_id()); ?>" class="egrid-modal">
            <div class="egrid-modal-header">
                <span class="egrid-modal-close">&times;</span>
                <h2 class="egrid-modal-title"><?php esc_html_e('Filters', EGRID_TEXT_DOMAIN); ?></h2>
            </div>
            <div class="egrid-modal-body">
                <div class="egrid-modal-content">
    <?php endif; ?>
                    <div id="egrid-products-filters-<?php echo esc_attr($widget->get_id()); ?>"
                         class="egrid-products-filters <?php echo esc_attr(implode(' ', $filter_classes)); ?>">
                        <?php
                        $filter_items = $widget->get_settings_for_display('filter_items', [
                            'enabled' => []
                        ]);
                        $enabled_filter_items = isset($filter_items['enabled']) ? $filter_items['enabled'] : [];
                        foreach ($enabled_filter_items as $enabled_filter_item) {
                            if ($enabled_filter_item == 'categories') {
                                $args = [
                                    'count' => $widget->get_settings_for_display('filter_by_categories_count', 'no'),
                                    'hierarchical' => $widget->get_settings_for_display('filter_by_categories_hierarchical', 'no'),
                                    'display_type' => $widget->get_settings_for_display('filter_by_categories_display_type', 'list'),
                                    'orderby' => $widget->get_settings_for_display('filter_by_categories_orderby', 'name'),
                                    'hide_empty' => $widget->get_settings_for_display('filter_by_categories_hide_empty', 'no'),
                                    'max_depth' => $widget->get_settings_for_display('filter_by_categories_max_depth', ''),
                                    'title' => $widget->get_settings_for_display('filter_by_categories_title', esc_html__('Filter by Category', EGRID_TEXT_DOMAIN)),
                                    'selected_categories' => $widget->get_cats(),
                                    'source' => $widget->get_settings_for_display('source', []),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_filter_by_categories_template_args', []));
                                egrid_get_template('categories.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } elseif ($enabled_filter_item == 'price') {
                                $args = [
                                    'title' => $widget->get_settings_for_display('filter_by_price_title', esc_html__('Filter by Price', EGRID_TEXT_DOMAIN)),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_filter_by_price_template_args', []));
                                egrid_get_template('price.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } elseif ($enabled_filter_item == 'rating') {
                                $args = [
                                    'title' => $widget->get_settings_for_display('filter_by_rating_title', esc_html__('Filter by Rating', EGRID_TEXT_DOMAIN)),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_filter_by_rating_template_args', []));
                                egrid_get_template('rating.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } elseif ($enabled_filter_item == 'product_search') {
                                $args = [
                                    'title' => $widget->get_settings_for_display('product_search_title', ''),
                                    'search' => $widget->get_search(),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_product_search_template_args', []));
                                egrid_get_template('product_search.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } elseif ($enabled_filter_item == 'products') {
                                $args = [
                                    'title' => $widget->get_settings_for_display('products_title', esc_html__('Products', EGRID_TEXT_DOMAIN)),
                                    'number' => $widget->get_settings_for_display('products_number', 5),
                                    'show' => $widget->get_settings_for_display('products_show', ''),
                                    'orderby' => $widget->get_settings_for_display('products_orderby', 'date'),
                                    'order' => $widget->get_settings_for_display('products_order', 'desc'),
                                    'show_hidden' => $widget->get_settings_for_display('products_show_hidden', 'no'),
                                    'hide_free' => $widget->get_settings_for_display('products_hide_free', 'no'),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_products_template_args', []));
                                egrid_get_template('products.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } elseif ($enabled_filter_item == 'stock_status') {
                                $args = [
                                    'title' => $widget->get_settings_for_display('filter_by_stock_status_title', ''),
                                    'stock_status' => $widget->get_stock_status(),
                                ];
                                $args = array_merge($args, apply_filters('egrid_products_filter_by_stock_status_template_args', []));
                                egrid_get_template('stock-status.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                            } else {
                                if (taxonomy_exists(wc_attribute_taxonomy_name($enabled_filter_item))) {
                                    $attribute_label = wc_attribute_label($enabled_filter_item);
                                    $args = [
                                        'display_type' => $widget->get_settings_for_display("filter_by_{$enabled_filter_item}_display_type", 'list'),
                                        'query_type' => $widget->get_settings_for_display("filter_by_{$enabled_filter_item}_query_type", 'and'),
                                        'title' => $widget->get_settings_for_display("filter_by_{$enabled_filter_item}_title", sprintf(esc_html__('Filter by %s', EGRID_TEXT_DOMAIN), $attribute_label)),
                                        'attribute_name' => $enabled_filter_item,
                                        'attribute_label' => $attribute_label,
                                    ];
                                    $args = array_merge($args, apply_filters('egrid_products_filter_by_attribute_template_args', []));
                                    $args = array_merge($args, apply_filters("egrid_products_filter_by_attribute_{$enabled_filter_item}_template_args", []));
                                    egrid_get_template('attribute.php', $args, EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
                                }
                            }
                        }
                        ?>
                    </div>
    <?php if($enable_filter_popup): ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php } ?>