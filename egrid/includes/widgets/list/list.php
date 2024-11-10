<?php
	use Elementor\Controls_Manager;
	use EGrid\Controls\Layout\EGrid_Layout_Control;
	use EGrid\Controls\Sorter\EGrid_Sorter_Control;

	if(!function_exists('egrid_register_widget_list')){
		add_filter('egrid_register_widgets', 'egrid_register_widget_list');
		function egrid_register_widget_list($widgets){
			$widgets[] = [
				'name' => 'list',
				'title' => esc_html__('List', EGRID_TEXT_DOMAIN),
				'icon' => 'eicon-bullet-list',
				'keywords' => [
					'egrid'
				]
			];

			return $widgets;
		}
	}

	if(!function_exists('egrid_widget_list_register_controls')){
		add_action('egrid_widget_list_register_controls', 'egrid_widget_list_register_controls', 10, 1);
		function egrid_widget_list_register_controls($widget){
			$widget->start_controls_section(
				'layout_section',
				[
					'label' => esc_html__( 'Content', EGRID_TEXT_DOMAIN ),
					'tab' => Controls_Manager::TAB_LAYOUT,
				]
			);

			$layout_options = apply_filters('egrid-products-widget-layout-options', [
				'default' => [
					'label' => esc_html__( 'Default', EGRID_TEXT_DOMAIN ),
					'image' => EGRID_URL . 'includes/widgets/woocommerce/products/assets/images/default.png'
				],
			]);
			$widget->add_control(
				'layout',
				[
					'type' => EGrid_Layout_Control::CONTROL_NAME,
					'options' => $layout_options,
				]
			);

			$widget->end_controls_section();
		}
	}
?>