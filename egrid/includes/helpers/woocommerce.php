<?php
	if(!function_exists('egrid_get_filtered_price')){
		function egrid_get_filtered_price() {
			global $wpdb;

			$tax_query  = egrid_parse_tax_query_filter();
			$meta_query = egrid_parse_meta_query_filter();
			$meta_query = new WP_Meta_Query( $meta_query );
			$tax_query  = new WP_Tax_Query( $tax_query );

			$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql = "
				SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
				FROM {$wpdb->wc_product_meta_lookup}
				WHERE product_id IN (
					SELECT ID FROM {$wpdb->posts}
					" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
					WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish'
					" . $tax_query_sql['where'] . $meta_query_sql['where'] . '
				)';

			// echo '<pre>';
			// var_dump($sql);
			// echo '</pre>';

			$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

			return $wpdb->get_row( $sql );
		}
	}

	if(!function_exists('egrid_get_filtered_product_count')){
		function egrid_get_filtered_product_count( $rating ) {
			global $wpdb;

			$tax_query  = [];
			$meta_query = [];

			// Set new rating filter.
			$product_visibility_terms = wc_get_product_visibility_term_ids();
			$tax_query[]              = array(
				'taxonomy'      => 'product_visibility',
				'field'         => 'term_taxonomy_id',
				'terms'         => $product_visibility_terms[ 'rated-' . $rating ],
				'operator'      => 'IN',
				'rating_filter' => true,
			);

			$meta_query     = new WP_Meta_Query( $meta_query );
			$tax_query      = new WP_Tax_Query( $tax_query );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql  = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) FROM {$wpdb->posts} ";
			$sql .= $tax_query_sql['join'] . $meta_query_sql['join'];
			$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' ";
			$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

			return absint( $wpdb->get_var( $sql ) );
		}
	}

	if(!function_exists('egrid_get_filtered_term_product_counts')){
		function egrid_get_filtered_term_product_counts($term_ids, $taxonomy, $query_type){
			global $wpdb;

			$use_lookup_table = 'yes' === get_option( 'woocommerce_attribute_lookup_enabled' );

			$tax_query  = egrid_parse_tax_query_filter();
			$meta_query = egrid_parse_meta_query_filter();

			$meta_query = new \WP_Meta_Query( $meta_query );
			$tax_query  = new \WP_Tax_Query( $tax_query );

			if ( $use_lookup_table ) {
				$query = egrid_get_product_counts_query_using_lookup_table( $tax_query, $meta_query, $taxonomy, $term_ids );
			} else {
				$query = egrid_get_product_counts_query_not_using_lookup_table( $tax_query, $meta_query, $term_ids );
			}

			$query     = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
			$query_sql = implode( ' ', $query );

			$results                      = $wpdb->get_results( $query_sql, ARRAY_A );
			$counts                       = array_map( 'absint', wp_list_pluck( $results, 'term_count', 'term_count_id' ) );
			return $counts;
		}
	}

	if(!function_exists('egrid_get_product_counts_query_using_lookup_table')){
		function egrid_get_product_counts_query_using_lookup_table($tax_query, $meta_query, $taxonomy, $term_ids){
			global $wpdb;

			$lookup_table_name = $wpdb->prefix . 'wc_product_attributes_lookup';

			$meta_query_sql    = $meta_query->get_sql( 'post', $lookup_table_name, 'product_or_parent_id' );
			$tax_query_sql     = $tax_query->get_sql( $lookup_table_name, 'product_or_parent_id' );
			$hide_out_of_stock = 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' );
			$in_stock_clause   = $hide_out_of_stock ? ' AND in_stock = 1' : '';

			$query['select'] = 'SELECT COUNT(DISTINCT product_or_parent_id) as term_count, term_id as term_count_id';
			$query['from']   = "FROM {$lookup_table_name}";
			$query['join']   = "
				{$tax_query_sql['join']} {$meta_query_sql['join']}
				INNER JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$lookup_table_name}.product_or_parent_id";

			$encoded_taxonomy = sanitize_title( $taxonomy );
			$term_ids_sql     = '(' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';
			$query['where']   = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'
				{$tax_query_sql['where']} {$meta_query_sql['where']}
				AND {$lookup_table_name}.taxonomy='{$encoded_taxonomy}'
				AND {$lookup_table_name}.term_id IN $term_ids_sql
				{$in_stock_clause}";

			if ( ! empty( $term_ids ) ) {
				$attributes_to_filter_by = \WC_Query::get_layered_nav_chosen_attributes();

				if ( ! empty( $attributes_to_filter_by ) ) {
					$and_term_ids = array();

					foreach ( $attributes_to_filter_by as $taxonomy => $data ) {
						if ( 'and' !== $data['query_type'] ) {
							continue;
						}
						$all_terms             = get_terms( $taxonomy, array( 'hide_empty' => false ) );
						$term_ids_by_slug      = wp_list_pluck( $all_terms, 'term_id', 'slug' );
						$term_ids_to_filter_by = array_values( array_intersect_key( $term_ids_by_slug, array_flip( $data['terms'] ) ) );
						$and_term_ids          = array_merge( $and_term_ids, $term_ids_to_filter_by );
					}

					if ( ! empty( $and_term_ids ) ) {
						$terms_count   = count( $and_term_ids );
						$term_ids_list = '(' . join( ',', $and_term_ids ) . ')';
						// The extra derived table ("SELECT product_or_parent_id FROM") is needed for performance
						// (causes the filtering subquery to be executed only once).
						$query['where'] .= "
							AND product_or_parent_id IN ( SELECT product_or_parent_id FROM (
								SELECT product_or_parent_id
								FROM {$lookup_table_name} lt
								WHERE is_variation_attribute=0
								{$in_stock_clause}
								AND term_id in {$term_ids_list}
								GROUP BY product_id
								HAVING COUNT(product_id)={$terms_count}
								UNION
								SELECT product_or_parent_id
								FROM {$lookup_table_name} lt
								WHERE is_variation_attribute=1
								{$in_stock_clause}
								AND term_id in {$term_ids_list}
							) temp )";
					}
				} else {
					$query['where'] .= $in_stock_clause;
				}
			} elseif ( $hide_out_of_stock ) {
				$query['where'] .= " AND {$lookup_table_name}.in_stock=1";
			}

			// $search_query_sql = \WC_Query::get_main_search_query_sql();
			// if ( $search_query_sql ) {
			// 	$query['where'] .= ' AND ' . $search_query_sql;
			// }

			$query['group_by'] = 'GROUP BY terms.term_id';
			$query['group_by'] = "GROUP BY {$lookup_table_name}.term_id";

			return $query;
		}
	}

	if(!function_exists('egrid_get_product_counts_query_not_using_lookup_table')){
		function egrid_get_product_counts_query_not_using_lookup_table( $tax_query, $meta_query, $term_ids ) {
			global $wpdb;

			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			// Generate query.
			$query           = array();
			$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) AS term_count, terms.term_id AS term_count_id";
			$query['from']   = "FROM {$wpdb->posts}";
			$query['join']   = "
				INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
				INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
				INNER JOIN {$wpdb->terms} AS terms USING( term_id )
				" . $tax_query_sql['join'] . $meta_query_sql['join'];

			$term_ids_sql   = '(' . implode( ',', array_map( 'absint', $term_ids ) ) . ')';
			$query['where'] = "
				WHERE {$wpdb->posts}.post_type IN ( 'product' )
				AND {$wpdb->posts}.post_status = 'publish'
				{$tax_query_sql['where']} {$meta_query_sql['where']}
				AND terms.term_id IN $term_ids_sql";

			// $search_query_sql = \WC_Query::get_main_search_query_sql();
			// if ( $search_query_sql ) {
			// 	$query['where'] .= ' AND ' . $search_query_sql;
			// }

			$query['group_by'] = 'GROUP BY terms.term_id';

			return $query;
		}
	}

	if(!function_exists('egrid_parse_tax_query_filter')){
		function egrid_parse_tax_query_filter(){
			$tax_query = [];
			if(isset($_POST['filters'])){
				$filters = json_decode(stripslashes($_POST['filters']), true);

				if(isset($filters['attributes'])){
					if ( ! empty( $filters['attributes'] ) ){
						foreach ($filters['attributes'] as $taxonomy_name => $terms) {
							$taxonomy_name = strstr( $taxonomy_name, 'pa_' ) ? sanitize_title( $taxonomy_name ) : 'pa_' . sanitize_title( $taxonomy_name );
							$field    = 'slug';

							// If no terms were specified get all products that are in the attribute taxonomy.
							if ( ! $terms ) {
								$terms = get_terms(
									array(
										'taxonomy' => $taxonomy_name,
										'fields'   => 'ids',
									)
								);
								$field = 'term_id';
							}

							$terms_operator = !is_array($terms) ? 'IN' : '=';
							$tax_query[] = array(
								'taxonomy' => $taxonomy_name,
								'terms'    => $terms,
								'field'    => $field,
								'operator' => $terms_operator,
							);
						}
					}
				}

				if(isset($filters['categories'])){
					if ( ! empty( $filters['categories'] ) ) {
						$filters['categories'] = array_map( 'sanitize_title', $filters['categories'] );
						$cat_operator = '=';
						if(is_array($filters['categories'])){
							$cat_operator = 'IN';
						}
						$field      = 'slug';

						$tax_query[] = array(
							'taxonomy'         => 'product_cat',
							'terms'            => $filters['categories'],
							'field'            => $field,
							'operator'         => $cat_operator,

							/*
							 * When cat_operator is AND, the children categories should be excluded,
							 * as only products belonging to all the children categories would be selected.
							 */
							'include_children' => false,
						);
					}
				}
			}

			return $tax_query;
		}
	}

	if(!function_exists('egrid_parse_meta_query_filter')){
		function egrid_parse_meta_query_filter(){
			$meta_query = [];
			if(isset($_POST['filters'])){
				$filters = json_decode(stripslashes($_POST['filters']), true);

				if(isset($filters['rating']) && !empty($filters['rating'])){
					$meta_query[] = array(
						'key' => '_wc_average_rating',
						'value' => floatval($filters['rating']),
						'compare' => '=',
						'type' => 'NUMERIC',
					);
				}
			}

			return $meta_query;
		}
	}
?>