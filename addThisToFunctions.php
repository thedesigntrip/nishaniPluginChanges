// add this to functions.php of the theme luxria
// this code is to create an admin interface for jewelry inventory and create custom post type for the same

function create_jewelry_inventory_post_type() {
    register_post_type('jewelry_inventory',
        array(
            'labels' => array(
                'name' => __('Jewelry Inventory'),
                'singular_name' => __('Inventory Item'),
                'add_new' => __('Add New Item'),
                'add_new_item' => __('Add New Inventory Item'),
                'edit_item' => __('Edit Inventory Item'),
                'new_item' => __('New Inventory Item'),
                'view_item' => __('View Inventory Item'),
                'search_items' => __('Search Inventory'),
                'not_found' => __('No inventory items found'),
                'not_found_in_trash' => __('No inventory items found in trash')
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-cart',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'has_archive' => true,
            'rewrite' => array('slug' => 'jewelry-inventory'),
            'show_in_rest' => true,
            'rest_base' => 'jewelry',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        )
    );

    // Register taxonomy for jewelry categories
    register_taxonomy(
        'jewelry_category',
        'jewelry_inventory',
        array(
            'labels' => array(
                'name' => __('Categories'),
                'singular_name' => __('Category'),
                'search_items' => __('Search Categories'),
                'all_items' => __('All Categories'),
                'parent_item' => __('Parent Category'),
                'parent_item_colon' => __('Parent Category:'),
                'edit_item' => __('Edit Category'),
                'update_item' => __('Update Category'),
                'add_new_item' => __('Add New Category'),
                'new_item_name' => __('New Category Name'),
                'menu_name' => __('Categories')
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'jewelry-category'),
            'show_in_rest' => true,
            'rest_base' => 'jewelry-categories',
        )
    );
}
add_action('init', 'create_jewelry_inventory_post_type');

// Add meta boxes for jewelry details
function add_jewelry_meta_boxes() {
    add_meta_box(
        'jewelry_details',
        'Jewelry Details',
        'render_jewelry_details_meta_box',
        'jewelry_inventory',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_jewelry_meta_boxes');

// Render the meta box content
function render_jewelry_details_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('jewelry_details_meta_box', 'jewelry_details_nonce');

    // Get existing values
    $item_id = get_post_meta($post->ID, 'item_id', true);
    $price = get_post_meta($post->ID, 'price', true);
    $gold_stock = get_post_meta($post->ID, 'gold_stock', true);
    $silver_stock = get_post_meta($post->ID, 'silver_stock', true);
    $gold_item_code = get_post_meta($post->ID, 'gold_item_code', true);
    $silver_item_code = get_post_meta($post->ID, 'silver_item_code', true);
    ?>
    <style>
        .jewelry-field {
            margin-bottom: 15px;
        }
        .jewelry-field label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        .jewelry-field input {
            width: 200px;
        }
    </style>

    <div class="jewelry-field">
        <label for="item_id">Item ID:</label>
        <input type="text" id="item_id" name="item_id" value="<?php echo esc_attr($item_id); ?>">
    </div>

    <div class="jewelry-field">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo esc_attr($price); ?>" step="0.01">
    </div>

    <div class="jewelry-field">
        <label for="gold_stock">Gold Stock:</label>
        <input type="number" id="gold_stock" name="gold_stock" value="<?php echo esc_attr($gold_stock); ?>">
    </div>

    <div class="jewelry-field">
        <label for="silver_stock">Silver Stock:</label>
        <input type="number" id="silver_stock" name="silver_stock" value="<?php echo esc_attr($silver_stock); ?>">
    </div>

    <div class="jewelry-field">
        <label for="gold_item_code">Gold Item Code:</label>
        <input type="text" id="gold_item_code" name="gold_item_code" value="<?php echo esc_attr($gold_item_code); ?>">
    </div>

    <div class="jewelry-field">
        <label for="silver_item_code">Silver Item Code:</label>
        <input type="text" id="silver_item_code" name="silver_item_code" value="<?php echo esc_attr($silver_item_code); ?>">
    </div>
    <?php
}

// Save meta box data
function save_jewelry_details($post_id) {
    // Check if nonce is set
    if (!isset($_POST['jewelry_details_nonce'])) {
        return;
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['jewelry_details_nonce'], 'jewelry_details_meta_box')) {
        return;
    }

    // If this is autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    $fields = array(
        'item_id',
        'price',
        'gold_stock',
        'silver_stock',
        'gold_item_code',
        'silver_item_code'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_jewelry_inventory', 'save_jewelry_details');

// Add custom columns to the jewelry inventory list
function add_jewelry_inventory_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Name');
    $new_columns['item_id'] = __('Item ID');
    $new_columns['price'] = __('Price');
    $new_columns['gold_stock'] = __('Gold Stock');
    $new_columns['silver_stock'] = __('Silver Stock');
    $new_columns['category'] = __('Category');
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_jewelry_inventory_posts_columns', 'add_jewelry_inventory_columns');

// Fill custom columns with data
function fill_jewelry_inventory_columns($column, $post_id) {
    switch ($column) {
        case 'item_id':
            echo get_post_meta($post_id, 'item_id', true);
            break;
        case 'price':
            $price = get_post_meta($post_id, 'price', true);
            echo '₹' . number_format((float)$price, 2);
            break;
        case 'gold_stock':
            echo get_post_meta($post_id, 'gold_stock', true);
            break;
        case 'silver_stock':
            echo get_post_meta($post_id, 'silver_stock', true);
            break;
        case 'category':
            $terms = get_the_terms($post_id, 'jewelry_category');
            if (!empty($terms) && !is_wp_error($terms)) {
                $category_names = array();
                foreach ($terms as $term) {
                    $category_names[] = $term->name;
                }
                echo implode(', ', $category_names);
            }
            break;
    }
}
add_action('manage_jewelry_inventory_posts_custom_column', 'fill_jewelry_inventory_columns', 10, 2);

// Register meta fields to be accessible via REST API
function register_jewelry_meta_fields() {
    register_post_meta('jewelry_inventory', 'price', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'number'
    ));
    
    register_post_meta('jewelry_inventory', 'gold_stock', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'number'
    ));
    
    register_post_meta('jewelry_inventory', 'silver_stock', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'number'
    ));
    
    register_post_meta('jewelry_inventory', 'gold_item_code', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string'
    ));
    
    register_post_meta('jewelry_inventory', 'silver_item_code', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string'
    ));
    
    register_post_meta('jewelry_inventory', 'item_id', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string'
    ));
}
add_action('init', 'register_jewelry_meta_fields');


// expose jewellery API 
function add_jewelry_meta_to_api() {
  register_rest_field('jewelry_inventory', 'jewelry_data', array(
    'get_callback' => function($post) {
      return array(
        'price' => get_post_meta($post['id'], 'price', true),
        'gold_stock' => get_post_meta($post['id'], 'gold_stock', true),
        'silver_stock' => get_post_meta($post['id'], 'silver_stock', true),
        'gold_item_code' => get_post_meta($post['id'], 'gold_item_code', true),
        'silver_item_code' => get_post_meta($post['id'], 'silver_item_code', true),
        'item_id' => get_post_meta($post['id'], 'item_id', true),
        'category' => wp_get_post_terms($post['id'], 'jewelry_category', array('fields' => 'names'))
      );
    },
    'schema' => array(
        'description' => 'Jewelry item data',
        'type' => 'object'
    ),
  ));
}
add_action('rest_api_init', 'add_jewelry_meta_to_api');
