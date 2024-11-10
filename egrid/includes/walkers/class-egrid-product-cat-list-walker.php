<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'EGRID_Product_Cat_List_Walker', false ) ) {
	return;
}

/**
 * Product cat list walker class.
 */
class EGRID_Product_Cat_List_Walker extends Walker {

	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type = 'product_cat';

	/**
	 * DB fields to use.
	 *
	 * @var array
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$cat_id = intval( $cat->term_id );

		$output .= '<li class="cat-item cat-item-' . $cat_id;

		$current_category = $args['current_category'];
		$chosen = false;
		if(is_array($current_category)){
			foreach ($current_category as $key => $value) {
				if(is_numeric($value)){
					if ( $value === $cat_id ) {
						$output .= ' current-cat';
						$chosen = true;
					}
				}
				else{
					if ( $value === $cat->slug ) {
						$output .= ' current-cat';
						$chosen = true;
					}
				}
			}
		}
		else{
			if ( $current_category === $cat_id ) {
				$output .= ' current-cat';
				$chosen = true;
			}
		}

		if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
			$output .= ' cat-parent';
		}

		if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( $cat_id, $args['current_category_ancestors'], true ) ) {
			$output .= ' current-cat-parent';
		}

		$output .= '"';
		$output .= ' data-value="' . $cat->slug . '"';
		$output .= '>';
		$output .= '<a href="#' . $cat->slug . '"';
		if($chosen){
			$output .= ' class="chosen"';
		}
		$output .= ' egrid-products-category-filter>';
			if ( isset($args['show_thumbnail']) && $args['show_thumbnail'] ) {
				ob_start();
	            $small_thumbnail_size = apply_filters( 'egrid_filter_thumbnail_size', 'woocommerce_thumbnail' );
	            $dimensions = wc_get_image_size($small_thumbnail_size);
	            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);

	            if ($thumbnail_id) {
	                $image = wp_get_attachment_image_src($thumbnail_id, $small_thumbnail_size);
	                $image = $image[0];
	                $image_srcset = function_exists('wp_get_attachment_image_srcset') ? wp_get_attachment_image_srcset($thumbnail_id, $small_thumbnail_size) : false;
	                $image_sizes = function_exists('wp_get_attachment_image_sizes') ? wp_get_attachment_image_sizes($thumbnail_id, $small_thumbnail_size) : false;
	            } else {
	                $image = wc_placeholder_img_src();
	                $image_srcset = false;
	                $image_sizes = false;
	            }

	            if ($image) {
	                // Prevent esc_url from breaking spaces in urls for image embeds.
	                // Ref: https://core.trac.wordpress.org/ticket/23605.
	                $image = str_replace(' ', '%20', $image);

	                // Add responsive image markup if available.
	                if ($image_srcset && $image_sizes) {
	                    ?>
	                    <img src="<?php echo esc_url($image); ?>"
	                         alt="<?php echo esc_attr($cat->name); ?>"
	                         width="<?php echo esc_attr($dimensions['width']) ?>"
	                         height="<?php echo esc_attr($dimensions['height']) ?>"
	                         srcset="<?php echo esc_attr($image_srcset) ?>"
	                         sizes="<?php echo esc_attr($image_sizes) ?>">
	                    <?php
	                } else {
	                    ?>
	                    <img src="<?php echo esc_url($image); ?>"
	                         alt="<?php echo esc_attr($cat->name); ?>"
	                         width="<?php echo esc_attr($dimensions['width']) ?>"
	                         height="<?php echo esc_attr($dimensions['height']) ?>">
	                    <?php
	                }
	            }
	            $thumbnail = ob_get_clean();
				$output .= ' <span class="thumbnail">' . $thumbnail . '</span>';
			}
		
		$output .= $cat->name;

		if ( $args['show_count'] ) {
			$output .= ' <span class="count">(' . $cat->count . ')</span>';
		}

		$output .= '</a>';
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $cat    Category.
	 * @param int    $depth  Depth of category. Not used.
	 * @param array  $args   Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max.
	 * depth and no ignore elements under that depth. It is possible to set the.
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
