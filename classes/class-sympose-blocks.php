<?php
/**
 * Blocks
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
class Sympose_Blocks {

	/**
	 * Construct
	 *
	 * @since    1.4.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories', array( $this, 'register_block_categories' ), 10, 2 );
	}

	/**
	 * Register block categories
	 *
	 * @since 1.4.0
	 *
	 * @param array $categories Array of existing block categories.
	 * @return array New category output.
	 */
	public function register_block_categories( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'sympose',
					'icon'  => 'wordpress',
					'title' => 'Sympose',
				),
			)
		);
	}

	/**
	 * Register block dependencies.
	 *
	 * @since 1.4.0
	 */
	public function register_blocks() {

		$asset_file = include plugin_dir_path( dirname( __FILE__ ) ) . '/blocks/build/index.asset.php';

		wp_register_script(
			'sympose-blocks',
			plugins_url( 'blocks/build/index.js', dirname( __FILE__ ) ),
			$asset_file['dependencies'],
			filemtime( plugin_dir_path( dirname( __FILE__ ) ) . '/blocks/build/index.js' ),
		);

		wp_register_style(
			'sympose-block-editor-style',
			plugins_url( 'css/dist/admin/blocks.css', dirname( __FILE__ ) ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( dirname( __FILE__ ) ) . '/css/dist/admin/blocks.css' )
		);

		wp_register_style(
			'sympose-frontend-style',
			plugins_url( 'css/dist/public/blocks.css', dirname( __FILE__ ) ),
			array(),
			filemtime( plugin_dir_path( dirname( __FILE__ ) ) . '/css/dist/public/blocks.css' )
		);

		register_block_type(
			'sympose/schedule',
			array(
				'editor_script'   => 'sympose-blocks',
				'editor_style'    => 'sympose-block-editor-style',
				'style'           => 'sympose-frontend-style',
				'render_callback' => array( $this, 'render_schedule' ),
			)
		);

		register_block_type(
			'sympose/list',
			array(
				'editor_script'   => 'sympose-blocks',
				'editor_style'    => 'sympose-block-editor-style',
				'style'           => 'sympose-frontend-style',
				'render_callback' => array( $this, 'render_list' ),
			)
		);
	}

	/**
	 * Render callback for Schedule
	 *
	 * @since 1.4.0
	 *
	 * @param array $attributes The attributes as saved by the block editor.
	 */
	public function render_schedule( $attributes ) {

		$args = array(
			'event'              => false,
			'show_people'        => true,
			'show_organisations' => true,
			'read_more'          => true,
			'hide_title'         => false,
		);

		$args = array_merge( $args, $attributes );

		foreach ( $args as &$arg ) {
			if ( true === $arg ) {
				$arg = 'true';
			}
		}

		if ( isset( $args['event'] ) ) {
			$event = $args['event'];
		}
		$sympose = new Sympose_Public();
		$output  = $sympose->render_schedule( $event, $args );
		return $output;
	}
	/**
	 * Render callback for List
	 *
	 * @since 1.4.0
	 *
	 * @param array $attributes The attributes as saved by the block editor.
	 */
	public function render_list( $attributes ) {

		$args = array(
			'event' => false,
			'type'  => 'person',
			'name'  => true,
		);

		$args = array_merge( $args, $attributes );

		$sympose = new Sympose_Public();
		$output  = $sympose->shortcodes( $args );
		return $output;
	}

}
