<?php
namespace EGrid\Controls\Layout;

class EGrid_Layout_Control extends \Elementor\Base_Data_Control {

	const CONTROL_NAME = 'egrid-layout-control';

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
		wp_register_style( 'egrid-layout-control-css', EGRID_URL . 'includes/controls/layout/assets/css/layout.css', [], '1.0.0' );
		wp_enqueue_style( 'egrid-layout-control-css' );

		// Scripts
		wp_register_script( 'egrid-layout-control-js', EGRID_URL . 'includes/controls/layout/assets/js/layout.js', [ 'jquery' ], '1.0.0' );
		wp_enqueue_script( 'egrid-layout-control-js' );
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
		return [];
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
			<div class="elementor-control-input-wrapper">
				<#
                if ( data.options ) {
                    _.each( data.options, function( value, key ) {
                        var selected = '';
                        if(data.controlValue == key){
                            selected = 'selected';
                        }
                #>
                <div class="egrid-layout-item {{ selected }}">
                    <input id="{{ data.name }}-{{ key }}" type="radio" class="field-egrid-layout" value="{{ key }}" name="{{ data.name }}" data-setting="{{ data.name }}" {{ selected }} />
                    <label for="{{ data.name }}-{{ key }}" data-title="{{ value.label }}">
                        <img src="{{ value.image }}" alt="{{ value.label }}">
                    </label>
                </div>
                <#
                    });
                }
                #>
			</div>
		</div>
		<?php
	}

}

?>