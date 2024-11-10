<?php
$current_rating = '';
if (isset($_POST['filters'])) {
    $filters = json_decode(stripslashes($_POST['filters']), true);
    $current_rating = isset($filters['rating']) ? $filters['rating'] : '';
}
?>
    <div class="egrid-products-rating-filter widget woocommerce widget_rating_filter">
        <div class="widget-inner">
            <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
            <ul>
                <?php
                for ($rating = 5; $rating >= 1; $rating--) {
                    $count = egrid_get_filtered_product_count($rating);
                    if (empty($count)) {
                        continue;
                    }

                    $rating_html = wc_get_star_rating_html($rating);
                    $count_html = wp_kses(
                        apply_filters('woocommerce_rating_filter_count', "({$count})", $count, $rating),
                        array(
                            'em' => array(),
                            'span' => array(),
                            'strong' => array(),
                        )
                    );
                    ?>
                    <li class="<?php echo esc_attr('wc-layered-nav-rating'); ?>">
                        <a href="#<?php echo esc_attr($rating); ?>" class="<?php if ($current_rating == $rating) {
                            echo esc_attr('chosen');
                        } ?>" egrid-products-rating-filter>
								<span class="star-rating">
									<?php echo $rating_html; ?>
								</span>
                            <?php echo $count_html; ?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php

            ?>
        </div>
    </div>
<?php
?>