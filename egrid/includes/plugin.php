<?php
namespace EGrid;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin class.
 *
 * The main class that initiates and runs the addon.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Addon Version
	 *
	 * @since 1.0.0
	 * @var string The addon version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.3';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var \EGrid\Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return \EGrid\Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
			add_action('wp_enqueue_scripts', [$this, 'enqueue']);
		}

		add_filter('woocommerce_get_settings_pages', [$this, 'woocommerce_get_settings_pages']);
		add_action('wp_footer', [$this, 'wp_footer']);
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', EGRID_TEXT_DOMAIN ),
			'<strong>' . esc_html__( 'Elementor Grid', EGRID_TEXT_DOMAIN ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', EGRID_TEXT_DOMAIN ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', EGRID_TEXT_DOMAIN ),
			'<strong>' . esc_html__( 'Elementor Grid', EGRID_TEXT_DOMAIN ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', EGRID_TEXT_DOMAIN ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', EGRID_TEXT_DOMAIN ),
			'<strong>' . esc_html__( 'Elementor Grid', EGRID_TEXT_DOMAIN ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', EGRID_TEXT_DOMAIN ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		require_once EGRID_PATH . 'includes/helpers/elementor.php';

		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
		add_action( 'elementor/elements/categories_registered', [$this, 'register_categories']);

		if (!class_exists('EGrid_Ajax_Handle')) {
            require_once EGRID_PATH . '/includes/classes/class-ajax-handle.php';
            new \EGrid_Ajax_Handle();
        }
	}

	/**
	 * Register Widgets
	 *
	 * Load widgets files and register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// require_once( EGRID_PATH . 'includes/widgets/widget-base.php' );

		// require_once( EGRID_PATH . 'includes/widgets/list/list.php' );
		// require_once( EGRID_PATH . 'includes/widgets/button/button.php' );

		// $widgets = apply_filters('egrid_register_widgets', []);

		// foreach ($widgets as $widget) {
		// 	$new_widget = new \EGrid\Widgets\Widget_Base([], $widget);
		// 	$widgets_manager->register( $new_widget );
		// }

		if(class_exists('WooCommerce')){
			require_once( EGRID_PATH . 'includes/widgets/woocommerce/products/products.php' );
			require_once( EGRID_PATH . 'includes/widgets/woocommerce/filters/categories/categories.php' );

			$widgets_manager->register( new \EGrid\Widgets\Woocommerce\EGrid_Products() );
			$widgets_manager->register( new \EGrid\Widgets\Woocommerce\EGrid_Products_Categories_Filter() );
		}

	}

	/**
	 * Register Controls
	 *
	 * Load controls files and register new Elementor controls.
	 *
	 * Fired by `elementor/controls/register` action hook.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
	public function register_controls( $controls_manager ) {

		require_once( EGRID_PATH . 'includes/controls/layout/layout.php' );
		require_once( EGRID_PATH . 'includes/controls/sorter/sorter.php' );

		$controls_manager->register( new \EGrid\Controls\Layout\EGrid_Layout_Control() );
		$controls_manager->register( new \EGrid\Controls\Sorter\EGrid_Sorter_Control() );
	}

	public function register_categories( $elements_manager ) {
		$elements_manager->add_category(
			'egrid-category',
			[
				'title' => esc_html__( 'EGrid', EGRID_TEXT_DOMAIN ),
				'icon' => 'fa fa-plug',
			]
		);
	}

	public function enqueue() {
		wp_register_style('egrid-loader', EGRID_URL . 'assets/css/loader.css', [], '1.0.0');
		wp_register_style('egrid-placeholder', EGRID_URL . 'assets/css/placeholder.css', [], '1.0.0');
		wp_register_style('egrid-modal-css', EGRID_URL . 'assets/css/modal.css', [], '1.0.0');
		if(class_exists('WooCommerce')){
			wp_register_style('egrid-products-css', EGRID_URL . 'includes/widgets/woocommerce/products/assets/css/egrid-products.css', [], '1.0.0');

			wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting.min.js', array( 'jquery' ), '0.4.2', true );
			wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch.min.js', array( 'jquery-ui-slider' ), self::VERSION, true );
			wp_register_script( 'egrid-price-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/price-filter.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch', 'accounting' ), '1.0.0', true );
			wp_localize_script(
				'egrid-price-filter',
				'egrid_price_filter_params',
				array(
					'currency_format_num_decimals' => 0,
					'currency_format_symbol'       => get_woocommerce_currency_symbol(),
					'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
					'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
					'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
				)
			);

			wp_register_script( 'egrid-attribute-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/attribute-filter.js', array( 'selectWoo' ), '1.0.0', true );
			wp_register_script( 'egrid-products-pagination', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/pagination.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-order', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/order.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-category-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/category-filter.js', array( 'selectWoo' ), '1.0.0', true );
			wp_register_script( 'egrid-products-rating-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/rating.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-product-search', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/product-search.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-stock-status-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/stock-status.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-featured-filter', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/featured-filter.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-column', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/column.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-filter-popup', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/filter-popup.js', array( 'jquery' ), '1.0.0', true );

			wp_register_script( 'egrid-products', EGRID_URL . 'includes/widgets/woocommerce/products/assets/js/egrid-products.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'egrid-products-categories-filter-widget-js', EGRID_URL . 'includes/widgets/woocommerce/filters/categories/assets/js/widget.js', array( 'jquery' ), '1.0.0', true );
			wp_localize_script(
				'egrid-products',
				'egrid_products',
				[
					'ajax_url' => admin_url('admin-ajax.php'),
					'post_id' => get_the_ID()
				]
			);
		}
	}

	public function woocommerce_get_settings_pages($settings){
		$settings[] = include EGRID_PATH . 'includes/widgets/woocommerce/settings.php';
		return $settings;
	}

	public function wp_footer(){
		egrid_get_template('wp-footer.php', [], EGRID_TEMPLATE_PATH, EGRID_PATH . 'templates/');
	}
}