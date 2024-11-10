<?php
	$settings = $widget->get_settings_for_display();
	$columns = $widget->get_settings_for_display('columns', 4);
	if($widget->get_columns()){
		$columns = $widget->get_columns();
	}
	$paginate = $widget->get_settings_for_display('paginate', false);
	$paginate = filter_var($paginate, FILTER_VALIDATE_BOOLEAN);
	$show_result_count = $widget->get_settings_for_display('show_result_count', false);
	$show_result_count = filter_var($show_result_count, FILTER_VALIDATE_BOOLEAN);
	$show_orderby = $widget->get_settings_for_display('show_orderby', false);
	$show_orderby = filter_var($show_orderby, FILTER_VALIDATE_BOOLEAN);
	$enable_filter_popup = $widget->get_settings_for_display('enable_filter_popup', false);
	$enable_filter_popup = filter_var($enable_filter_popup, FILTER_VALIDATE_BOOLEAN);
	$show_featured_filter = $widget->get_settings_for_display('show_featured_filter', false);
	$show_featured_filter = filter_var($show_featured_filter, FILTER_VALIDATE_BOOLEAN);
	$pagination_type = $widget->get_settings_for_display('pagination_type', 'pagination');
	$type = $widget->get_settings_for_display('type', 'products');
	$products = $widget->get_query_results();
	$is_loadmore = $pagination_type == 'load_more' && isset($_POST['loadmore']) && filter_var($_POST['loadmore'], FILTER_VALIDATE_BOOLEAN);
?>

