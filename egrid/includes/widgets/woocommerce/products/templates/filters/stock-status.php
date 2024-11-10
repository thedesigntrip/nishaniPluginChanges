<?php
    $chosen = false;
?>
<div class="egrid-products-stock-status-filter widget woocommerce widget_stock_status">
    <div class="widget-inner">
        <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
        <ul class="woocommerce-widget-stock-status-list">
            <?php
                $stock_statuses = wc_get_product_stock_status_options();
                foreach ( $stock_statuses as $status => $label ) {
                    $chosen = $status == $stock_status;
                    ?>
                        <li class="woocommerce-widget-stock-status-list__item <?php if ($chosen) {
                            echo esc_attr('woocommerce-widget-stock-status-list__item--chosen chosen');
                        } ?>">
                            <a rel="nofollow" href="#<?php echo esc_attr($status); ?>" class="<?php if ($chosen) {
                                echo esc_attr('chosen');
                            } ?>" egrid-products-stock-status-filter>
                                <?php echo esc_html($label); ?>
                            </a>
                        </li>
                    <?php
                }
            ?>
        </ul>
    </div>
</div>