// update the function inside of product-customizer.php (line 616) to include the localized jewelry_items data

/**
     * Product data management
     */
    private function localize_product_data($product_id)
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return;
        }

        $product_data = $this->get_product_data($product);

        // Fetch jewelry items to pass to the script
        $jewelry_items = get_posts([
            'post_type' => 'jewelry_inventory',
            'numberposts' => -1,
        ]);

        wp_localize_script('custom-three-renderer', 'jewelryItems', $jewelry_items);
        wp_localize_script('custom-three-renderer', 'productData', $product_data);
        wp_localize_script('custom-three-renderer', 'pc_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pc_add_to_cart')
        ]);
    }
