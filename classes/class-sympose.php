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
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sympose_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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

		$this->loader = new Sympose_Loader();

	}

	/**
	 * Local
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sympose_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

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
	 * Run loader
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * Get loader
	 *
	 * @return    Sympose_Loader    Load the hooks
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
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
