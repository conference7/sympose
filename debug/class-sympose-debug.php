<?php
/**
 * This file is used for local debugging.
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/admin
 */

/**
 * Debug
 *
 * Some debug functionality, ex.: setting the right path for CMB2 when the plugin is symlinked.
 *
 * @package    Sympose
 * @subpackage Sympose/admin
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Debug {
	/**
	 * Construct the plugin
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize
	 */
	public function init() {
		add_filter( 'cmb2_meta_box_url', array( $this, 'set_cmb2_resources_url' ) );
	}

	/**
	 * Set the correct CMB2 resource path
	 */
	public function set_cmb2_resources_url() {
		return plugin_dir_url( dirname( __FILE__ ) ) . 'vendor/cmb2/cmb2/';
	}
}