<div class="egrid-products egrid-products-default">
	<?php
		if ( $products && $products->ids ) {
			// Setup the loop.
			wc_setup_loop(
				array(
					'columns'      => $columns,
					'name'         => $type,
					'is_paginated' => $paginate,
					'total'        => $products->total,
					'total_pages'  => $products->total_pages,
					'per_page'     => $products->per_page,
					'current_page' => $products->current_page,
				)
			);

			$original_post = $GLOBALS['post'];

			if(!$is_loadmore){

				do_action( "woocommerce_shortcode_before_{$type}_loop", $settings );

				// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
				if ( $paginate ) {
					if($enable_filter_popup){
						add_action( 'woocommerce_before_shop_loop', [$widget, 'woocommerce_filter_popup_switch'], 10 );
					}
					if(!$show_result_count){
						remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
					}
					if(!$show_orderby){
						remove_action( 'woocommerce_before_shop_loop','woocommerce_catalog_ordering', 30);
					}
					if($show_featured_filter){
						add_action( 'woocommerce_before_shop_loop', [$widget, 'woocommerce_featured_filter'], 21 );
					}
					add_filter('woocommerce_catalog_orderby', [$widget, 'get_woocommerce_catalog_orderby_options']);
					add_filter('woocommerce_default_catalog_orderby', [$widget, 'get_woocommerce_default_catalog_orderby']);
					do_action( 'woocommerce_before_shop_loop' );
					remove_filter('woocommerce_catalog_orderby', [$widget, 'get_woocommerce_catalog_orderby_options']);
					remove_filter('woocommerce_default_catalog_orderby', [$widget, 'get_woocommerce_default_catalog_orderby']);
				}
			}

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				foreach ( $products->ids as $product_id ) {
					$GLOBALS['post'] = get_post( $product_id );
					setup_postdata( $GLOBALS['post'] );

					// Set custom product visibility when quering hidden products.
					add_action( 'woocommerce_product_is_visible', array( $widget, 'set_product_as_visible' ) );

					// Render product template.
					wc_get_template_part( 'content', 'product' );

					// Restore product visibility.
					remove_action( 'woocommerce_product_is_visible', array( $widget, 'set_product_as_visible' ) );
				}
			}

			?>
			<?php /*
				<li class="product">
					<div class="egrid-placeholder-item egrid-placeholder-image"></div>
					<div class="egrid-placeholder-item egrid-placeholder-category"></div>
					<div class="egrid-placeholder-item egrid-placeholder-title"></div>
					<div class="egrid-placeholder-item egrid-placeholder-price"></div>
				</li>
			*/ ?>
			<?php

			$GLOBALS['post'] = $original_post;
			woocommerce_product_loop_end();

			if(!$is_loadmore){

				// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
				if ( $paginate ) {
					if($pagination_type == 'pagination'){
						add_filter('woocommerce_pagination_args', [$widget, 'set_woocommerce_pagination_args']);
						do_action( 'woocommerce_after_shop_loop' );
						remove_filter('woocommerce_pagination_args', [$widget, 'set_woocommerce_pagination_args']);
					}
					elseif($pagination_type == 'load_more'){
						$next_page = $products->current_page + 1;
						if($next_page < $products->total_pages){
							?>
								<div class="woocommerce-loadmore">
									<button type="button" class="btn" egrid-products-loadmore data-total-pages="<?php echo esc_attr($products->total_pages); ?>" data-current-page="<?php echo esc_attr($products->current_page); ?>"><?php echo esc_html__('Load More', EGRID_TEXT_DOMAIN); ?></button>
								</div>
							<?php
						}
					}
				}

				do_action( "woocommerce_shortcode_after_{$type}_loop", $settings );
			}

			wp_reset_postdata();
			wc_reset_loop();
		}
		else{
			// Setup the loop.
			wc_setup_loop(
				array(
					'columns'      => $columns,
					'name'         => $type,
					'is_paginated' => $paginate,
					'total'        => $products->total,
					'total_pages'  => $products->total_pages,
					'per_page'     => $products->per_page,
					'current_page' => $products->current_page,
				)
			);

			$original_post = $GLOBALS['post'];

			if(!$is_loadmore){

				do_action( "woocommerce_shortcode_before_{$type}_loop", $settings );

				// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
				if ( $paginate ) {
					if($enable_filter_popup){
						add_action( 'woocommerce_before_shop_loop', [$widget, 'woocommerce_filter_popup_switch'], 10 );
					}
					if(!$show_result_count){
						remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
					}
					if($show_featured_filter){
						add_action( 'woocommerce_before_shop_loop', [$widget, 'woocommerce_featured_filter'], 21 );
					}
					add_filter('woocommerce_catalog_orderby', [$widget, 'get_woocommerce_catalog_orderby_options']);
					add_filter('woocommerce_default_catalog_orderby', [$widget, 'get_woocommerce_default_catalog_orderby']);
					do_action( 'woocommerce_before_shop_loop' );
					remove_filter('woocommerce_catalog_orderby', [$widget, 'get_woocommerce_catalog_orderby_options']);
					remove_filter('woocommerce_default_catalog_orderby', [$widget, 'get_woocommerce_default_catalog_orderby']);
				}
			}

			woocommerce_product_loop_start();
			echo esc_html__('Nothing Found!', EGRID_TEXT_DOMAIN);
			woocommerce_product_loop_end();

			if(!$is_loadmore){

				// Fire standard shop loop hooks when paginating results so we can show result counts and so on.
				if ( $paginate ) {
					if($pagination_type == 'pagination'){
						add_filter('woocommerce_pagination_args', [$widget, 'set_woocommerce_pagination_args']);
						do_action( 'woocommerce_after_shop_loop' );
						remove_filter('woocommerce_pagination_args', [$widget, 'set_woocommerce_pagination_args']);
					} elseif($pagination_type == 'load_more'){
						$next_page = $products->current_page + 1;
						if($next_page < $products->total_pages){
							?>
								<div class="woocommerce-loadmore">
									<button type="button" class="btn" egrid-products-loadmore data-total-pages="<?php echo esc_attr($products->total_pages); ?>" data-current-page="<?php echo esc_attr($products->current_page); ?>"><?php echo esc_html__('Load More', EGRID_TEXT_DOMAIN); ?></button>
								</div>
							<?php
						}
					}
				}

				do_action( "woocommerce_shortcode_after_{$type}_loop", $settings );
			}

			wp_reset_postdata();
			wc_reset_loop();
		}
	?>
</div>