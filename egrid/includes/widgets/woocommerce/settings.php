<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'EGRID_WC_Settings', false ) ) {
	return new EGRID_WC_Settings();
}

/**
 * WC_Admin_Settings_General.
 */
class EGRID_WC_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'egrid';
		$this->label = __( 'Egrid', EGRID_TEXT_DOMAIN );

		parent::__construct();
	}

	/**
	 * Get settings or the default section.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {

		$settings =
			array(
				array(
					'title'             => __( 'New Arrivals days', EGRID_TEXT_DOMAIN ),
					'desc'              => __( 'This sets the number days to define the new arrivals products.', EGRID_TEXT_DOMAIN ),
					'id'                => 'egrid_new_arrivals_days',
					'default'           => '1',
					'desc_tip'          => true,
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 1,
						'step' => 1,
					),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'new_arrivals_options',
				),
			);

		return apply_filters( 'egrid_woocommerce_settings', $settings );
	}
}

return new EGRID_WC_Settings();
