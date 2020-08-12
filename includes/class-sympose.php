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

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sympose-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sympose-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sympose-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sympose-public.php';

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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'register_post_types' );
		$this->loader->add_action( 'cmb2_init', $plugin_admin, 'register_custom_fields', 10 );

		$this->loader->add_action( 'init', $plugin_admin, 'add_image_sizes' );

		// Session.
		$this->loader->add_action( 'manage_session_posts_columns', $plugin_admin, 'session_columns' );
		$this->loader->add_action( 'manage_session_posts_custom_column', $plugin_admin, 'column_content' );

		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'order_by_time' );

		// People.
		$this->loader->add_action( 'manage_person_posts_columns', $plugin_admin, 'person_columns' );
		$this->loader->add_action( 'manage_person_posts_custom_column', $plugin_admin, 'column_content' );

		// Organisations.
		$this->loader->add_action( 'manage_organisation_posts_columns', $plugin_admin, 'organisation_columns' );
		$this->loader->add_action( 'manage_organisation_posts_custom_column', $plugin_admin, 'column_content' );

		// Add filters to dashboard.
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'manage_filters' );

		// Remove date filter from dashboard.
		$this->loader->add_action( 'admin_head', $plugin_admin, 'remove_date_filter' );

		// Add settings page.
		$this->loader->add_action( 'cmb2_init', $plugin_admin, 'settings_page', 10 );

		// Save post: Sets the post publish date to the session date/time.
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post', 20, 2 );

		// Register REST.
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'register_rest_routes' );

		// Register Sidebars.
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'register_sidebars' );

		// Cron Schedules.
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'register_cron_schedules' );

		// Link remote products function to cronjob.
		$this->loader->add_action( 'sympose_refresh_extensions', $plugin_admin, 'get_sympose_extensions' );

		// Add Custom row actions.
		$this->loader->add_action( 'event_row_actions', $plugin_admin, 'add_row_actions', 10, 2 );

		// Sympose Submenu Pages.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_sub_menu', 20, 2 );

		// Add settings link to plugin actions.
		$this->loader->add_filter( 'plugin_action_links_sympose/sympose.php', $plugin_admin, 'plugin_row_actions', 10, 2 );

		// Order session on event/day and time.
		$this->loader->add_filter( 'edited_event', $plugin_admin, 'republish_sessions', 20, 2 );

		// Order session on event/day and time.
		$this->loader->add_filter( 'admin_footer', $plugin_admin, 'maybe_show_setup_wizard', 20, 2 );

		// Add logic to disable Quick Start.
		$this->loader->add_filter( 'admin_notices', $plugin_admin, 'disable_quick_start_notice', 20, 2 );

		// Mark current submenu.
		$this->loader->add_filter( 'parent_file', $plugin_admin, 'highlight_parent_menu_item', 20, 1 );

		// Mark current submenu.
		$this->loader->add_filter( 'submenu_file', $plugin_admin, 'highlight_sub_menu_item', 20, 1 );

		// Mark current submenu.
		$this->loader->add_filter( 'admin_notices', $plugin_admin, 'validate_sympose_license', 20, 1 );
	}

	/**
	 * Public hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sympose_Public( $this->get_sympose(), $this->get_version(), $this->get_prefix() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Shortcode.
		$this->loader->add_action( 'init', $plugin_public, 'shortcodes' );

		// Add related info to content.
		$this->loader->add_filter( 'the_content', $plugin_public, 'add_content' );

		$this->loader->add_filter( 'sidebars_widgets', $plugin_public, 'change_sidebars' );

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
