<?php
$count        = filter_var($count, FILTER_VALIDATE_BOOLEAN);
$hierarchical = filter_var($hierarchical, FILTER_VALIDATE_BOOLEAN);
$hide_empty   = filter_var($hide_empty, FILTER_VALIDATE_BOOLEAN);
$max_depth    = absint($max_depth);
?>

<div class="egrid-products-categories-filter widget woocommerce">
    <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
    <?php
    if ($display_type == 'dropdown') {
        include_once EGRID_PATH . 'includes/walkers/class-egrid-product-cat-dropdown-walker.php';

        $args = array(
            'walker'             => new EGRID_Product_Cat_Dropdown_Walker(),
            'show_count'         => $count,
            'hierarchical'       => $hierarchical,
            'show_uncategorized' => 0,
            'show_option_none'   => esc_html__('Select a category', EGRID_TEXT_DOMAIN),
            'selected'           => isset($_REQUEST['product_cat']) ? sanitize_text_field($_REQUEST['product_cat']) : '',
            'hide_empty'         => $hide_empty,
            'depth'              => $max_depth,
            'multiple'           => false,
            'name'               => 'product_cat',
            );
        if ('order' === $orderby) {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'order';
        }
//            add_filter('wp_dropdown_cats', [$widget, 'customize_wp_dropdown_cats'], 10, 2);
        wc_product_dropdown_categories($args);
//            remove_filter('wp_dropdown_cats', [$widget, 'customize_wp_dropdown_cats'], 10, 2);
    } elseif ($display_type == 'list') {
        include_once EGRID_PATH . 'includes/walkers/class-egrid-product-cat-list-walker.php';

        $args = array(
            'walker'                     => new EGRID_Product_Cat_List_Walker(),
            'show_count'                 => $count,
            'hierarchical'               => $hierarchical,
            'taxonomy'                   => 'product_cat',
            'hide_empty'                 => $hide_empty,
            'menu_order'                 => false,
            'title_li'                   => '',
            'pad_counts'                 => 1,
            'show_option_none'           => __('No product categories exist.', EGRID_TEXT_DOMAIN),
            'current_category'           => $selected_categories,
            'current_category_ancestors' => [],
            'depth'                      => $max_depth,
            'max_depth'                  => $max_depth,
        );
        if ('order' === $orderby) {
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'order';
        }
        if(!empty($source)){
            $args['slug'] = $source;
        }
        ?>
        <ul class="product-categories">
            <?php wp_list_categories($args); ?>
        </ul>
        <?php
    }
    ?>
</div>
