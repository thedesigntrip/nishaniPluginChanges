<?php
    $chosen = false;
    $featured_status = $widget->get_featured_status();
    $title = '';
?>
<div class="egrid-products-featured-status-filter widget woocommerce widget_featured_status">
    <div class="widget-inner">
        <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
        <ul class="woocommerce-widget-featured-status-list">
            <?php
                $featured_statuses = [
                    '' => esc_html__('All Products', EGRID_TEXT_DOMAIN),
                    'featured' => esc_html__('Hot Items', EGRID_TEXT_DOMAIN),
                    'newarrival' => esc_html__('New Arrivals', EGRID_TEXT_DOMAIN),
                    'onsale' => esc_html__('On Sale', EGRID_TEXT_DOMAIN),
                ];
                foreach ( $featured_statuses as $status => $label ) {
                    $chosen = $status == $featured_status;
                    ?>
                        <li class="woocommerce-widget-featured-status-list__item <?php if ($chosen) {
                            echo esc_attr('woocommerce-widget-featured-status-list__item--chosen chosen');
                        } ?>">
                            <a rel="nofollow" href="#<?php echo esc_attr($status); ?>" class="<?php if ($chosen) {
                                echo esc_attr('chosen');
                            } ?>" egrid-products-featured-status-filter>
                                <?php echo esc_html($label); ?>
                            </a>
                        </li>
                    <?php
                }
            ?>
        </ul>
    </div>
</div>