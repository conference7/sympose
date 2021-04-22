<?php
/**
 * Core
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Core class
 *
 * @since      1.0.0
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $sympose Unique identifier
	 */
	protected $sympose;

	/**
	 * The prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $prefix Prefix string
	 */
	protected $prefix;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version Current version
	 */
	public $version;

	/**
	 * Core
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SYMPOSE_VERSION' ) ) {
			$this->version = SYMPOSE_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->sympose = 'sympose';
		$this->prefix  = '_sympose_';

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_hooks();

	}

	/**
	 * Local
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

	}

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

	/**
	 * Admin hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		new Sympose_Admin( $this->get_sympose(), $this->get_version(), $this->get_prefix() );
	}

	/**
	 * Public hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		new Sympose_Public( $this->get_sympose(), $this->get_version(), $this->get_prefix() );

	}

	/**
	 * General hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {
		// Register widgets.
		add_action( 'widgets_init', array( $this, 'register_widgets' ), 20, 1 );

		new Sympose_Social_Media( $this->get_sympose(), $this->get_version(), $this->get_prefix() );
		new Sympose_Compatibility( $this->get_sympose(), $this->get_version(), $this->get_prefix() );

	}

	/**
	 * Register Sympose Widgets
	 */
	public function register_widgets() {
		register_widget( 'Sympose_Widget_Profile' );
		register_widget( 'Sympose_Session_Information' );
		register_widget( 'Sympose_Session_Participants' );
	}

	/**
	 * Get plugin name
	 *
	 * @return    string    Returns the name
	 * @since     1.0.0
	 */
	public function get_sympose() {
		return $this->sympose;
	}

	/**
	 * The prefix of the plugin
	 *
	 * @return    string    Returns name
	 * @since     1.0.0
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Get version number
	 *
	 * @return    string    returns the version number
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
