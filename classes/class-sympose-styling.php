<?php
/**
 *
 * @link              https://sympose.io
 * @since             1.0.0
 * @package           Sympose
 *
 * @sympose
 * Plugin Name:       Sympose Social Media
 * Plugin URI:        https://sympose.io/
 * Description:       Adds Social Media to People and Organisations
 * Version:           1.2
 * Author:            Sympose
 * Author URI:        https://sympose.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sympose
 * Domain Path:       /languages
 */

class Sympose_Styling {

	public function __construct( $sympose = '', $version = '', $prefix = '_sympose_' ) {
		$this->sympose = $sympose;
		$this->version = $version;
		$this->prefix  = $prefix;
		$this->hooks();
	}

	public function hooks() {
		add_action( 'sympose_register_settings_general_fields', array( $this, 'add_styling_fields' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'plugins_loaded', array( $this, 'basic_styling' ) );
	}

	public function basic_styling() {
		$style = sympose_get_option( 'active_styling' );
		if ( $style === 'basic' ) {
			add_filter( 'sympose_schedule_read_more', '__return_empty_string' );
			add_filter( 'sympose_schedule_title', array( $this, 'customize_schedule_title' ), 10, 5 );
		}
	}

	public function customize_schedule_title( $output, $post_id, $link_start, $title, $link_end ) {
		$output      = $link_start . $title . $link_end;
		$description = get_the_excerpt( $post_id );
		$output     .= '<p class="excerpt">' . $description . '</p>';
		return $output;
	}

	public function enqueue_styles() {
		$style = sympose_get_option( 'active_styling' );
		wp_enqueue_style( $this->sympose . 'styling', plugin_dir_url( dirname( __FILE__ ) ) . 'css/dist/' . $style . '/style.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'css', array(), $this->version, 'all' );
	}

	public function add_styling_fields( $options ) {
		$options->add_field(
			array(
				'name'       => __( 'Enable CSS', 'sympose' ),
				'type'       => 'select',
				'id'         => $this->prefix . 'active_styling',
				'options_cb' => function () {
					return array(
						'none'  => 'No styling',
						'basic' => 'Basic',
					);
				},
			)
		);
	}
}
