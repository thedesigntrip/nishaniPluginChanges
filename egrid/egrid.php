<?php
/**
 * Plugin Name: EGrid
 * Description: Custom Elementor addon.
 * Plugin URI:  #
 * Version:     1.0.5
 * Author:      Kenneth Roy
 * Author URI:  #
 * Text Domain: egrid
 * 
 * Elementor tested up to:     3.22.3
 * Elementor Pro tested up to: 3.22.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define('EGRID_TEXT_DOMAIN', 'egrid');
define('EGRID_PATH', plugin_dir_path(__FILE__));
define('EGRID_URL', plugin_dir_url(__FILE__));
define('EGRID_TEMPLATE_PATH', 'egrid' . DIRECTORY_SEPARATOR);

function egrid() {
    require_once(__DIR__ . '/includes/helpers/template.php');
    if(class_exists('WooCommerce')){
        require_once(__DIR__ . '/includes/helpers/woocommerce.php');
    }

	// Load plugin file
	require_once( __DIR__ . '/includes/plugin.php' );

	// Run the plugin
	\EGrid\Plugin::instance();

}
add_action( 'plugins_loaded', 'egrid' );