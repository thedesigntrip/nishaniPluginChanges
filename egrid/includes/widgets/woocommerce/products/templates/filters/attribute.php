<?php
$attribute_label = wc_attribute_label($attribute_name);
$taxonomy = wc_attribute_taxonomy_name($attribute_name);
$terms = get_terms($taxonomy, array('hide_empty' => '1'));
$multiple = 'or' === $query_type;

$attributes = [];
if (isset($_POST['filters'])) {
    $filters = json_decode(stripslashes($_POST['filters']), true);
    $attributes = isset($filters['attributes']) ? $filters['attributes'] : [];
}
?>

<?php if (!is_wp_error($terms) && count($terms) > 0) { ?>
    <div class="egrid-products-attribute-filter egrid-products-<?php echo esc_attr($attribute_name); ?>-filter widget woocommerce widget_layered_nav woocommerce-widget-layered-nav">
        <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
        <?php
        $term_counts = egrid_get_filtered_term_product_counts(wp_list_pluck($terms, 'term_id'), $taxonomy, $query_type);
        if ($display_type == 'list') {
            ?>
            <ul class="woocommerce-widget-layered-nav-list">
                <?php
                foreach ($terms as $term) {
                    $count = isset($term_counts[$term->term_id]) ? $term_counts[$term->term_id] : 0;
                    if ($count <= 0) {
                        continue;
                    }
                    $chosen = isset($attributes[$attribute_name]) && $attributes[$attribute_name] == $term->slug;
                    ?>
                    <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term egrid-filter-<?php echo esc_attr($attribute_name); ?> <?php if ($chosen) {
                        echo esc_attr('woocommerce-widget-layered-nav-list__item--chosen chosen');
                    } ?>">
                        <a rel="nofollow" href="#<?php echo esc_attr($term->slug); ?>" class="<?php if ($chosen) {
                            echo esc_attr('chosen');
                        } ?>" egrid-products-attribute-filter="<?php echo esc_attr($attribute_name); ?>">
                            <?php 
                                do_action('egrid_attribute_name_before', $attribute_name, $term);
                                echo apply_filters('egrid_attribute_name', $term->name); 
                                do_action('egrid_attribute_name_after',$attribute_name, $term);
                            ?>
                            <span class="count"><?php echo apply_filters('egrid_count_html', $count); ?></span>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        } else if ($display_type == 'dropdown') {
            ?>
            <div class="woocommerce-widget-layered-nav-dropdown">
                <select class="egrid-products-attribute-dropdown woocommerce-widget-layered-nav-dropdown dropdown_layered_nav_<?php echo esc_attr($attribute_name); ?>" <?php if ($multiple) {
                    echo esc_attr('multiple="multiple"');
                } ?> egrid-products-attribute-filter="<?php echo esc_attr($attribute_name); ?>">
                    <option value=""><?php echo esc_html($attribute_label); ?></option>
                    <?php
                    foreach ($terms as $term) {
                        $count = isset($term_counts[$term->term_id]) ? $term_counts[$term->term_id] : 0;
                        if ($count <= 0) {
                            continue;
                        }
                        $chosen = isset($attributes[$attribute_name]) && $attributes[$attribute_name] == $term->slug;

                        ?>
                        <option value="<?php echo esc_attr(urldecode($term->slug)); ?>" <?php if ($chosen) {
                            echo esc_attr('selected');
                        } ?>><?php 
                            do_action('egrid_attribute_name_dropdown_before', $term);
                            echo apply_filters('egrid_attribute_dropdown_name', $term->name); 
                            do_action('egrid_attribute_name_dropdown_after', $term);
                            echo apply_filters('egrid_count_dropdown_html', $count);
                        ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }
        ?>
    </div>
<?php } ?>