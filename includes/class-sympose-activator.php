<?php
/**
 * Plugin activation function
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Activation
 *
 * @since      1.0.0
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Activator {

	/**
	 * Activation function
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Set default options.
		$current_options = get_option( 'sympose' );

		if ( empty( $current_options ) ) {
			update_option(
				'sympose',
				array(
					'enable_css'                    => 'on',
					'render_sidebars_after_content' => false,
					'show_setup_wizard'             => true,
				)
			);
		}

		// Insert 'running' term.
		$admin = new Sympose_Admin();
		$admin->register_post_types();
		if ( ! term_exists( 'running', 'session-status' ) ) {
			$insert_term = wp_insert_term( 'running', 'session-status' );
		}

		// Schedule cronjob for remote extensions.
		if ( ! wp_next_scheduled( 'sympose_refresh_extensions' ) ) {
			wp_schedule_event( time(), 'daily', 'sympose_refresh_extensions' );
		}

		// Flush Rewrite Rules.
		flush_rewrite_rules();

		// Refresh Exensions.
		$sympose_admin = new Sympose_Admin();
		$sympose_admin->get_sympose_extensions();
	}

}
