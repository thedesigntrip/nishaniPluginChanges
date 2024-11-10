<?php
	$show_hidden        = filter_var($show_hidden, FILTER_VALIDATE_BOOLEAN);
	$hide_free        = filter_var($hide_free, FILTER_VALIDATE_BOOLEAN);
	$product_visibility_term_ids = wc_get_product_visibility_term_ids();

	$query_args = array(
		'posts_per_page' => $number,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'no_found_rows'  => 1,
		'order'          => $order,
		'meta_query'     => array(),
		'tax_query'      => array(
			'relation' => 'AND',
		),
	); // WPCS: slow query ok.

	if ( empty( $instance['show_hidden'] ) ) {
		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
			'operator' => 'NOT IN',
		);
		$query_args['post_parent'] = 0;
	}

	if ( ! empty( $instance['hide_free'] ) ) {
		$query_args['meta_query'][] = array(
			'key'     => '_price',
			'value'   => 0,
			'compare' => '>',
			'type'    => 'DECIMAL',
		);
	}

	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$query_args['tax_query'][] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['outofstock'],
				'operator' => 'NOT IN',
			),
		); // WPCS: slow query ok.
	}

	switch ( $show ) {
		case 'featured':
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['featured'],
			);
			break;
		case 'onsale':
			$product_ids_on_sale    = wc_get_product_ids_on_sale();
			$product_ids_on_sale[]  = 0;
			$query_args['post__in'] = $product_ids_on_sale;
			break;
	}

	switch ( $orderby ) {
		case 'menu_order':
			$query_args['orderby'] = 'menu_order';
			break;
		case 'price':
			$query_args['meta_key'] = '_price'; // WPCS: slow query ok.
			$query_args['orderby']  = 'meta_value_num';
			break;
		case 'rand':
			$query_args['orderby'] = 'rand';
			break;
		case 'sales':
			$query_args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
			$query_args['orderby']  = 'meta_value_num';
			break;
		default:
			$query_args['orderby'] = 'date';
	}

	$products = new WP_Query( $query_args );

	if ( $products && $products->have_posts() ) {
		?>
		<div class="egrid-products-products-list widget woocommerce widget_products">
			<div class="widget-inner">
	        	<h2 class="widgettitle"><?php echo esc_html($title); ?></h2>
				<ul class="product_list_widget">
					<?php

					$template_args = array(
						// 'widget_id'   => isset( $args['widget_id'] ) ? $args['widget_id'] : $this->widget_id,
						'show_rating' => true,
					);

					while ( $products->have_posts() ) {
						$products->the_post();
						wc_get_template( 'content-widget-product.php', $template_args );
					}

					?>
				</ul>
			</div>
		</div>
		<?php
	}

	wp_reset_postdata();
?>