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
	protected $version;

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

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load dependencies
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Widgets.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/class-sympose-session-information.php';

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

		$plugin_admin = new Sympose_Admin( $this->get_sympose(), $this->get_version(), $this->get_prefix() );
	}

	/**
	 * Public hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sympose_Public( $this->get_sympose(), $this->get_version(), $this->get_prefix() );

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
