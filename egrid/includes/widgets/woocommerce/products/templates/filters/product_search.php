<div class="egrid-products-product-search widget woocommerce widget_product_search">
    <div class="widget-inner">
        <h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
        <?php
        set_query_var('s', $search);
        get_product_search_form();
        ?>
    </div>
</div>