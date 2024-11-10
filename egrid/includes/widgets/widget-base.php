<?php
namespace EGrid\Widgets;

use Elementor\Controls_Manager;
use EGrid\Controls\Layout\EGrid_Layout_Control;
use EGrid\Controls\Sorter\EGrid_Sorter_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor List Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Widget_Base extends \Elementor\Widget_Base {
	protected $name;
    protected $title;
    protected $icon;
    protected $categories;
    protected $keywords;
    protected $custom_help_url;
    protected $style_depends;
    protected $script_depends;

	public function __construct($data = [], $args = null ) {
		parent::__construct($data, $args);
		
		$name = isset($args['name']) && !empty($args['name']) ? $args['name'] : Utils::generate_random_string();
		$title = isset($args['title']) && !empty($args['title']) ? $args['title'] : strtoupper($name);
		$icon = isset($args['icon']) && !empty($args['icon']) ? $args['icon'] : 'eicon-tools';
		$categories = isset($args['categories']) && !empty($args['categories']) ? $args['categories'] : [];
		$categories = !empty($categories) ? $categories : [ 'egrid-category' ];
		$custom_help_url = isset($args['custom_help_url']) && !empty($args['custom_help_url']) ? $args['custom_help_url'] : '';
		$keywords = isset($args['keywords']) && !empty($args['keywords']) ? $args['keywords'] : [];
		$script_depends = isset($args['script_depends']) && !empty($args['script_depends']) ? $args['script_depends'] : [];
		$style_depends = isset($args['style_depends']) && !empty($args['style_depends']) ? $args['style_depends'] : [];

		$this->set_name($name);
		$this->set_title($title);
		$this->set_icon($icon);
		$this->set_categories($categories);
		$this->set_custom_help_url($custom_help_url);
		$this->set_keywords($keywords);
		$this->set_script_depends($script_depends);
		$this->set_style_depends($style_depends);
	}

	protected function set_name($name){
		$this->name = $name;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return $this->name;
	}

	protected function set_title($title){
		$this->title = $title;
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return $this->title;
	}

	protected function set_icon($icon){
		$this->icon = $icon;
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return $this->icon;
	}

	protected function set_custom_help_url($custom_help_url){
		$this->custom_help_url = $custom_help_url;
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
	public function get_custom_help_url() {
		return $this->custom_help_url;
	}

	protected function set_categories($categories){
		$this->categories = $categories;
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return $this->categories;
	}

	protected function set_keywords($keywords){
		$this->keywords = $keywords;
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return $this->keywords;
	}

	protected function set_script_depends($script_depends){
		$this->script_depends = $script_depends;
	}

	public function get_script_depends() {
		return $this->script_depends;
	}

	protected function set_style_depends($style_depends){
		$this->style_depends = $style_depends;
	}

	public function get_style_depends() {
		return $this->style_depends;
	}

	public function get_settings_for_display( $setting_key = null, $setting_default = null ){
		$settings = parent::get_settings_for_display($setting_key);

		$settings = !empty($settings) ? $settings : $setting_default;

		return $settings;
	}

	/**
	 * Register widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		do_action("egrid_widget_{$this->get_name()}_register_controls", $this);
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$template_path = EGRID_TEMPLATE_PATH . 'widgets/' . $this->get_name() . '/templates/';
        $default_path = EGRID_PATH . 'includes/widgets/' . $this->get_name() . '/templates/';

        $layout = $this->get_settings_for_display('layout', 'default');
        egrid_get_template($layout . '.php', [
        	'widget' => $this
        ], $template_path, $default_path);
	}
}
?>