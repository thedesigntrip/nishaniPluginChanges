<?php
namespace EGrid\Controls\Sorter;

class EGrid_Sorter_Control extends \Elementor\Base_Data_Control {

	const CONTROL_NAME = 'egrid-sorter-control';

	/**
	 * Get emoji one area control type.
	 *
	 * Retrieve the control type, in this case `emojionearea`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type() {
		return self::CONTROL_NAME;
	}

	/**
	 * Enqueue emoji one area control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the emoji one
	 * area control.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue() {
		// Styles
		wp_register_style( 'egrid-sorter-control-css', EGRID_URL . 'includes/controls/sorter/assets/css/sorter.css', [], '1.0.0' );
		wp_enqueue_style( 'egrid-sorter-control-css' );

		// Scripts
		wp_register_script( 'egrid-sorter-control-js', EGRID_URL . 'includes/controls/sorter/assets/js/sorter.js', [ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable' ], '1.0.0' );
		wp_enqueue_script( 'egrid-sorter-control-js' );
	}

	/**
	 * Get emoji one area control default settings.
	 *
	 * Retrieve the default settings of the emoji one area control. Used to return
	 * the default settings while initializing the emoji one area control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
		];
	}

	/**
	 * Render emoji one area control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<#
				options = data.options || {};
				formattedOptions = {};
				controlValue = data.controlValue || {};
				formattedControlValue = {};
				if(options) {
					_.each(options, function (list, list_index) {
						if(_.isEmpty(controlValue) || _.isEmpty(controlValue[list_index])){
							controlValue[list_index] = _.keys(list);
						}
						_.each(list, function (opt_value, opt_index) {
							formattedOptions[opt_index] = opt_value;
						});
					});
				}
				_.each( controlValue, function( group, group_index ) {
					formattedControlValue[group_index] = {};
					_.each( formattedOptions, function( opt_value, opt_index ) {
						if(_.contains(group, opt_index)){
							formattedControlValue[group_index][opt_index] = opt_value;
							delete formattedOptions[opt_index];
						}
					});
				});
				_.each(options, function (group, group_index) {
					_.each( formattedOptions, function( opt_value, opt_index ) {
						if(group[opt_index]){
							formattedControlValue[group_index][opt_index] = opt_value;
						}
					});
				});

				console.log(formattedControlValue);
			#>

			<# if ( data.label ) {#>
				<label class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>

			<div class="egrid-control-sorter-wrapper">
				<#
					_.each(formattedControlValue, function (group, group_index) {
						#>
						<div class="egrid-control-sorter">
							<div class="egrid-control-sorter-header">
								<span>{{{ group_index }}}</span>
							</div>
							<ul id="sorter-{{ group_index }}-{{ data._cid }}" class="egrid-control-sorter-list egrid-control-sorter-{{ group_index }} connected-sortable" data-index="{{ group_index }}">
								<#
									_.each( group, function( opt_value, opt_index ) {
										#>
											<li id="{{ opt_index }}-{{ data._cid }}" class="ui-state-default egrid-control-sorter-item" data-value="{{ opt_index }}">{{{ opt_value }}}</li>
										<#
									});
								#>
						    </ul>
						</div>
						<#
					});
				#>
			</div>

		</div>

		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}

?>