<?php

namespace EGrid\Widgets\Woocommerce;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Utils;
use EGrid\Controls\Layout\EGrid_Layout_Control;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor List Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class EGrid_Products_Categories_Filter extends \Elementor\Widget_Base
{

	const TEMPLATE_PATH = EGRID_TEMPLATE_PATH . 'widgets/woocommerce/filters/categories/templates/';
	const DEFAULT_PATH = EGRID_PATH . 'includes/widgets/woocommerce/filters/categories/templates/';

	protected $cats = [];

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (isset($_POST['categories'])) {
			$this->set_cats($_POST['categories']);
		}
	}

	protected function set_cats($categories)
	{
		$this->cats = $categories;
	}

	public function get_cats()
	{
		return $this->cats;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve list widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'egrid-products-categories-filter';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve list widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__('Products Categories Filter', EGRID_TEXT_DOMAIN);
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve list widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-product-categories';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url()
	{
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return ['egrid-category'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords()
	{
		return ['egrid', 'woocommerce', 'shop', 'store', 'product', 'archive', 'upsells', 'cross-sells', 'cross sells', 'related'];
	}

	public function get_script_depends()
	{
		return ['selectWoo', 'egrid-products-categories-filter-widget-js'];
	}

	public function get_style_depends()
	{
		return [];
	}

	public function get_settings_for_display($setting_key = null, $setting_default = null)
	{
		$settings = parent::get_settings_for_display($setting_key);

		$settings = !empty($settings) ? $settings : $setting_default;

		return $settings;
	}

	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{
		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__('Content', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_LAYOUT,
			]
		);

		$layout_options = apply_filters('egrid-products-categories-filter-widget-layout-options', [
			'default' => [
				'label' => esc_html__('Default', EGRID_TEXT_DOMAIN),
				'image' => EGRID_URL . 'includes/widgets/woocommerce/products/assets/images/default.png'
			],
		]);
		$this->add_control(
			'layout',
			[
				'type'    => EGrid_Layout_Control::CONTROL_NAME,
				'options' => $layout_options,
				'default' => apply_filters('egrid-products-category-filter-widget-layout-default', 'default')
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__('Settings', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$grid_options = [];
		$grid_option_default = '';
		$post = get_post();
		if ($post && $post->ID) {
		    $document = Plugin::$instance->documents->get_doc_for_frontend($post->ID);
		    if ($document && $document->is_built_with_elementor()) {
		        Plugin::$instance->documents->switch_to_document($document);
		        $elements_data = $document->get_elements_data();
		        $elements = egrid_find_element_by_type_recursive($elements_data, 'egrid-products');
		        foreach ($elements as $element) {
		        	$grid_options[$element['id']] = $element['id'];
		        	if(empty($grid_option_default)){
		        		$grid_option_default = $element['id'];
		        	}
		        }
		        Plugin::$instance->documents->restore_document();
		    }
		}
		$this->add_control(
			'grid',
			[
				'label' => esc_html__('Grid', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT2,
				'options' => $grid_options,
				'default' => $grid_option_default,
				'frontend_available' => true,
			]
		);

		$categories = get_terms([
			'taxonomy' => 'product_cat'
		]);
		$category_options = [];
		if(!is_wp_error($categories)){
			foreach ($categories as $category) {
				$category_options[$category->slug] = $category->name;
			}
		}
		$this->add_control(
			'source',
			[
				'label' => esc_html__('Source', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT2,
				'options' => $category_options,
				'multiple' => false,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Filter by Categories', EGRID_TEXT_DOMAIN),
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__('Order by', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'order'       => esc_html__('Category order', EGRID_TEXT_DOMAIN),
					'name'       => esc_html__('Name', EGRID_TEXT_DOMAIN),
				],
			]
		);

		$this->add_control(
			'display_type',
			[
				'label' => esc_html__('Display type', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => [
					'list'       => esc_html__('List', EGRID_TEXT_DOMAIN),
					'dropdown'       => esc_html__('Dropdown', EGRID_TEXT_DOMAIN),
				],
			]
		);
		$this->add_control(
			'show_all',
			[
				'label'        => esc_html__('Show All', EGRID_TEXT_DOMAIN),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off'    => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);
		$this->add_control(
			'all_text',
			[
				'label' => esc_html__('`All` Text', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('All', EGRID_TEXT_DOMAIN),
				'condition'   => [
                    'show_all' => 'yes',
                ],
			]
		);
		$this->add_control(
            'all_image',
            [
                'label'       => esc_html__('`All` Image', EGRID_TEXT_DOMAIN),
                'type'        => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src()
                ],
                'condition'   => [
                    'show_all' => 'yes',
                    'thumbnail' => 'yes',
                ],
            ]
        );
		$this->add_control(
			'count',
			[
				'label' => esc_html__('Show product counts', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'thumbnail',
			[
				'label' => esc_html__('Show thumbnail', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'hierarchical',
			[
				'label' => esc_html__('Show hierarchy', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label' => esc_html__('Hide empty categories', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'max_depth',
			[
				'label' => esc_html__('Maximum depth', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 0,
			]
		);

		$this->add_control(
			'multiple',
			[
				'label' => esc_html__('Multiple', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		$layout = $this->get_settings_for_display('layout');
		$layout = $layout ? $layout : 'default';

		egrid_get_template($layout . '.php', [
			'widget' => $this
		], self::TEMPLATE_PATH . 'layouts/', self::DEFAULT_PATH . 'layouts/');
	}
}
