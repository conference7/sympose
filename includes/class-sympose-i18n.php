<?php
/**
 * Internationalization
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Internationalization function
 *
 * @since      1.0.0
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_I18n {


	/**
	 * Load the text domain
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sympose',
			false,
			basename( dirname( dirname( __FILE__ ) ) ) . '/languages'
		);

	}


}
