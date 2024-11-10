<?php

namespace EGrid\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use EGrid\Controls\Layout\EGrid_Layout_Control;
use EGrid\Controls\Sorter\EGrid_Sorter_Control;

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
class EGrid_Products extends \Elementor\Widget_Base
{

	const TEMPLATE_PATH = EGRID_TEMPLATE_PATH . 'widgets/woocommerce/products/templates/';
	const DEFAULT_PATH = EGRID_PATH . 'includes/widgets/woocommerce/products/templates/';

	protected $custom_visibility = false;
	protected $limit = '';
	protected $orderby = '';
	protected $order = '';
	protected $ids = [];
	protected $skus = [];
	protected $cats = [];
	protected $tags = [];
	protected $attributes = [];
	protected $prices = [];
	protected $rating = '';
	protected $search = null;
	protected $stock_status = null;
	protected $featured_status = '';
	protected $columns = '';
	protected $visibility = 'visible';
	protected $page = 1;
	protected $paginate = false;

	public function __construct($data = [], $args = null)
	{
		parent::__construct($data, $args);

		if (isset($_POST['limit'])) {
			$limit = intval($_POST['limit']);
			$limit = $limit > 0 ? $limit : -1;
			$this->set_limit($limit);
		}

		if (isset($_POST['page'])) {
			$page = intval($_POST['page']);
			$page = $page > 0 ? $page : 1;
			$this->set_page($page);
		}

		if (isset($_POST['orderby']) && !empty($_POST['orderby'])) {
			$this->set_orderby($_POST['orderby']);
		}

		if (isset($_POST['order']) && !empty($_POST['order'])) {
			$order = strtoupper($_POST['order']);
			if (in_array($order, ['ASC', 'DESC'])) {
				$this->set_order($order);
			}
		}

		if (isset($_POST['columns']) && !empty($_POST['columns'])) {
			$columns = is_numeric($_POST['columns']) ? $_POST['columns'] : 4;
			$this->set_columns($columns);
		}

		if (isset($_POST['filters'])) {
			$filters = json_decode(stripslashes($_POST['filters']), true);

			if (isset($filters['attributes'])) {
				$this->set_attributes($filters['attributes']);
			}

			if (isset($filters['categories'])) {
				$this->set_cats($filters['categories']);
			}

			if (isset($filters['tags'])) {
				$this->set_tags($filters['tags']);
			}

			if (isset($filters['prices'])) {
				$this->set_prices($filters['prices']);
			}

			if (isset($filters['rating'])) {
				$this->set_rating($filters['rating']);
			}

			if (isset($filters['search'])) {
				$this->set_search($filters['search']);
			}

			if (isset($filters['stock_status'])) {
				$this->set_stock_status($filters['stock_status']);
			}

			if (isset($filters['featured_status'])) {
				$this->set_featured_status($filters['featured_status']);
			}
		}

		// add_action('wp_footer', [$this, 'wp_footer']);
	}

	protected function set_limit($limit)
	{
		$this->limit = $limit;
	}

	public function get_limit()
	{
		return $this->limit;
	}

	protected function set_page($page)
	{
		$this->page = $page;
	}

	public function get_page()
	{
		return $this->page;
	}

	protected function set_orderby($orderby)
	{
		$this->orderby = $orderby;
	}

	public function get_orderby()
	{
		return $this->orderby;
	}

	protected function set_order($order)
	{
		$this->order = $order;
	}

	public function get_order()
	{
		return $this->order;
	}

	protected function set_columns($columns)
	{
		$this->columns = $columns;
	}

	public function get_columns()
	{
		return $this->columns;
	}

	protected function set_cats($categories)
	{
		$this->cats = $categories;
	}

	public function get_cats()
	{
		return $this->cats;
	}

	protected function set_tags($tags)
	{
		$this->tags = $tags;
	}

	public function get_tags()
	{
		return $this->tags;
	}

	protected function set_prices($prices)
	{
		$this->prices = $prices;
	}

	public function get_prices()
	{
		return $this->prices;
	}

	protected function set_rating($rating)
	{
		$this->rating = $rating;
	}

	public function get_rating()
	{
		return $this->rating;
	}

	protected function set_attributes($attributes)
	{
		$this->attributes = $attributes;
	}

	public function get_attributes()
	{
		return $this->attributes;
	}

	protected function set_search($search)
	{
		$this->search = $search;
	}

