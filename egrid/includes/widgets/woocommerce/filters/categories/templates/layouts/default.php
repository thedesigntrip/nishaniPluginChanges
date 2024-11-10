<?php
	$count = $widget->get_settings_for_display('count', '');
	$count = filter_var($count, FILTER_VALIDATE_BOOLEAN);
    $hierarchical = $widget->get_settings_for_display('hierarchical', '');
    $hierarchical = filter_var($hierarchical, FILTER_VALIDATE_BOOLEAN);
    $display_type = $widget->get_settings_for_display('display_type', 'list');
    $orderby = $widget->get_settings_for_display('orderby', 'name');
    $hide_empty = $widget->get_settings_for_display('hide_empty', '');
    $hide_empty = filter_var($hide_empty, FILTER_VALIDATE_BOOLEAN);
    $thumbnail = $widget->get_settings_for_display('thumbnail', '');
    $thumbnail = filter_var($thumbnail, FILTER_VALIDATE_BOOLEAN);
    $show_all = $widget->get_settings_for_display('show_all', '');
    $show_all = filter_var($show_all, FILTER_VALIDATE_BOOLEAN);
    $all_text = $widget->get_settings_for_display('all_text', esc_html__('All', EGRID_TEXT_DOMAIN));
    $max_depth = $widget->get_settings_for_display('max_depth', 0);
    $title = $widget->get_settings_for_display('title', '');
    $source = $widget->get_settings_for_display('source', []);
    $grid = $widget->get_settings_for_display('grid', '');
    $selected_categories = $widget->get_cats();
    $multiple = $widget->get_settings_for_display('multiple', '');
    $multiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);

    $show_option_all = '';
    if($show_all){
        if($thumbnail){
            $all_image = $widget->get_settings_for_display('all_image', '');
            $all_image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( ['all_image' => $all_image], 'gallery_thumbnail', 'all_image' );
            $show_option_all .= '<span class="thumbnail">';
            $show_option_all .= $all_image_html;
            $show_option_all .= '</span>';
        }
        $show_option_all .= $all_text;
    }
?>

<div class="egrid-products-categories-filter egrid-products-categories-filter-default widget woocommerce">
	<?php if(!empty($title)): ?>
	    <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
	<?php endif; ?>
    <?php
    if ($display_type == 'dropdown') {
        include_once EGRID_PATH . 'includes/walkers/class-egrid-product-cat-dropdown-walker.php';

        $args = array(
            'walker'             => new EGRID_Product_Cat_Dropdown_Walker(),
            'show_count'         => $count,
            'hierarchical'       => $hierarchical,
            'show_uncategorized' => 0,
            'show_option_none'   => 0,
            // 'selected'           => $selected_categories,
            'hide_empty'         => $hide_empty,
            'depth'              => $max_depth,
            'multiple'           => $multiple,
            'name'               => 'product_cat[]',
        );
        if ('order' === $orderby) {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'order';
        }
        wc_product_dropdown_categories($args);
    } elseif ($display_type == 'list') {
        include_once EGRID_PATH . 'includes/walkers/class-egrid-product-cat-list-walker.php';

        $args = array(
            'walker'                     => new EGRID_Product_Cat_List_Walker(),
            'show_count'                 => $count,
            'show_thumbnail'             => $thumbnail,
            'hierarchical'               => $hierarchical,
            'taxonomy'                   => 'product_cat',
            'hide_empty'                 => $hide_empty,
            'menu_order'                 => false,
            'title_li'                   => '',
            'pad_counts'                 => 1,
            'show_option_none'           => __('No product categories exist.', EGRID_TEXT_DOMAIN),
            'show_option_all'           => $show_option_all,
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