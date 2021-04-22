<?php
/**
 * Compatibility
 *
 * @link       https://sympose.net
 * @since      1.4.0
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Blocks
 *
 * @since      1.4.0
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Compatibility {

	/**
	 * Construct
	 *
	 * @since    1.4.0
	 */
	public function __construct() {
		add_filter( 'sympose_customize_submenu_pages', array( $this, 'customize_submenu_pages' ) );
	}

	/**
	 * Customize submenu pages
	 *
	 * @since 1.4.0
	 *
	 * @param array $submenu Original submenu array.
	 * @return array New submenu output.
	 */
	public function customize_submenu_pages( $submenu ) {
		if ( ! class_exists( 'Sympose_Tracks' ) ) {
			return $submenu;
		}

		return array_slice( $submenu, 0, 3, true ) +
			array(
				25 => array(
					'page_title'      => __( 'Tracks', 'sympose' ),
					'menu_title'      => __( 'Tracks', 'sympose' ),
					'callback'        => esc_html(
						add_query_arg(
							array(
								'taxonomy'  => 'session-track',
								'post_type' => 'session',
							),
							'edit-tags.php'
						)
					),
					'capability_type' => apply_filters( 'sympose_manage_tracks_cap', 'manage_options' ),
				),
			) +
			array_slice( $submenu, 3, count( $submenu ) - 3, true );
	}

}