	public function get_search()
	{
		return $this->search;
	}

	protected function set_stock_status($stock_status)
	{
		$this->stock_status = $stock_status;
	}

	public function get_stock_status()
	{
		return $this->stock_status;
	}

	protected function set_featured_status($featured_status)
	{
		$this->featured_status = $featured_status;
	}

	public function get_featured_status()
	{
		return $this->featured_status;
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
		return 'egrid-products';
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
		return esc_html__('Products', EGRID_TEXT_DOMAIN);
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
		return 'eicon-products';
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
		return ['egrid-products', 'egrid-price-filter', 'egrid-attribute-filter', 'egrid-products-pagination', 'egrid-products-order', 'egrid-products-category-filter', 'egrid-products-rating-filter', 'egrid-products-product-search', 'egrid-products-stock-status-filter', 'egrid-products-featured-filter', 'egrid-products-column', 'egrid-products-filter-popup'];
	}

	public function get_style_depends()
	{
		return ['select2', 'egrid-loader', 'egrid-placeholder', 'egrid-modal-css', 'egrid-products-css'];
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
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		$this->start_controls_section(
			'layout_section',
			[
				'label' => esc_html__('Content', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_LAYOUT,
			]
		);

		$layout_options = apply_filters('egrid-products-widget-layout-options', [
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
				'default' => apply_filters('egrid-products-widget-layout-default', 'default')
			]
		);

		$this->end_controls_section();
		// Item Layout
		$this->start_controls_section(
			'item_layout_section',
			[
				'label' => esc_html__( 'Item Layout', EGRID_TEXT_DOMAIN ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
			$item_layout_options = apply_filters('egrid-products-widget-item-layout-options', [
				'default' => [
					'label' => esc_html__( 'Default', EGRID_TEXT_DOMAIN ),
					'image' => EGRID_URL . 'includes/widgets/woocommerce/products/assets/images/default.png'
				],
			]);
			$this->add_control(
				'item_layout',
				[
					'type'         => EGrid_Layout_Control::CONTROL_NAME,
					'options'      => $item_layout_options,
					'default' 		 => apply_filters('egrid-products-widget-item-layout', 'default')
					//'prefix_class' => 'egrid-item-layout--'
				]
			);
		$this->end_controls_section();
		// Content
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content_order',
			[
				'label' => esc_html__( 'Priority Order', EGRID_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => '50',
				'selectors' => [
					'{{WRAPPER}} .egrid-products-content' => 'order: {{VALUE}};',
				],
				'condition' => [
					'enable_filter' => 'yes',
					'enable_filter_popup' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'image',// Usage: `{$args['name']}_size` and `{$args['name']}_custom_dimension`, in this case `$args['name']_size` and `$args['name']_custom_dimension`.
	            'control_type' => 'group',
	            'default'      => 'woocommerce_thumbnail',
	            'exclude'      => ['custom', 'thumbnail', '1536x1536','2048x2048','trp-custom-language-flag'] 
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
				'multiple' => true,
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'limit',
			[
				'label'   => esc_html__('Products to show', EGRID_TEXT_DOMAIN),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'   => esc_html__('Number of Columns', EGRID_TEXT_DOMAIN),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 6,
				'step'    => 1,
				'default' => 4
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__('Order', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('Default', EGRID_TEXT_DOMAIN),
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__('Order By', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_woocommerce_catalog_orderby_options(),
				'default' => get_option('woocommerce_default_catalog_orderby'),
			]
		);

		$this->add_control(
			'paginate',
			[
				'label' => esc_html__('Paginate', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'show_result_count',
			[
				'label' => esc_html__('Show Result Count', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_orderby',
			[
				'label' => esc_html__('Show Order by', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_featured_filter',
			[
				'label' => esc_html__('Show Featured Filter', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => esc_html__('Pagination Type', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pagination' => esc_html__('Pagination', EGRID_TEXT_DOMAIN),
					'load_more'  => esc_html__('Load more', EGRID_TEXT_DOMAIN),
					'none'       => esc_html__('None', EGRID_TEXT_DOMAIN),
				],
				'default' => 'pagination',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);

		$this->end_controls_section();
		// Content

		// Filter
		$this->start_controls_section(
			'filter_section',
			[
				'label' => esc_html__('Filter', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_filter',
			[
				'label' => esc_html__('Enable Filter', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'enable_filter_popup',
			[
				'label' => esc_html__('Filter popup?', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'enable_filter' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filter_position',
			[
				'label' => esc_html__('Position', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0'       => esc_html__('Start', EGRID_TEXT_DOMAIN),
					'99'         => esc_html__('End', EGRID_TEXT_DOMAIN),
				],
				'selectors' => [
					'{{WRAPPER}} .egrid-products-filters' => 'order: {{VALUE}};',
				],
				'condition' => [
					'enable_filter' => 'yes',
					'enable_filter_popup' => '',
				],
			]
		);

		$this->add_responsive_control(
			'filter_width',
			[
				'label' => esc_html__( 'Width', EGRID_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 33.333,
				],
				'selectors' => [
					'{{WRAPPER}} .egrid-products-filters' => 'flex: 0 0 {{SIZE}}%;',
				],
				'condition' => [
					'enable_filter' => 'yes',
					'enable_filter_popup' => '',
				],
			]
		);

		$filter_disabled_items = [
			'categories' => esc_html__('Categories', EGRID_TEXT_DOMAIN),
		];
		foreach ($attribute_taxonomies as $tax) {
			$filter_disabled_items[$tax->attribute_name] = $tax->attribute_label;
		}
		$filter_disabled_items['price'] = esc_html__('Price', EGRID_TEXT_DOMAIN);
		$filter_disabled_items['rating'] = esc_html__('Rating', EGRID_TEXT_DOMAIN);
		$filter_disabled_items['product_search'] = esc_html__('Product Search', EGRID_TEXT_DOMAIN);
		$filter_disabled_items['products'] = esc_html__('Products list', EGRID_TEXT_DOMAIN);
		$filter_disabled_items['stock_status'] = esc_html__('Stock Status', EGRID_TEXT_DOMAIN);
		$this->add_control(
			'filter_items',
			[
				'label' => esc_html__('Enable/Disable?', EGRID_TEXT_DOMAIN),
				'type' => EGrid_Sorter_Control::CONTROL_NAME,
				'options' => [
					'enabled' => [],
					'disabled' => $filter_disabled_items,
				],
				'condition' => [
					'enable_filter' => 'yes',
				],
			]
		);

		$this->end_controls_section();
		// Filter

		// Filter By Categories
		$this->start_controls_section(
			'filter_by_categories_section',
			[
				'label' => esc_html__('Filter by Categories', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'categories',
						],
					],
				],
			]
		);

		$this->add_control(
			'filter_by_categories_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Filter by Categories', EGRID_TEXT_DOMAIN),
			]
		);

		$this->add_control(
			"filter_by_categories_orderby",
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
			'filter_by_categories_display_type',
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
			'filter_by_categories_count',
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
			'filter_by_categories_hierarchical',
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
			'filter_by_categories_hide_empty',
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
			'filter_by_categories_max_depth',
			[
				'label' => esc_html__('Maximum depth', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 0,
			]
		);

		$this->end_controls_section();

		// Add filter for each attribute
		foreach ($attribute_taxonomies as $tax) {
			if (taxonomy_exists(wc_attribute_taxonomy_name($tax->attribute_name))) {
				$this->start_controls_section(
					"filter_by_{$tax->attribute_name}_section",
					[
						'label' => sprintf(esc_html__('Filter by %s', EGRID_TEXT_DOMAIN), $tax->attribute_label),
						'tab' => Controls_Manager::TAB_CONTENT,
						'conditions' => [
							'terms' => [
								[
									'name' => 'enable_filter',
									'operator' => '===',
									'value' => 'yes',
								],
								[
									'name' => 'filter_items[enabled]',
									'operator' => 'contains',
									'value' => $tax->attribute_name,
								],
							],
						],
					]
				);

				$this->add_control(
					"filter_by_{$tax->attribute_name}_title",
					[
						'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
						'type' => Controls_Manager::TEXT,
						'default' => sprintf(esc_html__('Filter by %s', EGRID_TEXT_DOMAIN), $tax->attribute_label),
					]
				);

				$this->add_control(
					"filter_by_{$tax->attribute_name}_display_type",
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
					"filter_by_{$tax->attribute_name}_query_type",
					[
						'label' => esc_html__('Query type', EGRID_TEXT_DOMAIN),
						'type' => Controls_Manager::SELECT,
						'default' => 'and',
						'options' => [
							'and'       => esc_html__('AND', EGRID_TEXT_DOMAIN),
							'or'       => esc_html__('OR', EGRID_TEXT_DOMAIN),
						],
					]
				);

				$this->end_controls_section();
			}
		}

		// Filter By Price
		$this->start_controls_section(
			'filter_by_price_section',
			[
				'label' => esc_html__('Filter by Price', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'price',
						],
					],
				],
			]
		);

		$this->add_control(
			'filter_by_price_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Filter by Price', EGRID_TEXT_DOMAIN),
			]
		);

		$this->end_controls_section();

		// Filter By Rating
		$this->start_controls_section(
			'filter_by_rating_section',
			[
				'label' => esc_html__('Filter by Rating', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'rating',
						],
					],
				],
			]
		);

		$this->add_control(
			'filter_by_rating_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Filter by Rating', EGRID_TEXT_DOMAIN),
			]
		);

		$this->end_controls_section();

		// Product Search
		$this->start_controls_section(
			'product_search_section',
			[
				'label' => esc_html__('Product Search', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'product_search',
						],
					],
				],
			]
		);

		$this->add_control(
			'product_search_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->end_controls_section();

		// Products list
		$this->start_controls_section(
			'products_section',
			[
				'label' => esc_html__('Products list', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'products',
						],
					],
				],
			]
		);

		$this->add_control(
			'products_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Products', EGRID_TEXT_DOMAIN),
			]
		);

		$this->add_control(
			'products_number',
			[
				'label' => esc_html__('Number of products to show', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
			]
		);

		$this->add_control(
			'products_show',
			[
				'label' => esc_html__('Show', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('All products', EGRID_TEXT_DOMAIN),
					'featured' => esc_html__('Featured products', EGRID_TEXT_DOMAIN),
					'onsale' => esc_html__('On-sale products', EGRID_TEXT_DOMAIN),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'products_orderby',
			[
				'label' => esc_html__('Order by', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'menu_order' => esc_html__('Menu order', EGRID_TEXT_DOMAIN),
					'date' => esc_html__('Date', EGRID_TEXT_DOMAIN),
					'price' => esc_html__('Price', EGRID_TEXT_DOMAIN),
					'rand' => esc_html__('Random', EGRID_TEXT_DOMAIN),
					'sales' => esc_html__('Sales', EGRID_TEXT_DOMAIN),
				],
				'default' => 'date',
			]
		);

		$this->add_control(
			'products_order',
			[
				'label' => esc_html__('Order', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'asc' => esc_html__('ASC', EGRID_TEXT_DOMAIN),
					'desc' => esc_html__('DESC', EGRID_TEXT_DOMAIN),
				],
				'default' => 'desc',
			]
		);

		$this->add_control(
			'products_hide_free',
			[
				'label' => esc_html__('Hide free products', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'products_show_hidden',
			[
				'label' => esc_html__('Show hidden products', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', EGRID_TEXT_DOMAIN),
				'label_off' => esc_html__('No', EGRID_TEXT_DOMAIN),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

		// Stock Status
		$this->start_controls_section(
			'filter_by_stock_status_section',
			[
				'label' => esc_html__('Filter by Stock Status', EGRID_TEXT_DOMAIN),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => [
					'terms' => [
						[
							'name' => 'enable_filter',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'filter_items[enabled]',
							'operator' => 'contains',
							'value' => 'stock_status',
						],
					],
				],
			]
		);

		$this->add_control(
			'filter_by_stock_status_title',
			[
				'label' => esc_html__('Title', EGRID_TEXT_DOMAIN),
				'type' => Controls_Manager::TEXT,
				'default' => '',
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
		$pagination_type = $this->get_settings_for_display('pagination_type', 'pagination');
		$is_loadmore = $pagination_type == 'load_more' && isset($_POST['loadmore']) && filter_var($_POST['loadmore'], FILTER_VALIDATE_BOOLEAN);
		$lazyloading = isset($_POST['lazyloading']) ? filter_var($_POST['lazyloading'], FILTER_VALIDATE_BOOLEAN) : true;

		if ($is_loadmore) {
			egrid_get_template($layout . '.php', [
				'widget' => $this
			], self::TEMPLATE_PATH . 'layouts/', self::DEFAULT_PATH . 'layouts/');
		} else {
			$enable_filter = $this->get_settings_for_display('enable_filter', 'no');

			egrid_get_template('wrapper-start.php', apply_filters('egrid_products_wrapper_start_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
			egrid_get_template('loader.php', apply_filters('egrid_products_loader_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
			egrid_get_template('inner-start.php', apply_filters('egrid_products_inner_start_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
			egrid_get_template('content-start.php', apply_filters('egrid_products_content_start_template_args', [
				'enable_filter' => $enable_filter,
			]), self::TEMPLATE_PATH, self::DEFAULT_PATH );
			egrid_get_template($layout . '.php', [
				'widget' => $this
			], self::TEMPLATE_PATH . 'layouts/', self::DEFAULT_PATH . 'layouts/');
			egrid_get_template('content-end.php', apply_filters('egrid_products_content_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
			if (apply_filters('egrid_products_hook_filters_to_footer', false, $this)) {
				add_action('wp_footer', [$this, 'include_filters']);
			} else {
				$this->include_filters();
			}
			egrid_get_template('inner-end.php', apply_filters('egrid_products_inner_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
			egrid_get_template('wrapper-end.php', apply_filters('egrid_products_wrapper_end_template_args', []), self::TEMPLATE_PATH, self::DEFAULT_PATH);
		}
	}

	protected function parse_query_args()
	{
		$paginate = $this->get_settings_for_display('paginate', false);
		$paginate = filter_var($paginate, FILTER_VALIDATE_BOOLEAN);
		$limit = $this->get_settings_for_display('limit', 8);
		$orderby = $this->get_woocommerce_default_catalog_orderby();
		$order = $this->get_settings_for_display('order', 'DESC');
		if ($this->get_limit()) {
			$limit = $this->get_limit();
		}
		if ($this->get_orderby()) {
			$orderby = $this->get_orderby();
		}
		if ($this->get_order()) {
			$order = $this->get_order();
		}

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => $paginate === false,
			'orderby'             => $orderby,
			'order'               => $order,
			's'					  => $this->get_search(),
		);

		if ($orderby == 'featured') {
			$query_args['meta_key'] = '_featured';
			$query_args['orderby'] = 'meta_value';
		} elseif ($orderby == 'rating') {
			$query_args['meta_key'] = '_wc_average_rating';
			$query_args['orderby'] = 'meta_value_num';
		} elseif ($orderby == 'popularity') {
			$query_args['meta_key'] = 'total_sales';
			$query_args['orderby'] = 'meta_value_num';
		}

		if ($paginate) {
			$query_args['paged'] = $this->get_page();
		}

		$query_args['meta_query'] = [];
		$query_args['tax_query']  = [];

		// // Visibility.
		// $this->set_visibility_query_args( $query_args );

		// // SKUs.
		// $this->set_skus_query_args( $query_args );

		// // IDs.
		// $this->set_ids_query_args( $query_args );

		// // Set specific types query args.
		// if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
		// 	$this->{"set_{$this->type}_query_args"}( $query_args );
		// }

		if($this->get_featured_status()){
			switch ( $this->get_featured_status() ) {
				case 'featured':
					$product_visibility_term_ids = wc_get_product_visibility_term_ids();
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
				case 'newarrival':
					$new_arrivals_days = get_option('egrid_new_arrivals_days', 1);
					$query_args['date_query'] = [
						[
							'after'     => $new_arrivals_days == 1 ? '1 day ago' : "{$new_arrivals_days} days ago",
         					'inclusive' => true,
						]
					];
					break;
			}
		}

		// Attributes.
		$query_args = $this->set_attributes_query_args($query_args);

		// Categories.
		$this->set_categories_query_args($query_args);

		// // Tags.
		// $this->set_tags_query_args( $query_args );

		// Prices.
		$query_args = $this->set_prices_query_args($query_args);

		// Rating.
		$query_args = $this->set_rating_query_args($query_args);

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}

	protected function set_visibility_hidden_query_args(&$query_args)
	{
		$this->custom_visibility   = true;
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => array('exclude-from-catalog', 'exclude-from-search'),
			'field'            => 'name',
			'operator'         => 'AND',
			'include_children' => false,
		);
	}

	protected function set_visibility_catalog_query_args(&$query_args)
	{
		$this->custom_visibility   = true;
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'exclude-from-search',
			'field'            => 'name',
			'operator'         => 'IN',
			'include_children' => false,
		);
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'exclude-from-catalog',
			'field'            => 'name',
			'operator'         => 'NOT IN',
			'include_children' => false,
		);
	}

	protected function set_visibility_search_query_args(&$query_args)
	{
		$this->custom_visibility   = true;
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'exclude-from-catalog',
			'field'            => 'name',
			'operator'         => 'IN',
			'include_children' => false,
		);
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'exclude-from-search',
			'field'            => 'name',
			'operator'         => 'NOT IN',
			'include_children' => false,
		);
	}

	protected function set_visibility_featured_query_args(&$query_args)
	{
		$query_args['tax_query'][] = array(
			'taxonomy'         => 'product_visibility',
			'terms'            => 'featured',
			'field'            => 'name',
			'operator'         => 'IN',
			'include_children' => false,
		);
	}

	protected function set_visibility_query_args(&$query_args)
	{
		$settings = $this->get_settings_for_display();
		if (!empty($settings['skus'])) {
			if (method_exists($this, 'set_visibility_' . $settings['visibility'] . '_query_args')) {
				$this->{'set_visibility_' . $settings['visibility'] . '_query_args'}($query_args);
			}
		}
	}

	protected function set_skus_query_args(&$query_args)
	{
		$settings = $this->get_settings_for_display();
		if (!empty($settings['skus'])) {
			$skus                       = array_map('trim', explode(',', $settings['skus']));
			$query_args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => 1 === count($skus) ? $skus[0] : $skus,
				'compare' => 1 === count($skus) ? '=' : 'IN',
			);
		}
	}

	protected function set_ids_query_args(&$query_args)
	{
		$settings = $this->get_settings_for_display();
		if (!empty($settings['ids'])) {
			$ids = array_map('trim', explode(',', $settings['ids']));

			if (1 === count($ids)) {
				$query_args['p'] = $ids[0];
			} else {
				$query_args['post__in'] = $ids;
			}
		}
	}

	protected function set_attributes_query_args(&$query_args)
	{
		$attributes = $this->get_attributes();

		if (!empty($attributes)) {
			foreach ($attributes as $taxonomy => $terms) {
				$taxonomy = strstr($taxonomy, 'pa_') ? sanitize_title($taxonomy) : 'pa_' . sanitize_title($taxonomy);
				$field    = 'slug';

				// If no terms were specified get all products that are in the attribute taxonomy.
				if (!$terms) {
					$terms = get_terms(
						array(
							'taxonomy' => $taxonomy,
							'fields'   => 'ids',
						)
					);
					$field = 'term_id';
				}

				$terms_operator = !is_array($terms) ? 'IN' : '=';
				$query_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'terms'    => $terms,
					'field'    => $field,
					'operator' => $terms_operator,
				);
			}
		}

		return $query_args;
	}

	protected function set_categories_query_args(&$query_args)
	{
		// $settings = $this->get_settings_for_display();
		$categories = $this->get_cats();
		$source = $this->get_settings_for_display('source', []);
		if(empty($categories)){
			$categories = $source;
		}
		if (!empty($categories)) {
			$categories = array_map('sanitize_title', $categories);
			$cat_operator = '=';
			if (is_array($categories)) {
				$cat_operator = 'IN';
			}
			$field      = 'slug';

			$query_args['tax_query'][] = array(
				'taxonomy'         => 'product_cat',
				'terms'            => $categories,
				'field'            => $field,
				'operator'         => $cat_operator,

				// /*
				//  * When cat_operator is AND, the children categories should be excluded,
				//  * as only products belonging to all the children categories would be selected.
				//  */
				// 'include_children' => 'AND' === $settings['cat_operator'] ? false : true,
				'include_children' => false,
			);
		}
	}

	protected function set_tags_query_args(&$query_args)
	{
		$settings = $this->get_settings_for_display();
		if (!empty($settings['tag'])) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_tag',
				'terms'    => array_map('sanitize_title', explode(',', $settings['tag'])),
				'field'    => 'slug',
				'operator' => $settings['tag_operator'],
			);
		}
	}

	protected function set_prices_query_args(&$query_args)
	{
		$prices = $this->get_prices();
		if (!empty($prices) && isset($prices['min_price']) && !empty($prices['min_price']) && isset($prices['max_price']) && !empty($prices['max_price'])) {
			$query_args['meta_query'][] = array(
				'key' => '_price',
				'value' => [floatval($prices['min_price']), floatval($prices['max_price'])],
				'compare' => 'BETWEEN',
				'type' => 'NUMERIC',
			);
		}

		return $query_args;
	}

	protected function set_rating_query_args(&$query_args)
	{
		$rating = $this->get_rating();
		if (!empty($rating)) {
			$query_args['meta_query'][] = array(
				'key' => '_wc_average_rating',
				'value' => floatval($rating),
				'compare' => '=',
				'type' => 'NUMERIC',
			);
		}

		return $query_args;
	}

	public function get_query_results()
	{
		$stock_status = $this->get_stock_status();
		if($stock_status){
			add_filter( 'posts_clauses', [ $this, 'filter_stock_status_post_clauses' ] );
		}
		$query = new \WP_Query($this->parse_query_args());

		if($stock_status){
			remove_filter( 'posts_clauses', [ $this, 'filter_stock_status_post_clauses' ] );
		}

		$paginated = !$query->get('no_found_rows');

		$results = (object) array(
			'ids'          => wp_parse_id_list($query->posts),
			'total'        => $paginated ? (int) $query->found_posts : count($query->posts),
			'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
			'per_page'     => (int) $query->get('posts_per_page'),
			'current_page' => $paginated ? (int) max(1, $query->get('paged', 1)) : 1,
		);

		return $results;
	}

	public function set_product_as_visible($visibility)
	{
		return $this->custom_visibility ? true : $visibility;
	}

	public function filter_stock_status_post_clauses( $args ) {
		global $wpdb;

		$stock_status = $this->get_stock_status();
		if($stock_status){
			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= $wpdb->prepare( ' AND wc_product_meta_lookup.stock_status=%s ', wc_clean( wp_unslash( $stock_status ) ) );
		}
		return $args;
	}

	public function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}

	public function set_woocommerce_pagination_args($args)
	{
		$args['base'] = '#%_%';
		$args['format'] = '%#%';

		return $args;
	}

	public function customize_wp_dropdown_cats($output, $parsed_args)
	{
		$output = preg_replace('/<select([^>]*)>/', '<select$1 egrid-products-category-filter>', $output);
		if (isset($parsed_args['multiple']) && $parsed_args['multiple'] == true) {
			$output = preg_replace('/<select([^>]*)>/', '<select$1 multiple>', $output);
		}

		return $output;
	}

	public function get_woocommerce_catalog_orderby_options()
	{
		return apply_filters('egrid_woocommerce_catalog_orderby_options', [
			'menu_order' => esc_html__('Default sorting', EGRID_TEXT_DOMAIN),
			// 'featured' => esc_html__('Featured', EGRID_TEXT_DOMAIN),
			'popularity' => esc_html__('Best Selling', EGRID_TEXT_DOMAIN),
			'rating'     => esc_html__('Rating', EGRID_TEXT_DOMAIN),
			'title'      => esc_html__('Alphabetically, A-Z', EGRID_TEXT_DOMAIN),
			'title-desc' => esc_html__('Alphabetically, Z-A', EGRID_TEXT_DOMAIN),
			'price'      => esc_html__('Price, low to high', EGRID_TEXT_DOMAIN),
			'price-desc' => esc_html__('Price, high to low', EGRID_TEXT_DOMAIN),
			'date'       => esc_html__('Date, old to new', EGRID_TEXT_DOMAIN),
			'date-desc'  => esc_html__('Date, new to old', EGRID_TEXT_DOMAIN),
		]);
	}

	public function get_woocommerce_default_catalog_orderby()
	{
		$orderby = $this->get_settings_for_display('orderby', get_option('woocommerce_default_catalog_orderby'));
		if (!empty($this->get_orderby())) {
			$orderby = $this->get_orderby();
		}
		if ($orderby == 'title' || $orderby == 'price') {
			if (strtoupper($this->get_order()) == 'DESC') {
				$orderby .= '-desc';
			}
		}
		return $orderby;
	}

	public function woocommerce_filter_popup_switch(){
		egrid_get_template('filter-popup-switch.php', [
			'widget' => $this,
		], EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
	}

	public function woocommerce_featured_filter(){
		egrid_get_template('featured-filter.php', [
			'widget' => $this,
		], EGrid_Products::TEMPLATE_PATH . 'filters/', EGrid_Products::DEFAULT_PATH . 'filters/');
	}

	public function include_filters()
	{
		egrid_get_template('filters.php', [
			'widget' => $this,
		], self::TEMPLATE_PATH, self::DEFAULT_PATH);
	}

	public function wp_footer(){
		egrid_get_template('wp-footer.php', [
			'widget' => $this,
		], self::TEMPLATE_PATH, self::DEFAULT_PATH);
	}
}
