<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sympose
 * @subpackage Sympose/admin
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $sympose The ID of this plugin.
	 */
	private $sympose;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $prefix The prefix of this plugin.
	 */
	private $prefix;

	/**
	 * The store url of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $store_url The prefix of this plugin.
	 */
	private $store_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param boolean $sympose The name of this plugin.
	 * @param boolean $version The version of this plugin.
	 * @param string  $prefix The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $sympose = false, $version = false, $prefix = '_sympose_' ) {

		$this->sympose   = $sympose;
		$this->version   = $version;
		$this->prefix    = $prefix;
		$this->store_url = 'https://sympose.net';

		$this->init();

	}

	/**
	 * Initialize the class
	 */
	public function init() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'cmb2_init', array( $this, 'register_custom_fields' ), 10 );

		add_action( 'init', array( $this, 'add_image_sizes' ) );

		// Session.
		add_action( 'manage_session_posts_columns', array( $this, 'session_columns' ) );
		add_action( 'manage_session_posts_custom_column', array( $this, 'column_content' ) );

		add_action( 'pre_get_posts', array( $this, 'order_by_time' ) );

		// People.
		add_action( 'manage_person_posts_columns', array( $this, 'person_columns' ) );
		add_action( 'manage_person_posts_custom_column', array( $this, 'column_content' ) );

		// Organisations.
		add_action( 'manage_organisation_posts_columns', array( $this, 'organisation_columns' ) );
		add_action( 'manage_organisation_posts_custom_column', array( $this, 'column_content' ) );

		// Add filters to dashboard.
		add_action( 'restrict_manage_posts', array( $this, 'manage_filters' ) );

		// Remove date filter from dashboard.
		add_action( 'admin_head', array( $this, 'remove_date_filter' ) );

		// Add settings page.
		add_action( 'cmb2_init', array( $this, 'settings_page' ), 10 );

		// Save post: Sets the post publish date to the session date/time.
		add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );

		// Register REST.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Register Sidebars.
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

		// Cron Schedules.
		add_filter( 'cron_schedules', array( $this, 'register_cron_schedules' ) );

		// Link remote products function to cronjob.
		add_action( 'sympose_refresh_extensions', array( $this, 'get_sympose_extensions' ) );

		// Add Custom row actions.
		add_action( 'event_row_actions', array( $this, 'add_row_actions' ), 10, 2 );

		// Sympose Submenu Pages.
		add_action( 'admin_menu', array( $this, 'admin_sub_menu' ), 20, 2 );

		// Add settings link to plugin actions.
		add_filter( 'plugin_action_links_sympose/sympose.php', array( $this, 'plugin_row_actions' ), 10, 2 );

		// Order session on event/day and time.
		add_filter( 'edited_event', array( $this, 'republish_sessions' ), 20, 2 );

		// Order session on event/day and time.
		add_filter( 'admin_footer', array( $this, 'maybe_show_setup_wizard' ), 20, 2 );

		// Add logic to disable Quick Start.
		add_filter( 'admin_notices', array( $this, 'disable_quick_start_notice' ), 20, 2 );

		// Mark current submenu.
		add_filter( 'parent_file', array( $this, 'highlight_parent_menu_item' ), 20, 1 );

		// Mark current submenu.
		add_filter( 'submenu_file', array( $this, 'highlight_sub_menu_item' ), 20, 1 );

		// Mark current submenu.
		add_filter( 'admin_notices', array( $this, 'validate_sympose_license' ), 20, 1 );
	}

	/**
	 * Register post types
	 *
	 * @since       1.0.0
	 */
	public function register_post_types() {

		$labels = array();

		$labels['session']      = array(
			'name'               => _x( 'Sessions', 'post type general name', 'sympose' ),
			'singular_name'      => _x( 'Session', 'post type singular name', 'sympose' ),
			'menu_name'          => _x( 'Sessions', 'admin menu', 'sympose' ),
			'name_admin_bar'     => _x( 'Session', 'add new on admin bar', 'sympose' ),
			'add_new'            => _x( 'Add New', 'session', 'sympose' ),
			'add_new_item'       => __( 'Add New Session', 'sympose' ),
			'new_item'           => __( 'New Session', 'sympose' ),
			'edit_item'          => __( 'Edit Session', 'sympose' ),
			'view_item'          => __( 'View Session', 'sympose' ),
			'all_items'          => __( 'All Sessions', 'sympose' ),
			'search_items'       => __( 'Search Sessions', 'sympose' ),
			'parent_item_colon'  => __( 'Parent Sessions:', 'sympose' ),
			'not_found'          => __( 'No sessions found.', 'sympose' ),
			'not_found_in_trash' => __( 'No sessions found in Trash.', 'sympose' ),
		);
		$labels['organisation'] = array(
			'name'               => _x( 'Organisations', 'post type general name', 'sympose' ),
			'singular_name'      => _x( 'Organisation', 'post type singular name', 'sympose' ),
			'menu_name'          => _x( 'Organisations', 'admin menu', 'sympose' ),
			'name_admin_bar'     => _x( 'Organisation', 'add new on admin bar', 'sympose' ),
			'add_new'            => _x( 'Add New', 'organisation', 'sympose' ),
			'add_new_item'       => __( 'Add New Organisation', 'sympose' ),
			'new_item'           => __( 'New Organisation', 'sympose' ),
			'edit_item'          => __( 'Edit Organisation', 'sympose' ),
			'view_item'          => __( 'View Organisation', 'sympose' ),
			'all_items'          => __( 'All Organisations', 'sympose' ),
			'search_items'       => __( 'Search Organisations', 'sympose' ),
			'parent_item_colon'  => __( 'Parent Organisations:', 'sympose' ),
			'not_found'          => __( 'No organisations found.', 'sympose' ),
			'not_found_in_trash' => __( 'No organisations found in Trash.', 'sympose' ),
		);
		$labels['person']       = array(
			'name'               => _x( 'People', 'post type general name', 'sympose' ),
			'singular_name'      => _x( 'Person', 'post type singular name', 'sympose' ),
			'menu_name'          => _x( 'People', 'admin menu', 'sympose' ),
			'name_admin_bar'     => _x( 'Person', 'add new on admin bar', 'sympose' ),
			'add_new'            => _x( 'Add New', 'person', 'sympose' ),
			'add_new_item'       => __( 'Add New Person', 'sympose' ),
			'new_item'           => __( 'New Person', 'sympose' ),
			'edit_item'          => __( 'Edit Person', 'sympose' ),
			'view_item'          => __( 'View Person', 'sympose' ),
			'all_items'          => __( 'All People', 'sympose' ),
			'search_items'       => __( 'Search People', 'sympose' ),
			'parent_item_colon'  => __( 'Parent People:', 'sympose' ),
			'not_found'          => __( 'No people found.', 'sympose' ),
			'not_found_in_trash' => __( 'No people found in Trash.', 'sympose' ),
		);

		$labels['event'] = array(
			'name'              => _x( 'Events', 'taxonomy general name', 'sympose' ),
			'singular_name'     => _x( 'Event', 'taxonomy singular name', 'sympose' ),
			'search_items'      => __( 'Search Events', 'sympose' ),
			'all_items'         => __( 'All Events', 'sympose' ),
			'parent_item'       => __( 'Parent Event', 'sympose' ),
			'parent_item_colon' => __( 'Parent Event:', 'sympose' ),
			'edit_item'         => __( 'Edit Event', 'sympose' ),
			'update_item'       => __( 'Update Event', 'sympose' ),
			'add_new_item'      => __( 'Add New Event', 'sympose' ),
			'new_item_name'     => __( 'New Event Name', 'sympose' ),
			'menu_name'         => __( 'Events', 'sympose' ),
		);

		$session_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgZmlsbD0iIzgyODc4YyIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTcgM3YtMmMwLS41NTIuNDQ3LTEgMS0xczEgLjQ0OCAxIDF2MmMwIC41NTItLjQ0NyAxLTEgMXMtMS0uNDQ4LTEtMXptLTEyIDFjLjU1MyAwIDEtLjQ0OCAxLTF2LTJjMC0uNTUyLS40NDctMS0xLTEtLjU1MyAwLTEgLjQ0OC0xIDF2MmMwIC41NTIuNDQ3IDEgMSAxem0xMyAxM3YtM2gtMXY0aDN2LTFoLTJ6bS01IC41YzAgMi40ODEgMi4wMTkgNC41IDQuNSA0LjVzNC41LTIuMDE5IDQuNS00LjUtMi4wMTktNC41LTQuNS00LjUtNC41IDIuMDE5LTQuNSA0LjV6bTExIDBjMCAzLjU5LTIuOTEgNi41LTYuNSA2LjVzLTYuNS0yLjkxLTYuNS02LjUgMi45MS02LjUgNi41LTYuNSA2LjUgMi45MSA2LjUgNi41em0tMTQuMjM3IDMuNWgtNy43NjN2LTEzaDE5djEuNzYzYy43MjcuMzMgMS4zOTkuNzU3IDIgMS4yNjh2LTkuMDMxaC0zdjFjMCAxLjMxNi0xLjI3OCAyLjMzOS0yLjY1OCAxLjg5NC0uODMxLS4yNjgtMS4zNDItMS4xMTEtMS4zNDItMS45ODR2LS45MWgtOXYxYzAgMS4zMTYtMS4yNzggMi4zMzktMi42NTggMS44OTQtLjgzMS0uMjY4LTEuMzQyLTEuMTExLTEuMzQyLTEuOTg0di0uOTFoLTN2MjFoMTEuMDMxYy0uNTExLS42MDEtLjkzOC0xLjI3My0xLjI2OC0yeiIvPjwvc3ZnPg==';

		register_post_type(
			'session',
			array(
				'labels'            => $labels['session'],
				'public'            => true,
				'has_archive'       => false,
				'menu_icon'         => $session_icon,
				'supports'          => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_in_menu'      => false,
				'show_in_admin_bar' => true,
			)
		);

		$organisation_icon = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgZmlsbC1ydWxlPSJldmVub2RkIiBmaWxsPSIjODI4NzhjIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0yNCAyNGgtMjN2LTE2aDZ2LThoMTF2MTJoNnYxMnptLTEyLTVoLTN2NGgzdi00em00IDBoLTN2NGgzdi00em02IDBoLTJ2NGgydi00em0tMTcgMGgtMnY0aDJ2LTR6bTExLTVoLTJ2Mmgydi0yem0tNSAwaC0ydjJoMnYtMnptMTEgMGgtMnYyaDJ2LTJ6bS0xNyAwaC0ydjJoMnYtMnptMTEtNGgtMnYyaDJ2LTJ6bS01IDBoLTJ2Mmgydi0yem0tNiAwaC0ydjJoMnYtMnptMTEtNGgtMnYyaDJ2LTJ6bS01IDBoLTJ2Mmgydi0yem01LTRoLTJ2Mmgydi0yem0tNSAwaC0ydjJoMnYtMnoiLz48L3N2Zz4=';

		register_post_type(
			'organisation',
			array(
				'labels'            => $labels['organisation'],
				'public'            => true,
				'has_archive'       => false,
				'menu_icon'         => $organisation_icon,
				'hierarchical'      => true,
				'supports'          => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
				'show_in_rest'      => true,
				'show_in_menu'      => false,
				'show_in_admin_bar' => true,
			)
		);

		$person_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgZmlsbD0iIzgyODc4YyIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMjAuODIyIDE4LjA5NmMtMy40MzktLjc5NC02LjY0MS0xLjQ5LTUuMDktNC40MTguNC0uNzU2Ljc0LTEuNDgxIDEuMDI3LTIuMTc4LS44NjYuNDk2LTEuODMzLjg4LTIuMzU5IDEuMDExbC0uMjI0LTEuMDY2YzEuMTQ4LS4zMjggMi4zODgtLjkzOSAzLjI1Mi0xLjgzOCAxLjgzNi02LjI4My0xLjI2Ny05LjYwNy01LjQyOC05LjYwNy01LjA4MiAwLTguNDY1IDQuOTQ5LTMuNzMyIDEzLjY3OCAxLjU5OCAyLjk0NS0xLjcyNSAzLjY0MS01LjA5IDQuNDE4LTIuOTc5LjY4OC0zLjE3OCAyLjE0My0zLjE3OCA0LjY2M2wuMDA1IDEuMjQxaDIzLjk5bC4wMDUtMS4yNDFjMC0yLjUyLS4xOTktMy45NzUtMy4xNzgtNC42NjN6bS04LjgxNC00Ljc3NmMtLjQ0MS4wOTEtLjg3My0uMTk0LS45NjMtLjYzNi0uMDkyLS40NDIuMTkzLS44NzQuNjM0LS45NjVsMS43NTMtLjM4OS4zMjkgMS42LTEuNzUzLjM5eiIvPjwvc3ZnPg==';

		register_post_type(
			'person',
			array(
				'labels'            => $labels['person'],
				'public'            => true,
				'has_archive'       => false,
				'menu_icon'         => $person_icon,
				'hierarchical'      => true,
				'supports'          => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
				'show_in_rest'      => true,
				'show_in_menu'      => false,
				'show_in_admin_bar' => true,
			)
		);

		register_taxonomy(
			'person-category',
			array( 'person' ),
			array(
				'hierarchical' => true,
				'label'        => __( 'Category', 'sympose' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			'organisation-category',
			array( 'organisation' ),
			array(
				'hierarchical' => true,
				'label'        => __( 'Category', 'sympose' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			'session-status',
			array( 'session' ),
			array(
				'hierarchical' => true,
				'label'        => __( 'Status', 'sympose' ),
				'public'       => false,
			)
		);

		register_taxonomy(
			'event',
			array( 'session', 'organisation', 'person' ),
			apply_filters(
				'sympose_customize_event_taxonomy',
				array(
					'hierarchical' => true,
					'labels'       => $labels['event'],
					'public'       => false,
					'show_ui'      => true,
					'show_in_rest' => true,
				)
			)
		);
	}

	/**
	 *  Register REST Routes for admin
	 *
	 * @since   1.0.0
	 */
	public function register_rest_routes() {
		register_rest_route(
			'sympose/v1',
			'/generate_sample_data/',
			array(
				'methods'  => array( 'GET', 'POST' ),
				'callback' => array( $this, 'create_sample_data' ),
			)
		);
		register_rest_route(
			'sympose/v1',
			'/quick_start_event',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'process_quick_start' ),
			)
		);
	}

	/**
	 * Create Sample Data
	 *
	 * @param object $request The request object.
	 *
	 * @return  array returns the status.
	 *
	 * @throwns Nothing.
	 * @since   1.1.1
	 */
	public function create_sample_data( $request ) {

		if ( ! is_user_logged_in() ) {
			return array(
				'status'  => 400,
				'message' => 'You\'re not logged in..',
			);
		}

		$output = array(
			'status'  => 200,
			'message' => __( 'Sample data generated successfully!', 'sympose' ),
		);

		$event_name = get_bloginfo( 'name' );

		if ( term_exists( $event_name, 'event' ) ) {
			for ( $i = 1; $i < 99; $i ++ ) {
				if ( ! term_exists( $event_name . '-' . $i, 'event' ) ) {
					$event_name = $event_name . '-' . $i;
					break;
				}
			}
		}

		$date = new Datetime();

		$event = wp_insert_term(
			$event_name,
			'event'
		);

		if ( is_wp_error( $event ) ) {
			return array(
				'status'  => 400,
				'message' => __( 'Creating sample data failed. There is an event with the same name.', 'sympose' ),
			);
		}

		// Set event date.
		update_term_meta( $event['term_id'], $this->prefix . 'event_date', $date->getTimestamp() );

		$days = array();

		for ( $i = 1; $i < 3 + 1; $i ++ ) {

			$day = wp_insert_term(
				'Day ' . $i,
				'event',
				array(
					'parent' => $event['term_id'],
				)
			);

			// Set day date.
			update_term_meta( $day['term_id'], $this->prefix . 'event_date', $date->getTimestamp() );

			$days[] = $day;

			// Increase start date.
			$date->modify( ' +1 day' );
		}

		// Set event date.
		update_term_meta( $event['term_id'], $this->prefix . 'event_date', time() );

		$event_term = get_term( $event['term_id'] );

		if ( ! term_exists( 'speakers', 'person-category' ) ) {
			$speakers_category = wp_insert_term(
				'Speakers',
				'person-category'
			);

			$speakers_category_term = get_term( $speakers_category['term_id'] );
		} else {
			$speakers_category_term = get_term_by( 'slug', 'speakers', 'person-category' );
		}

		if ( ! term_exists( 'sponsors', 'organisation-category' ) ) {
			$sponsors_category = wp_insert_term(
				'Sponsors',
				'organisation-category'
			);

			$sponsors_category_term = get_term( $sponsors_category['term_id'] );
		} else {
			$sponsors_category_term = get_term_by( 'slug', 'sponsors', 'organisation-category' );
		}

		$agenda_page = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => __( 'Schedule', 'sympose' ),
				'post_content' => '[sympose type="schedule" event="' . esc_html( $event_term->slug ) . '"]',
				'post_status'  => 'publish',
			)
		);

		if ( ! is_wp_error( $agenda_page ) ) {
			$output['message'] .= ' <a href="' . get_permalink( $agenda_page ) . '">' . __( 'Go to the event schedule', 'sympose' ) . '</a>';
		}

		// Set schedule page for events terms.
		foreach ( $days as $day ) {
			update_term_meta( $day['term_id'], $this->prefix . 'schedule_page_id', $agenda_page );
		}

		update_term_meta( $event['term_id'], $this->prefix . 'schedule_page_id', $agenda_page );

		$speakers_page = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => __( 'Speakers', 'sympose' ),
				'post_content' => '[sympose type="person" category="' . esc_html( $speakers_category_term->slug ) . '" event="' . esc_html( $event_term->slug ) . '" description="true" name="true"]',
				'post_status'  => 'publish',
			)
		);

		$sponsors_page = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => __( 'Sponsors', 'sympose' ),
				'post_content' => '[sympose type="organisation" category="' . esc_html( $sponsors_category_term->slug ) . '" event="' . esc_html( $event_term->slug ) . '" name="true"]',
				'post_status'  => 'publish',
			)
		);

		$sponsors = array();

		for ( $i = 1; $i < 11; $i ++ ) {

			$sponsor_content = $this->get_random_text();
			$content_array   = explode( ' ', $sponsor_content );
			$sponsor_name    = ucwords( preg_replace( '/[^a-zA-Z ]+/', '', implode( ' ', array_rand( array_flip( $content_array ), 2 ) ) ) );

			$sponsor = wp_insert_post(
				array(
					'post_type'    => 'organisation',
					'post_title'   => $sponsor_name,
					'post_content' => $sponsor_content,
					'post_status'  => 'publish',
				)
			);

			wp_set_object_terms( $sponsor, $event['term_id'], 'event', true );
			wp_set_object_terms( $sponsor, $sponsors_category_term->term_id, 'organisation-category', true );

			$sponsors[] = $sponsor;
		}

		$random_users = $this->get_random_users( 20 );
		$speakers     = array();
		if ( $random_users ) {
			foreach ( $random_users as $user ) {
				$speaker = wp_insert_post(
					array(
						'post_type'    => 'person',
						'post_title'   => ucfirst( $user->name->first ) . ' ' . ucfirst( $user->name->last ),
						'post_content' => $this->get_random_text(),
						'post_status'  => 'publish',
						'meta_input'   => array(
							'_sympose_email' => $user->email,
						),
					)
				);

				wp_set_object_terms( $speaker, $event['term_id'], 'event', true );
				wp_set_object_terms( $speaker, $speakers_category_term->term_id, 'person-category', true );

				$speakers[] = $speaker;

				$this->upload_set_featured_image( $user->picture->large, $speaker );

				// Fire action for extensions to hook into.
				do_action( 'sympose_random_user_import', $speaker, $user );
			}
		}

		foreach ( $days as $event_day ) {

			$start_time = new Datetime( 'today' );
			$start_time->setTime( 8, 00 );

			for ( $i = 1; $i < 11; $i ++ ) {

				$start_time->modify( '+1 hour' );

				$end_time = clone $start_time;
				$end_time->modify( '+30 minutes' );

				$post_content  = $this->get_random_text();
				$content_array = explode( ' ', $post_content );

				$session_id = wp_insert_post(
					array(
						'post_type'    => 'session',
						'post_title'   => ucfirst( preg_replace( '/[^a-zA-Z ]+/', '', implode( ' ', array_rand( array_flip( $content_array ), 5 ) ) ) ),
						'post_content' => $post_content,
						'post_status'  => 'publish',
						'meta_input'   => array(
							$this->prefix . 'session_start' => $start_time->format( 'H:i' ),
							$this->prefix . 'session_end' => $end_time->format( 'H:i' ),
							$this->prefix . 'session_people' => array_rand( array_flip( $speakers ), 2 ),
							$this->prefix . 'session_organisations' => array_rand( array_flip( $sponsors ), 2 ),
						),
					)
				);

				// Set event and days.
				wp_set_object_terms( $session_id, $event['term_id'], 'event', true );
				wp_set_object_terms( $session_id, $event_day['term_id'], 'event', true );
			}
		}

		// Add session info to session sidebar.
		$sidebar_widgets = get_option( 'sidebars_widgets' );
		if ( ! isset( $sidebar_widgets['sympose-session-sidebar'] ) ) {
			$sidebar_widgets['sympose-session-sidebar'] = array();
		}
		$session_sidebar = $sidebar_widgets['sympose-session-sidebar'];

		if ( empty( $session_sidebar ) || ! in_array( 'sympose_session_information-2', $session_sidebar, true ) ) {
			$sidebar_widgets['sympose-session-sidebar'][] = 'sympose_session_information-2';

			// Ok, good - now update the option for this widget.
			update_option(
				'widget_sympose_session_information',
				array(
					2              => array(
						'title' => '',
					),
					'_multiwidget' => 1,
				)
			);

		}

		update_option( 'sidebars_widgets', $sidebar_widgets );

		if ( is_wp_error( $agenda_page ) || is_wp_error( $speakers_page ) || is_wp_error( $sponsors_page ) || is_wp_error( $event ) ) {
			$output['status']  = 400;
			$output['message'] = __( 'An error occured while creating sample data', 'sympose' );
		} else {
			$options                      = get_option( 'sympose' );
			$options['show_setup_wizard'] = false;

			update_option( 'sympose', $options );
		}

		return $output;
	}

	/**
	 * Order sessions by time in admin
	 *
	 * @param object $query run the function with a custom query.
	 *
	 * @return  object returns the customized query.
	 * @since   1.0.0
	 */
	public function order_by_time( $query ) {
		if ( ! is_admin() ) {
			return $query;
		}

		if ( $query->get( 'post_type' ) !== 'session' ) {
			return $query;
		}

		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'ASC' );

		return $query;
	}

	/**
	 * Remove date filter
	 *
	 * @since       1.0.0
	 */
	public function remove_date_filter() {
		if ( get_post_type() === 'session' ) {
			add_filter( 'months_dropdown_results', '__return_empty_array' );
		}
	}

	/**
	 * Adds filters to WP Dashboard
	 *
	 * @param string $post_type the post type.
	 *
	 * @since   1.0.0
	 */
	public function manage_filters( $post_type ) {

		$taxonomies   = array();
		$current_term = false;

		$queried_object = get_queried_object();

		if ( is_object( $queried_object ) && property_exists( $queried_object, 'term_id' ) ) {
			$current_term = $queried_object->slug;
		}

		switch ( $post_type ) {
			case 'session':
				$taxonomies = array( 'event' );
				break;

			case 'person':
				$taxonomies = array( 'person-category' );
				break;

			default:
				break;
		}

		foreach ( $taxonomies as $taxonomy_slug ) {

			// Retrieve taxonomy data.
			$taxonomy_obj  = get_taxonomy( $taxonomy_slug );
			$taxonomy_name = $taxonomy_obj->labels->name;

			// Retrieve taxonomy terms.
			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'parent'   => 0,
				)
			);

			// Display filter HTML.
			echo "<select name='" . esc_attr( $taxonomy_slug ) . "' id='" . esc_attr( $taxonomy_slug ) . "' class='postform'>";

			/* translators: %s is the taxonomy name */
			echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'sympose' ), esc_html( $taxonomy_name ) ) . '</option>';

			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
					esc_html( $term->slug ),
					( ( isset( $current_term ) && ( $current_term === $term->slug ) ) ? ' selected="selected"' : '' ),
					esc_html( $term->name ),
					esc_html( $term->count )
				);
				$child_terms = get_terms(
					array(
						'taxonomy' => esc_html( $taxonomy_slug ),
						'parent'   => esc_html( $term->term_id ),
					)
				);
				foreach ( $child_terms as $term ) {
					printf(
						'<option value="%1$s" %2$s>-- %3$s (%4$s)</option>',
						esc_html( $term->slug ),
						( ( isset( $current_term ) && ( $current_term === $term->slug ) ) ? ' selected="selected"' : '' ),
						esc_html( $term->name ),
						esc_html( $term->count )
					);
				}
			}
			echo '</select>';
		}
	}


	/**
	 * Register image sizes
	 *
	 * @since        1.0.0
	 */
	public function add_image_sizes() {
		add_image_size( 'person-small', 100, 100, true );
		add_image_size( 'person-medium', 150, 220, true );
		add_image_size( 'person-schedule', 50, 50, true );

		add_image_size( 'organisation-small', 100, 100, false );
		add_image_size( 'organisation-medium', 130, 80, false );
		add_image_size( 'organisation-schedule', 65, 40, false );
	}

	/**
	 * Manage session columns
	 *
	 * @param array $columns The column that will be processed.
	 *
	 * @return   array returns filtered columns
	 * @since    1.0.0
	 */
	public function session_columns( $columns ) {
		unset( $columns['date'] );
		$columns['time'] = __( 'Time', 'sympose' );
		$columns['day']  = __( 'Day', 'sympose' );

		$columns = apply_filters( 'sympose_session_columns', $columns );

		$columns['people']        = __( 'People', 'sympose' );
		$columns['organisations'] = __( 'Organisations', 'sympose' );

		$columns['event'] = __( 'Event', 'sympose' );

		return $columns;
	}

	/**
	 * Session Column content
	 *
	 * @param array $column array with the column to be processed.
	 *
	 * @return array data for the column.
	 * @since   1.0.0
	 */
	public function column_content( $column ) {
		$post_id    = get_the_ID();
		$event      = wp_get_object_terms(
			$post_id,
			'event',
			array(
				'orderby' => 'parent',
				'parent'  => 0,
			)
		);
		$main_event = reset( $event );
		switch ( $column ) {
			case 'event':
				if ( ! $main_event ) {
					break;
				}
				echo '<a href="' . esc_url( add_query_arg( 'event', $main_event->slug ) ) . '">' . esc_html( $main_event->name ) . '</a>';
				break;
			case 'day':
				if ( ! $main_event ) {
					break;
				}

				$days = wp_get_object_terms(
					$post_id,
					'event',
					array(
						'orderby'  => 'parent',
						'child_of' => $main_event->term_id,
					)
				);
				foreach ( $days as $key => $day ) {
					echo '<a href="' . esc_url( add_query_arg( 'event', $day->slug ) ) . '">' . esc_html( $day->name ) . '</a>';
					if ( 0 <= $key && count( $days ) - 1 !== $key ) {
						echo ', ';
					}
				}
				break;
			case 'time':
				echo esc_html( get_post_meta( $post_id, $this->prefix . 'session_start', true ) ) . ' - ' . esc_html( get_post_meta( $post_id, $this->prefix . 'session_end', true ) );
				break;
			case 'person-category':
				$category      = wp_get_object_terms( $post_id, 'person-category' );
				$main_category = reset( $category );
				if ( $main_category ) {
					echo '<a href="' . esc_url( add_query_arg( 'person-category', $main_category->slug ) ) . '">' . esc_html( $main_category->name ) . '</a>';
				}
				break;
			case 'organisation-category':
				$category      = wp_get_object_terms( $post_id, 'organisation-category' );
				$main_category = reset( $category );
				if ( $main_category ) {
					echo '<a href="' . esc_url( add_query_arg( 'organisation-category', $main_category->slug ) ) . '">' . esc_html( $main_category->name ) . '</a>';
				}
				break;
			case 'image':
				$image_id  = sympose_get_image( $post_id );
				$post_type = get_post_type( $post_id );
				echo wp_get_attachment_image( $image_id, $post_type . '-schedule' );
				break;
			case 'people':
				$people = get_post_meta( $post_id, $this->prefix . 'session_people', true );

				if ( is_array( $people ) ) {
					// phpcs:disable
					$sympose_public = new Sympose_Public();

					echo '<div class="sym-list">';
					foreach ( $people as $id ) {
						$args = array(
							'name' => true,
							'desc' => false,
							'size' => 'person-schedule',
						);

						$image = sympose_get_image( get_post( $id ) );
						if ( $image ) {
							$args = array_merge( $args, array( 'name' => false ) );
						}

						echo $sympose_public->render_item( $id, $args, true );
					}
					echo '</div>';
					// phpcs:enable
				}

				break;

			case 'organisations':
				$organisations = get_post_meta( $post_id, $this->prefix . 'session_organisations', true );

				if ( is_array( $organisations ) ) {
					// phpcs:disable
					$sympose_public = new Sympose_Public();

					echo '<div class="sym-list">';

					foreach ( $organisations as $id ) {
						$image = sympose_get_image( get_post( $id ) );
						$args  = array(
							'name' => true,
							'desc' => false,
							'size' => 'organisation-schedule',
						);
						if ( $image ) {
							$args['name'] = false;
						}

						echo $sympose_public->render_item( $id, $args, true );
					}
					echo '</div>';
					// phpcs:enable
				}
		}

		return apply_filters( 'sympose_session_column_content', $column, $post_id );

	}

	/**
	 * Manage Person columns
	 *
	 * @param array $columns array of columns.
	 *
	 * @return  array array of columns.
	 * @since    1.0.0
	 */
	public function person_columns( $columns ) {
		$title = $columns['title'];
		unset( $columns['date'] );
		unset( $columns['title'] );

		$columns['image']           = '<span class="dashicons dashicons-format-image"></span>';
		$columns['title']           = $title;
		$columns['person-category'] = __( 'Category', 'sympose' );
		$columns['event']           = __( 'Event', 'sympose' );

		return $columns;
	}

	/**
	 * Manage Organisation columns
	 *
	 * @param array $columns array of columns.
	 *
	 * @return  array array of columns.
	 * @since    1.0.0
	 */
	public function organisation_columns( $columns ) {
		$title = $columns['title'];
		unset( $columns['date'] );
		unset( $columns['title'] );

		$columns['image']                 = '<span class="dashicons dashicons-format-image"></span>';
		$columns['title']                 = $title;
		$columns['organisation-category'] = __( 'Category', 'sympose' );
		$columns['event']                 = __( 'Event', 'sympose' );

		return $columns;
	}

	/**
	 * Register custom fields with CMB2
	 *
	 * @since    1.0.0
	 */
	public function register_custom_fields() {

		$general = new_cmb2_box(
			array(
				'id'           => $this->prefix . 'data',
				'title'        => __( 'Details', 'sympose' ),
				'object_types' => array( 'organisation', 'person' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$general->add_field(
			array(
				'name'         => __( 'Short description', 'sympose' ),
				'type'         => 'text',
				'object_types' => array( 'person' ),
				'id'           => $this->prefix . 'description',
			)
		);

		$general->add_field(
			array(
				'name'         => __( 'Image', 'sympose' ),
				'type'         => 'file',
				'object_types' => array( 'organisation', 'person' ),
				'id'           => $this->prefix . 'image',
			)
		);

		do_action( 'sympose_register_general_custom_fields', $general );

		$sessions = new_cmb2_box(
			array(
				'id'           => $this->prefix . 'sessions_fields',
				'title'        => __( 'Session Details', 'sympose' ),
				'object_types' => array( 'session' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$sessions->add_field(
			array(
				'name'        => __( 'Start', 'sympose' ),
				'type'        => 'text_time',
				'id'          => $this->prefix . 'session_start',
				'time_format' => get_option( 'time_format' ),
				'default'     => '00:00',
				'attributes'  => array(
					'required' => 'required',
				),
			)
		);

		$sessions->add_field(
			array(
				'name'        => __( 'End', 'sympose' ),
				'type'        => 'text_time',
				'id'          => $this->prefix . 'session_end',
				'time_format' => get_option( 'time_format' ),
				'default'     => '00:00',
				'attributes'  => array(
					'required' => 'required',
				),
			)
		);

		$sessions->add_field(
			array(
				'name'        => __( 'Mark as static session', 'sympose' ),
				'type'        => 'checkbox',
				'id'          => $this->prefix . 'session_static',
				'time_format' => get_option( 'time_format' ),
				'desc'        => __( 'Static sessions show in the schedule without a link to the session page. (This can be used for breaks or registration)', 'sympose' ),
			)
		);

		$sessions->add_field(
			array(
				'name'        => __( 'People', 'sympose' ),
				'type'        => 'multicheck',
				'id'          => $this->prefix . 'session_people',
				'classes'     => 'sympose-session-people sortable',
				'options_cb'  => function () {
					$people  = get_posts(
						array(
							'post_type'   => 'person',
							'numberposts' => - 1,
						)
					);
					$options = array();
					foreach ( $people as $person ) {

						$edit_link  = get_edit_post_link( $person->ID );
						$link_title = __( 'Go to', 'sympose' ) . ' ' . $person->post_title . ' ' . __( 'profile', 'sympose' );

						$options[ $person->ID ] = $person->post_title . ' <small><i><a target="_blank" title="' . $link_title . '" href="' . $edit_link . '">' . __( 'edit', 'sympose' ) . '</a></i></small>';
					}

					return $options;
				},
				'after_field' => function () {
					echo '<p><a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=person' ) ) . '">' . esc_html__( 'Create new person', 'sympose' ) . '</a></p>';
				},
				'description' => __( 'Tip: You can change the order of this list by dragging the items', 'sympose' ),
			)
		);

		$sessions->add_field(
			array(
				'name'        => __( 'Organisations', 'sympose' ),
				'type'        => 'multicheck',
				'id'          => $this->prefix . 'session_organisations',
				'classes'     => 'sympose-session-organisations sortable',
				'options_cb'  => function () {
					$organisations = get_posts(
						array(
							'post_type'   => 'organisation',
							'numberposts' => - 1,
						)
					);

					$options = array();
					foreach ( $organisations as $organisation ) {

						$edit_link  = get_edit_post_link( $organisation->ID );
						$link_title = __( 'Go to', 'sympose' ) . ' ' . $organisation->post_title . ' ' . __( 'profile', 'sympose' );

						$options[ $organisation->ID ] = $organisation->post_title . ' <small><i><a target="_blank" title="' . $link_title . '" href="' . $edit_link . '">' . __( 'edit', 'sympose' ) . '</a></i></small>';
					}

					return $options;
				},
				'after_field' => function () {
					echo '<p><a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=organisation' ) ) . '">' . esc_html__( 'Create new organisation', 'sympose' ) . '</a></p>';
				},
			)
		);

		do_action( 'sympose_register_session_custom_fields', $sessions );

		$organisation = new_cmb2_box(
			array(
				'id'           => $this->prefix . 'organisation_fields',
				'title'        => __( 'People', 'sympose' ),
				'object_types' => array( 'organisation' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$organisation->add_field(
			array(
				'name'        => __( 'People', 'sympose' ),
				'type'        => 'multicheck',
				'id'          => $this->prefix . 'organisation_people',
				'options_cb'  => function () {

					$people = get_posts(
						array(
							'post_type'   => 'person',
							'numberposts' => - 1,
						)
					);

					$options = array();
					foreach ( $people as $person ) {
						$options[ $person->ID ] = $person->post_title;
					}

					return $options;
				},
				'after_field' => function () {
					echo '<p><a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=person' ) ) . '">' . esc_html__( 'Create new person', 'sympose' ) . '</a></p>';
				},
			)
		);

		$category = new_cmb2_box(
			array(
				'id'           => $this->prefix . 'event_fields',
				'title'        => __( 'Details', 'sympose' ),
				'object_types' => array( 'term' ),
				'taxonomies'   => array( 'event' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		$category->add_field(
			array(
				'name'       => __( 'Start date', 'sympose' ),
				'type'       => 'text_date_timestamp',
				'id'         => $this->prefix . 'event_date',
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		$category->add_field(
			array(
				'name'        => __( 'Schedule Page', 'sympose' ),
				'type'        => 'select',
				'id'          => $this->prefix . 'schedule_page_id',
				'options_cb'  => function () {
					$parent_pages = get_pages(
						array(
							'parent'      => 0,
							'numberposts' => - 1,
						)
					);
					$output       = array();
					$output[0]    = __( '-- Select an option --', 'sympose' );
					foreach ( $parent_pages as $page ) {
						$output[ $page->ID ] = $page->post_title;
						$children            = get_pages(
							array(
								'child_of' => $page->ID,
							)
						);
						foreach ( $children as $child ) {
							$output[ $child->ID ] = '-- ' . $child->post_title;
						}
					}

					return $output;
				},
				'after_field' => function( $field_args, $field ) {
					echo '<br/><span style="display: block; text-align: right; margin-top: 2px;"><a href="' . esc_url( get_permalink( absint( $field->value ) ) ) . '">';
					esc_html__( 'Go to page', 'sympose' );
					echo '</a></span>';
				},
			)
		);

		$category->add_field(
			array(
				'name'       => __( 'Display people with', 'sympose' ),
				'type'       => 'select',
				'default_cb' => function() {
					return sympose_get_option( 'schedule_people_format' );
				},
				'show_on_cb' => function() {
					return ( sympose_get_option( 'show_people_on_schedule' ) === 'on' ? true : false );
				},
				'id'         => $this->prefix . 'schedule_people_format',
				'desc'       => __( 'How would you like people to show on the schedule?', 'sympose' ),
				'options'    => array(
					'default'    => __( 'Default', 'sympose' ),
					'photo'      => __( 'Photo only', 'sympose' ),
					'name'       => __( 'Name only', 'sympose' ),
					'photo_name' => __( 'Photo & name', 'sympose' ),
				),
			)
		);

		$category->add_field(
			array(
				'name'       => __( 'Display organisations with', 'sympose' ),
				'type'       => 'select',
				'default_cb' => function() {
					return sympose_get_option( 'schedule_organisations_format' );
				},
				'show_on_cb' => function() {
					return ( sympose_get_option( 'show_organisations_on_schedule' ) === 'on' ? true : false );
				},
				'id'         => $this->prefix . 'schedule_organisations_format',
				'desc'       => __( 'How would you like organisations to show on the schedule?', 'sympose' ),
				'options'    => array(
					'default'   => __( 'Default', 'sympose' ),
					'logo'      => __( 'Photo only', 'sympose' ),
					'name'      => __( 'Name only', 'sympose' ),
					'logo_name' => __( 'Logo & name', 'sympose' ),
				),
			)
		);

		do_action( 'sympose_event_category_custom_fields', $category );
	}

	/**
	 * Register settings page and custom fields
	 *
	 * #since   1.0.0
	 */
	public function settings_page() {

		$options = new_cmb2_box(
			array(
				'id'           => $this->prefix . 'settings_box',
				'title'        => 'Sympose Settings',
				'menu_title'   => 'Sympose',
				'object_types' => array( 'options-page' ),
				'option_key'   => 'sympose',
				'position'     => 30,
			)
		);

		$options->add_field(
			array(
				'name' => __( 'General', 'sympose' ),
				'id'   => $this->prefix . 'settings_general_title',
				'type' => 'title',
			)
		);

		$options->add_field(
			array(
				'name'            => __( 'Enable CSS', 'sympose' ),
				'type'            => 'checkbox',
				'default'         => 'on',
				'id'              => 'enable_css',
				'sanitization_cb' => function ( $value, $field_args, $field ) {
					return is_null( $value ) ? 0 : $value;
				},
			)
		);

		$options->add_field(
			array(
				'id'            => $this->prefix . 'create_pages',
				'type'          => 'mk-render-row',
				'render_row_cb' => function () {
					?>
					<div class="cmb-row">
						<div class="cmb-th">
							<label for="_sympose_settings_create_pages">Generate sample content</label>
						</div>
						<div class="cmb-td">
							<p class="sympose-generate-sample-data">
								<?php
								submit_button(
									__( 'Generate Sample Data', 'sympose' ),
									'secondary',
									'sympose-generate-sample-data',
									false
								);
								?>
								<span class="spinner"></span>
							</p>
							<label for="_sympose_settings_create_pages">
								<span class="cmb2-metabox-description"><?php esc_html__( 'Create sample structure for a conference website.', 'sympose' ); ?></span>
							</label>
						</div>
					</div>
					<?php
				},
			)
		);

		$options->add_field(
			array(
				'name' => __( 'Schedule', 'sympose' ),
				'id'   => $this->prefix . 'settings_schedule',
				'type' => 'title',
			)
		);

		$options->add_field(
			array(
				'name'            => __( 'Show people on schedule', 'sympose' ),
				'type'            => 'checkbox',
				'default'         => '',
				'id'              => 'show_people_on_schedule',
				'sanitization_cb' => function ( $value, $field_args, $field ) {
					return is_null( $value ) ? false : $value;
				},
			)
		);

		$options->add_field(
			array(
				'name'    => __( 'Display people with', 'sympose' ),
				'type'    => 'select',
				'default' => '',
				'id'      => 'schedule_people_format',
				'desc'    => __( 'How would you like people to show on the schedule?', 'sympose' ),
				'options' => array(
					'photo'      => __( 'Photo only', 'sympose' ),
					'name'       => __( 'Name only', 'sympose' ),
					'photo_name' => __( 'Photo & name', 'sympose' ),
				),
			)
		);

		$options->add_field(
			array(
				'name'            => __( 'Show organisations on schedule', 'sympose' ),
				'type'            => 'checkbox',
				'default'         => '',
				'id'              => 'show_organisations_on_schedule',
				'sanitization_cb' => function ( $value, $field_args, $field ) {
					return is_null( $value ) ? false : $value;
				},
			)
		);

		$options->add_field(
			array(
				'name'    => __( 'Display organisations with', 'sympose' ),
				'type'    => 'select',
				'default' => '',
				'id'      => 'schedule_organisations_format',
				'desc'    => __( 'How would you like organisations to show on the schedule?', 'sympose' ),
				'options' => array(
					'logo'      => __( 'Photo only', 'sympose' ),
					'name'      => __( 'Logo only', 'sympose' ),
					'logo_name' => __( 'Logo & name', 'sympose' ),
				),
			)
		);

		$options->add_field(
			array(
				'name' => __( 'Sidebars', 'sympose' ),
				'id'   => $this->prefix . 'settings_sidebars',
				'type' => 'title',
			)
		);

		$options->add_field(
			array(
				'name'            => __( 'Overwrite sidebars?', 'sympose' ),
				'type'            => 'checkbox',
				'default'         => '',
				'id'              => 'overwrite_sidebars',
				'sanitization_cb' => function ( $value, $field_args, $field ) {
					return is_null( $value ) ? false : $value;
				},
			)
		);

		$options->add_field(
			array(
				'name'       => __( 'Which is the default sidebar?', 'sympose' ),
				'type'       => 'select',
				'default'    => '',
				'id'         => 'default_sidebar',
				'desc'       => __( 'In order to replace the sidebars, Sympose needs to know which sidebar displays by default.', 'sympose' ),
				'options_cb' => function () {
					global $wp_registered_sidebars;

					$output = array();

					if ( ! empty( $wp_registered_sidebars ) ) {
						foreach ( $wp_registered_sidebars as $sidebar ) {
							if ( strpos( $sidebar['id'], 'sympose-' ) === false ) {
								$output[ $sidebar['id'] ] = $sidebar['name'];
							}
						}
					} else {
						$output[0] = __( 'No sidebars available.', 'sympose' );
					}

					return $output;
				},
			)
		);

		$options->add_field(
			array(
				'name'            => __( 'Show widgets after post content', 'sympose' ),
				'type'            => 'checkbox',
				'default'         => '',
				'id'              => 'render_sidebars_after_content',
				'desc'            => __( 'Enable this option to display Sympose\'s widgets after the post content.', 'sympose' ),
				'sanitization_cb' => function ( $value, $field_args, $field ) {
					return is_null( $value ) ? false : $value;
				},
			)
		);

		do_action( 'sympose_register_settings_fields', $options );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->sympose, plugin_dir_url( dirname( __FILE__ ) ) . 'css/dist/admin/sympose.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->sympose,
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/admin/sympose.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'js',
			array(
				'jquery',
				'wp-api',
			),
			$this->version,
			false
		);

	}

	/**
	 * Save post Hook
	 *
	 * Sets the post published date to the day + time date
	 *
	 * @param int    $id Post ID.
	 * @param object $post Post object.
	 *
	 * @since        1.0.0
	 */
	public function save_post( $id, $post ) {

		if ( get_post_type( $id ) !== 'session' ) {
			return;
		}

		$start = get_post_meta( $id, $this->prefix . 'session_start', true );

		if ( empty( $start ) ) {
			return;
		}

		$end       = get_post_meta( $id, $this->prefix . 'session_end', true );
		$event_day = false;
		$event     = wp_get_object_terms( $id, 'event', array( 'parent' => 0 ) );

		if ( ! empty( $event ) ) {
			$event     = reset( $event );
			$event_day = get_term_meta( $event->term_id, $this->prefix . 'event_date', true );

			$event_children = get_term_children( $event->term_id, 'event' );

			foreach ( $event_children as $day ) {
				if ( has_term( $day, 'event', $post ) ) {
					$event_day = get_term_meta( $day, $this->prefix . 'event_date', true );
				}
			}
		}

		if ( empty( $event_day ) ) {
			return;
		}

		// Make time array and consider different time/formats.
		$start_array = explode( ':', gmdate( 'H:i', strtotime( $start ) ) );
		$datetime    = new Datetime();
		$datetime->setTimestamp( $event_day );

		$datetime->setTime( $start_array[0], $start_array[1] );

		$date = gmdate( 'Y-m-d H:i:s', $datetime->getTimestamp() );

		$date_gmt = get_gmt_from_date( current_time( 'mysql' ) );

		// Remove the action, avoid a loop.
		remove_action( 'save_post', array( $this, 'save_post' ), 20 );

		// Update the post.
		// We set post_date_gmt so that we prevent posts from getting scheduled.
		$update = wp_update_post(
			array(
				'ID'            => $id,
				'post_date'     => $date,
				'post_date_gmt' => $date_gmt,
			)
		);

		add_action( 'save_post', array( $this, 'save_post' ), 20, 2 );

	}


	/**
	 * Register Sidebars
	 *
	 * @since   1.0.5
	 */
	public function register_sidebars() {
		register_sidebar(
			array(
				'name'          => __( 'Sympose People', 'sympose' ),
				'id'            => 'sympose-person-sidebar',
				'description'   => __( 'Displays on person post types.', 'sympose' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widgettitle">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Sympose Organisations', 'sympose' ),
				'id'            => 'sympose-organisation-sidebar',
				'description'   => __( 'Displays on organisation post types.', 'sympose' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widgettitle">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => __( 'Sympose Sessions', 'sympose' ),
				'id'            => 'sympose-session-sidebar',
				'description'   => __( 'Displays on session post types.', 'sympose' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widgettitle">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Register Cron Schedules
	 *
	 * @since   1.0.9
	 */
	public function register_cron_schedules() {
		if ( ! isset( $schedules['minutely'] ) ) {
			$schedules['minutely'] = array(
				'interval' => 1 * 60,
				'display'  => __( 'Every minute', 'sympose' ),
			);
		}

		return $schedules;
	}

	/**
	 * Get remote extensions
	 *
	 * @since  1.0.10
	 */
	public function get_sympose_extensions() {
		// Get all products.
		$response = wp_remote_get( 'https://sympose.net/edd-api/v2/products/?number=99' );
		$body     = wp_remote_retrieve_body( $response );

		// Check for errors.
		if ( ! is_wp_error( $body ) ) {
			update_option( 'sympose_extensions', $body );
		}

	}

	/**
	 * Get Random Users from Random User API
	 *
	 * @param int $amount The amount of users to retrieve.
	 *
	 * @return  array|boolean array of users or false.
	 * @since   1.1.4
	 */
	public function get_random_users( $amount ) {

		// Connect to random user api.
		$request = wp_remote_get( 'https://randomuser.me/api/?results=' . $amount );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $request );

		$data = json_decode( $body );

		if ( isset( $data->results ) && ! empty( $data->results ) ) {
			return $data->results;
		} else {
			return false;
		}
	}

	/**
	 * Get Random Text
	 *
	 * @since   1.1.4
	 */
	public function get_random_text() {
		$request = wp_remote_get( 'https://loripsum.net/api?short' );
		$body    = wp_remote_retrieve_body( $request );

		return $body;
	}

	/**
	 * Upload and set featured image by url
	 *
	 * @param string $url URL of the image.
	 *
	 * @param int    $id The post to set the image for.
	 *
	 * @since   1.1.4
	 */
	public function upload_set_featured_image( $url, $id ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_sideload_image( $url, $id, '', 'id' );

		// Set URL for image custom field.
		update_post_meta( $id, $this->prefix . 'image', wp_get_attachment_url( $attachment_id ) );

		// Set ID for image custom field.
		update_post_meta( $id, $this->prefix . 'image_id', $attachment_id );
	}

	/**
	 * Add Row actions for events taxonomy
	 *
	 * @param array  $actions Array of existing actions.
	 *
	 * @param object $term The term object.
	 *
	 * @return  array  Updated array of actions.
	 * @since   1.2.0
	 */
	public function add_row_actions( $actions, $term ) {

		$page_id = sympose_get_schedule_page( $term );

		if ( ! empty( $page_id ) ) {
			$actions['go-to-schedule'] = '<a href="' . get_permalink( $page_id ) . '">' . __( 'Go to schedule', 'sympose' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Admin Sub Menu
	 */
	public function admin_sub_menu() {

		$svg_icon = $this->get_sympose_icon();

		$parent_slug = esc_html(
			add_query_arg(
				array(
					'post_type' => 'session',
				),
				'edit.php'
			)
		);

		add_menu_page(
			'Sympose',
			'Sympose',
			'manage_options',
			$parent_slug,
			false,
			$svg_icon,
			20
		);

		$submenu_pages = array(
			array(
				'page_title' => __( 'Sessions', 'sympose' ),
				'menu_title' => __( 'Sessions', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'post_type' => 'session',
						),
						'edit.php'
					)
				),
			),
			array(
				'page_title' => __( 'Events', 'sympose' ),
				'menu_title' => __( 'Events', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'taxonomy'  => 'event',
							'post_type' => 'session',
						),
						'edit-tags.php'
					)
				),
			),
			array(
				'page_title' => __( 'People', 'sympose' ),
				'menu_title' => __( 'People', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'post_type' => 'person',
						),
						'edit.php'
					)
				),
			),
			array(
				'page_title' => __( 'Person categories', 'sympose' ),
				'menu_title' => __( '&nbsp; &rdsh; Categories', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'taxonomy'  => 'person-category',
							'post_type' => 'person',
						),
						'edit-tags.php'
					)
				),
			),
			array(
				'page_title' => __( 'Organisations', 'sympose' ),
				'menu_title' => __( 'Organisations', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'post_type' => 'organisation',
						),
						'edit.php'
					)
				),
			),
			array(
				'page_title' => __( 'Organisation categories', 'sympose' ),
				'menu_title' => __( '&nbsp; &rdsh; Categories', 'sympose' ),
				'callback'   => esc_html(
					add_query_arg(
						array(
							'taxonomy'  => 'organisation-category',
							'post_type' => 'organisation',
						),
						'edit-tags.php'
					)
				),
			),
		);

		$submenu_pages = apply_filters( 'sympose_customize_submenu_pages', $submenu_pages );

		foreach ( $submenu_pages as $page ) {

			add_submenu_page(
				$parent_slug,
				$page['page_title'],
				$page['menu_title'],
				'manage_options',
				$page['callback']
			);
		}

		add_submenu_page(
			$parent_slug,
			__( 'Shortcodes', 'sympose' ),
			__( 'Shortcodes', 'sympose' ),
			'manage_options',
			'sympose-shortcodes',
			array( $this, 'shortcodes' )
		);

		add_submenu_page(
			$parent_slug,
			__( 'Extensions', 'sympose' ),
			__( 'Extensions', 'sympose' ),
			'manage_options',
			'sympose-extensions',
			array( $this, 'extensions' )
		);

		add_submenu_page(
			$parent_slug,
			'Quick Start',
			'Quick Start',
			'manage_options',
			'sympose-quick-start',
			array( $this, 'configurator' )
		);

		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'sympose' ),
			__( 'Settings', 'sympose' ),
			'manage_options',
			esc_html(
				add_query_arg(
					array(
						'page' => 'sympose',
					),
					'admin.php'
				)
			)
		);

		// Remove CMB2 created page.
		remove_menu_page( 'sympose' );
	}

	/**
	 * Shortcodes
	 *
	 * @since   1.2.0
	 */
	public function shortcodes() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sympose Shortcodes', 'sympose' ); ?></h1>
			<div class="card">
				<div class="wrap">
					<?php

					$events = get_terms(
						array(
							'taxonomy' => 'event',
							'parent'   => 0,
						)
					);

					if ( count( $events ) > 0 ) {
						echo '<h2>' . esc_html__( 'A couple of shortcodes you can use', 'sympose' ) . '</h2>';
						echo esc_html__( 'Sympose automatically generates shortcodes based on the content that you create. Below, you will find shortcodes for showing the schedule, a list of people, or lists of organisations.', 'sympose' );
					} else {
						echo '<h2>' . esc_html__( 'Shortcodes will appear here', 'sympose' ) . '</h2>';
						echo '<p>' . esc_html__( 'As soon as you create content with sympose, available shortcodes will be displayed here..', 'sympose' ) . '</p>';
					}

					?>
				</div>
			</div>
			<?php

			$types = array(
				array( 'organisations', 'organisation' ),
				array( 'people', 'person' ),
			);

			foreach ( $events as $event ) {

				foreach ( $types as $type ) {

					$taxonomy = $type[1] . '-category';

					$terms = get_terms(
						array(
							'taxonomy' => $taxonomy,
						)
					);

					if ( ! empty( $terms ) ) {

						echo '<div class="card">';

						echo '<div class="wrap">';

						/* translators: %1$s is the type of content, %2$s is the event name */
						echo '<strong>' . sprintf( esc_html__( 'Show %1$s of %2$s', 'sympose' ), esc_html( $type[0] ), esc_html( $event->name ) ) . '</strong><br/>';

						echo '<ul>';

						foreach ( $terms as $term ) {

							echo '<li><i>[sympose type="' . esc_html( $type[1] ) . '" category="' . esc_html( $term->slug ) . '" event="' . esc_html( $event->slug ) . '" description="false" name="true"]</i></li>';
						}

						echo '</ul>';

						echo '</div>';

						echo '</div>';

					}
				}

				// Schedule.
				echo '<div class="card">';

				echo '<div class="wrap">';

				/* translators: %1$s is the event name */
				echo '<strong>' . sprintf( esc_html__( 'Show the schedule of %1$s', 'sympose' ), esc_html( $event->name ) ) . '</strong>';

				echo '<ul>';
				echo '<li><i>[sympose type="schedule" event="' . esc_html( $event->slug ) . '"]</i></li>';
				echo '</ul>';

				echo '</div>';

				echo '</div>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Extensions
	 *
	 * @since   1.2.0
	 */
	public function extensions() {

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sympose Extensions', 'sympose' ); ?></h1>
			<p></p>
			<div class="extensions-wrapper">
				<?php

				$remote_products = json_decode( get_option( 'sympose_extensions' ) );

				if ( ! $remote_products || empty( $remote_products ) ) {
					echo '<h2>' . esc_html__( 'Extensions could not be loaded', 'sympose' ) . '</h2>';
					echo '<p>' . esc_html__( 'There was a problem retrieving the extensions. Try re-activating the plugin.', 'sympose' ) . '</p>';

					return;
				}

				$parsed_link = wp_parse_url( $remote_products->products[0]->info->link );

				$checkout_link = $parsed_link['scheme'] . '://' . $parsed_link['host'] . '/checkout/';

				foreach ( $remote_products->products as $product ) {

					if ( '0.00' === $product->pricing->amount ) {
						$price         = __( 'Free', 'sympose' );
						$download_link = $product->info->link;
					} else {
						$price         = '&euro;' . esc_html( $product->pricing->amount );
						$download_link = add_query_arg(
							array(
								'edd_action'  => 'add_to_cart',
								'download_id' => $product->info->id,
							),
							$checkout_link
						);
					}

					$license_key = '';

					$licenses = sympose_get_option( 'licenses' );
					if ( is_array( $licenses ) ) {
						if ( isset( $licenses[ $product->info->slug ] ) && ! empty( $licenses[ $product->info->slug ] ) ) {
							$license_key = $licenses[ $product->info->slug ];
						}
					}

					echo '<div class="sympose-extension">';
					echo '<div class="image">';
					echo '<img alt="' . esc_html( $product->info->title ) . ' " width="75" height="75" src="' . esc_url( $product->info->thumbnail ) . '" />';
					echo '</div>';
					echo '<div class="title"><h3>' . esc_html( $product->info->title ) . '</h3></div>';
					echo '<div class="content">';
					echo '<p>' . esc_html( wp_trim_words( wp_kses( strip_shortcodes( $product->info->content ), '<i><p><strong><a>' ), 40 ) ) . '</p>';
					echo '</div>';
					echo '<div class="footer">';
					$plugin_file = $product->info->slug . '/' . $product->info->slug . '.php';

					if ( is_plugin_active( $plugin_file ) || 'all-extensions' === $product->info->slug ) {
						if ( '0.00' === $product->pricing->amount ) {
							echo '<a class="button button-primary disabled" href="#">' . esc_html__( 'Active', 'sympose' ) . '</a>';
						} else {
							echo '<form action="#" method="POST" class="activate-license">';
							wp_nonce_field( 'activate-license', 'activate-license', '' );
							echo '<input type="hidden" name="download-id" value="' . esc_html( $product->info->id ) . '"/>';
							echo '<input type="hidden" name="plugin-name" value="' . esc_html( $product->info->slug ) . '" />';
							$escaped_license_key = esc_html( $license_key );
							echo '<input class="license regular-text ltr" name="license-key" type="' . ( ! empty( $escaped_license_key ) ? 'password' : 'text' ) . '" value="' . esc_html( $license_key ) . '" placeholder="' . esc_html__( 'Enter your license key..', 'sympose' ) . '" />';
							submit_button( esc_html__( 'Activate', 'sympose' ), 'secondary', 'submit-license-activation', false );
							echo '</form>';
							echo '<p><a target="_blank" href="' . esc_url( $product->info->link ) . '">' . esc_html__( 'More information', 'sympose' ) . '</a></p>';
						}
					} elseif ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
						echo '<a class="button button-primary" href="' . esc_url( admin_url() ) . esc_url( wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . rawurlencode( $plugin_file ) . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $plugin_file ) ) . '">' . esc_html__( 'Activate', 'sympose' ) . '</a>';
					} else {
						echo '<a target="_blank" class="text-link" href="' . esc_url( $product->info->link ) . '">' . esc_html__( 'More information', 'sympose' ) . '</a><a target="_blank" class="button button-primary" href="' . esc_url( $download_link ) . '">' . esc_html__( 'Download', 'sympose' ) . ' - ' . esc_html( $price ) . '</a>';
					}
					echo '</div>';

					echo '</div>';
				}

				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Adds settings link to plugin page
	 *
	 * @param mixed  $links Current array of links.
	 * @param string $file The plugin file.
	 *
	 * @return mixed    New array of links
	 */
	public function plugin_row_actions( $links, $file ) {
		if ( is_plugin_active( 'sympose/sympose.php' ) ) {
			if ( strpos( $file, 'sympose.php' ) !== false ) {
				$links['settings'] = '<a href="' . get_admin_url( get_current_blog_id(), 'admin.php?page=sympose' ) . '" target="_blank">' . __( 'Settings', 'sympose' ) . '</a>';
			}
		}

		return $links;
	}

	/**
	 *
	 * Republishes the sessions with correct date after the event date changes
	 *
	 * @param int $term_id The term ID.
	 */
	public function republish_sessions( $term_id ) {
		// TODO: Only do this when the date actually changed.
		$sessions = get_posts(
			array(
				'post_type'   => 'session',
				'tax_query'   => array(
					'taxonomy' => 'event',
					'terms'    => $term_id,
					'field'    => 'slug',
					'operator' => 'IN',
				),
				'numberposts' => - 1,
				'orderby'     => 'menu_order',
			)
		);

		foreach ( $sessions as $session ) {
			wp_update_post(
				array(
					'ID' => $session->ID,
				)
			);
		}
	}

	/**
	 * Setup Wizard Introduction screen
	 *
	 * @since 1.2.4
	 */
	public function maybe_show_setup_wizard() {
		$options = get_option( 'sympose' );

		$current_screen = get_current_screen();

		if ( isset( $options['show_setup_wizard'] ) && 'sympose_page_sympose-quick-start' !== $current_screen->base || false === $options ) {
			if ( true === $options['show_setup_wizard'] || empty( $options ) ) {
				?>
				<div class="sympose-setup-wizard">
					<div class="content">
						<div class="sympose-setup-wizard-header">
							<h1>Sympose</h1>
						</div>
						<div class="sympose-setup-wizard-notices">

						</div>
						<div class="sympose-setup-wizard-content">
							<div class="wizard-content" data-type="introduction">
								<p><?php esc_attr_e( 'Welcome to Sympose!', 'sympose' ); ?></p>
								<p><?php esc_attr_e( 'Thanks for choosing Sympose to launch your event/conference website. This Setup wizard will guide you through the basic setup to help you configure your event or conference.', 'sympose' ); ?></p>
								<p><?php esc_attr_e( 'You can generate some sample data, use the Quick Start to set things up or skip the setup and do things your own way.', 'sympose' ); ?></p>
								<p>
									<strong><?php esc_attr_e( 'Get started now', 'sympose' ); ?>...</strong>
									<?php esc_attr_e( 'this process will only take a few minutes to complete!', 'sympose' ); ?>
								</p>
								<ul class="buttons">
									<li><a data-action="skip-step" href="#" class="button"><?php esc_attr_e( 'Skip setup', 'sympose' ); ?></a></li>
									<li><a data-action="setup-sample-data" class="button" href="#"><?php esc_attr_e( 'Setup sample data', 'sympose' ); ?></a><span class="spinner"></span></span></li>
									<li><a data-action="quick-start" class="button" href="#"><?php esc_attr_e( 'Quick start an event', 'sympose' ); ?></a></li>
								</ul>
							</div>
							<div class="wizard-content" data-type="quick-start">
								<?php $this->configurator(); ?>
							</div>
						</div>
						<div class="sympose-setup-wizard-footer">

						</div>
					</div>
				</div>
				<?php
			}
		}
	}

	/**
	 *
	 * Processes the quick start input
	 *
	 * @param object $request The request object from the REST API.
	 *
	 * @return array Array with status and message.
	 * @throws Exception Throws an error on error.
	 * @since 1.2.4
	 */
	public function process_quick_start( $request ) {

		$params = json_decode( $request->get_body() );

		if ( empty( $params ) ) {
			$params = $request->get_params();
		}

		$result = array();

		if ( isset( $params->action ) && 'skip-step' === $params->action ) {
			$options                      = get_option( 'sympose' );
			$options['show_setup_wizard'] = false;
			update_option( 'sympose', $options );

			$result = array(
				'status'  => 200,
				'message' => __( 'We will not show the setup wizard again.', 'sympose' ),
			);

		} else {
			if ( isset( $params['save_data'] ) ) {
				unset( $params['save_data'] );
				update_option( 'sympose_quick_start_data', $params );

				$result = array(
					'status'  => 200,
					'message' => __( 'Data saved!', 'sympose' ),
				);
			} else {

				// Create Event.
				$data = $params;

				$event_name = $params['title'];

				if ( empty( $event_name ) ) {
					return array(
						'status'  => 400,
						'message' => __( 'Seems like we got some empty fields..', 'sympose' ),
					);
				}

				$event = wp_insert_term(
					$event_name,
					'event'
				);

				if ( is_wp_error( $event ) ) {
					return array(
						'status'  => 400,
						'message' => 'An event with this name already exists.',
					);
				}

				$date = new Datetime( $params['start_date'] );

				// Set event date.
				update_term_meta( $event['term_id'], $this->prefix . 'event_date', $date->getTimestamp() );

				$days = array();

				$count_days = absint( $params['days'] );

				for ( $i = 1; $i < $count_days + 1; $i ++ ) {

					$day = wp_insert_term(
						'Day ' . $i,
						'event',
						array(
							'parent' => $event['term_id'],
						)
					);
					update_term_meta( $day['term_id'], $this->prefix . 'event_date', $date->getTimestamp() );
					$days[] = $day;

					// Increase start date.
					$date->modify( ' +1 day' );
				}

				$event_term = get_term( $event['term_id'] );

				$agenda_page = wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_title'   => $event_name . ' ' . __( 'Schedule', 'sympose' ),
						'post_content' => '[sympose type="schedule" event="' . esc_html( $event_term->slug ) . '"]',
						'post_status'  => 'publish',
					)
				);

				// Set schedule page for events.
				update_term_meta( $event['term_id'], $this->prefix . 'schedule_page_id', $agenda_page );

				// Set schedule page for days.
				foreach ( $days as $day ) {
					update_term_meta( $day['term_id'], $this->prefix . 'schedule_page_id', $agenda_page );
				}

				$speakers_page = wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_title'   => $event_name . ' ' . __( 'Speakers', 'sympose' ),
						'post_content' => '[sympose type="person" event="' . esc_html( $event_term->slug ) . '" description="true" name="true"]',
						'post_status'  => 'publish',
					)
				);

				$sponsors_page = wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_title'   => $event_name . ' ' . __( 'Sponsors', 'sympose' ),
						'post_content' => '[sympose type="organisation" event="' . esc_html( $event_term->slug ) . '"]',
						'post_status'  => 'publish',
					)
				);

				$sponsors = array();

				$sponsors[] = '';

				// Sponsors.
				if ( isset( $data['organisations'] ) && ! empty( $data['organisations'] ) ) {
					unset( $data['organisations'][0] );
					foreach ( $data['organisations'] as $key => $organisation ) {
						$sponsor = wp_insert_post(
							array(
								'post_type'    => 'organisation',
								'post_title'   => $organisation,
								'post_content' => '',
								'post_status'  => 'publish',
							)
						);

						wp_set_object_terms( $sponsor, $event['term_id'], 'event', true );

						$sponsors[] = $sponsor;
					}
				}

				// Speakers.
				$speakers = array();

				$speakers[] = '';

				if ( isset( $data['people'] ) && ! empty( $data['people'] ) ) {
					unset( $data['people'][0] );
					foreach ( $data['people'] as $key => $person ) {
						$speaker = wp_insert_post(
							array(
								'post_type'    => 'person',
								'post_title'   => $person,
								'post_content' => '',
								'post_status'  => 'publish',
							)
						);

						wp_set_object_terms( $speaker, $event['term_id'], 'event', true );

						$speakers[] = $speaker;
					}
				}

				// Sessions.
				$sessions = array();

				$day_count = 0;

				foreach ( $data['sessions'] as $key => $day ) {

					// Ignore initial schedule.
					if ( 'initial' === $key ) {
						continue;
					}

					foreach ( $day as $session_key => $session ) {

						if ( 'row' === $session_key || empty( $session['title'] ) ) {
							continue;
						}

						$people_ids       = array();
						$organisation_ids = array();

						// Get people.
						if ( ! empty( $session['people'] ) && is_array( $session['people'] ) ) {
							foreach ( $session['people'] as $id ) {
								if ( 0 !== (int) $id ) {
									$people_ids[] = (string) $speakers[ $id ];
								}
							}
						}

						// Get organisations.
						if ( ! empty( $session['organisations'] ) && is_array( $session['organisations'] ) ) {
							foreach ( $session['organisations'] as $id ) {
								if ( 0 !== (int) $id ) {
									$organisation_ids[] = (string) $sponsors[ $id ];
								}
							}
						}

						$session_id = wp_insert_post(
							array(
								'post_type'    => 'session',
								'post_title'   => $session['title'],
								'post_content' => '',
								'post_status'  => 'publish',
								'meta_input'   => array(
									$this->prefix . 'session_start'         => $session['start_time'],
									$this->prefix . 'session_end'           => $session['end_time'],
									$this->prefix . 'session_people'        => $people_ids,
									$this->prefix . 'session_organisations' => $organisation_ids,
								),
							)
						);

						// Set event and days.
						wp_set_object_terms( $session_id, $event['term_id'], 'event', true );
						wp_set_object_terms( $session_id, $days[ $day_count ]['term_id'], 'event', true );

						$sessions[] = $session;
					}

					// Increase day count.
					$day_count ++;
				}

				// Empty array after creation.
				update_option( 'sympose_quick_start_data', array() );

				$schedule_link     = get_permalink( $agenda_page );
				$session_dashboard = add_query_arg( 'event', $event_term->slug, admin_url() . 'edit.php?post_type=session' );

				/* Translators: %s is the event name */
				$message = sprintf( esc_html__( 'Your event "%1$s" has been created. %2$sGo to the schedule%3$s, or %4$sshow an overview of the sessions for this event%5$s.', 'sympose' ), esc_html( $data['title'] ), '<a href="' . $schedule_link . '">', '</a>', '<a href="' . $session_dashboard . '">', '</a>' );

				$options                      = get_option( 'sympose' );
				$options['show_setup_wizard'] = false;

				update_option( 'sympose', $options );

				$result = array(
					'status'  => 200,
					'message' => $message,
				);

			}
		}

		return $result;
	}

	/**
	 * The contents of the quick start tool
	 *
	 * @since 1.2.4
	 */
	public function configurator() {

		$defaults = array(
			'title'         => '',
			'days'          => false,
			'start_date'    => false,
			'people'        => array(),
			'organisations' => array(),
			'sessions'      => array(
				'start_time' => array(),
				'end_time'   => array(),
				'title'      => array(),
			),
		);

		$data = get_option( 'sympose_quick_start_data' );

		$data = wp_parse_args( $data, $defaults );

		?>
		<div class="wrap">
			<form id="sympose-quick-start" data-id="1">
				<div class="steps">
					<ul>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'step', 1, filter_input( INPUT_SERVER, 'REQUEST_URI' ) ) ); ?>" data-id="1">
								<span class="title">General information</span>
								<span class="step-number">1</span>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'step', 2, filter_input( INPUT_SERVER, 'REQUEST_URI' ) ) ); ?>" data-id="2">
								<span class="title">People</span>
								<span class="step-number">2</span>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'step', 3, filter_input( INPUT_SERVER, 'REQUEST_URI' ) ) ); ?>" data-id="3">
								<span class="title">Organisations</span>
								<span class="step-number">3</span>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'step', 4, filter_input( INPUT_SERVER, 'REQUEST_URI' ) ) ); ?>" data-id="4">
								<span class="title">Schedule</span>
								<span class="step-number">4</span>
							</a>
						</li>
					</ul>
				</div>

				<div class="content">
					<div class="block" data-id="1">
						<h3><?php esc_attr_e( 'Sympose Quick Start', 'sympose' ); ?></h3>
						<p><?php esc_attr_e( 'Quick start your event with this tool, fill in your event name, people, organisations and the schedule.', 'sympose' ); ?></p>
						<p>
							<label for="title"><?php esc_attr_e( 'Event Title', 'sympose' ); ?>
								<input type="text" id="title" name="title" value="<?php echo esc_html( isset( $data['title'] ) ? $data['title'] : '' ); ?>"/>
							</label>
						</p>
						<p>
							<label for="days"><?php esc_attr_e( 'Amount of days', 'sympose' ); ?>
								<input type="number" id="days" name="days" value="<?php echo esc_html( isset( $data['days'] ) ? $data['days'] : '' ); ?>"/>
							</label>
						</p>
						<p>
							<label for="days"><?php esc_attr_e( 'Start Date', 'sympose' ); ?>
								<input type="date" id="start_date" name="start_date" value="<?php echo esc_html( isset( $data['start_date'] ) ? $data['start_date'] : '' ); ?>"/>
							</label>
						</p>
					</div>
					<div class="block" data-id="2">
						<h3>People</h3>

						<table>
							<tbody>
							<tr data-type="initial">
								<td><input type="text" placeholder="Name.." name="people[]" id="people[]"/></td>
								<td><a data-action="delete" href="#"> </a></td>
							</tr>
							<?php
							if ( isset( $data['people'] ) && ! empty( $data['people'] ) ) {
								unset( $data['people'][0] );
								foreach ( $data['people'] as $key => $person ) {
									?>
									<tr data-type="<?php echo( 1 === $key ? 'first' : 'clone' ); ?>">
										<td><input type="text" placeholder="Name.." name="people[]" id="people[]" value="<?php echo esc_html( ! empty( $person ) ? $person : '' ); ?>"/>
										</td>
										<td><a data-action="delete" href=""> </a></td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
							<tfoot>
							<tr>
								<td><a data-action="add" href="#">Add another person..</a></td>
							</tr>
							</tfoot>
						</table>
					</div>
					<div class="block" data-id="3">
						<h3>Organisations</h3>
						<table>
							<tbody>
							<tr data-type="initial">
								<td><input type="text" placeholder="Company title.." name="organisations[]" id="organisations[]"/></td>
								<td><a data-action="delete" href=""></a></td>
							</tr>
							<?php
							if ( isset( $data['organisations'] ) && ! empty( $data['organisations'] ) ) {
								unset( $data['organisations'][0] );
								foreach ( $data['organisations'] as $key => $organisation ) {
									?>
									<tr data-type="<?php echo( 1 === $key ? 'first' : 'clone' ); ?>">
										<td><input type="text" placeholder="Name.." name="organisations[]" id="organisations[]" value="<?php echo esc_html( ! empty( $organisation ) ? $organisation : '' ); ?>"/>
										</td>
										<td><a data-action="delete" href=""></a></td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
							<tfoot>
							<tr>
								<td><a data-action="add" href="#">Add another organisation..</a></td>
							</tr>
							</tfoot>
						</table>
					</div>
					<div class="block" data-id="4">
						<h3>Schedule</h3>

						<table class="schedule" data-type="initial">
							<tbody>
							<tr>
								<th class="title" colspan="6"><?php echo esc_attr__( 'Day', 'sympose' ) . ' '; ?></th>
							</tr>
							<tr class="session-header">
								<th><?php esc_attr_e( 'Start time', 'sympose' ); ?></th>
								<th><?php esc_attr_e( 'End time', 'sympose' ); ?></th>
								<th><?php esc_attr_e( 'Title', 'sympose' ); ?></th>
								<th><?php esc_attr_e( 'People', 'sympose' ); ?></th>
								<th><?php esc_attr_e( 'Organisations', 'sympose' ); ?></th>
								<th></th>
							</tr>
							<tr data-type="initial" data-id="initial">
								<td><input type="time" placeholder="Start time.." name="sessions[initial][row][start_time]" class="session_start_time"/></td>
								<td><input type="time" placeholder="End time.." name="sessions[initial][row][end_time]" class="session_end_time"/></td>
								<td><input type="text" placeholder="Title.." name="sessions[initial][row][title]" class="session_title"/></td>
								<td>
									<select data-type="people" data-selected='<?php echo wp_json_encode( array() ); ?>' multiple name="sessions[initial][row][people][]" class="session_people">
										<option>Select..</option>
									</select>
								</td>
								<td>
									<select data-type="organisations" data-selected='<?php echo wp_json_encode( array() ); ?>' multiple name="sessions[initial][row][organisations][]" class="session_organisations">
										<option>Select..</option>
									</select>
								</td>
								<td><a data-action="delete" href=""></a></td>
							</tr>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="6"><a data-action="add" href="#">Add another session..</a></td>
							</tr>
							</tfoot>
						</table>

						<?php

						// Check the amount of days.
						$days = 0;

						if ( ! empty( $data['days'] ) && $data['days'] > 1 ) {
							$days = intval( $data['days'] );
						}

						for ( $i = 1; $i <= $days; $i ++ ) {
							?>
							<table class="schedule" data-id="<?php echo esc_attr( $i ); ?>">
								<tbody>
								<tr>
									<th class="title"
										colspan="6"><?php esc_attr_e( 'Day', 'sympose' ) . ' ' . intval( $i ); ?></th>
								</tr>
								<tr class="session-header">
									<th><?php esc_attr_e( 'Start time', 'sympose' ); ?></th>
									<th><?php esc_attr_e( 'End time', 'sympose' ); ?></th>
									<th><?php esc_attr_e( 'Title', 'sympose' ); ?></th>
									<th><?php esc_attr_e( 'People', 'sympose' ); ?></th>
									<th><?php esc_attr_e( 'Organisations', 'sympose' ); ?></th>
									<th></th>
								</tr>
								<tr data-type="initial" data-id="1">
									<td><input type="time" placeholder="Start time.." name="sessions[<?php echo intval( $i ); ?>][row][start_time]" class="session_start_time"/></td>
									<td><input type="time" placeholder="End time.." name="sessions[<?php echo intval( $i ); ?>][row][end_time]" class="session_end_time"/></td>
									<td><input type="text" placeholder="Title.." name="sessions[<?php echo intval( $i ); ?>][row][title]" class="session_title"/></td>
									<td>
										<select data-type="people" data-selected='<?php echo wp_json_encode( array() ); ?>' multiple name="sessions[<?php echo intval( $i ); ?>][row][people][]" class="session_people">
											<option>Select..</option>
										</select>
									</td>
									<td>
										<select data-type="organisations" data-selected='<?php echo wp_json_encode( array() ); ?>' multiple name="sessions[<?php echo intval( $i ); ?>][row][organisations][]" class="session_organisations">
											<option>Select..</option>
										</select>
									</td>
									<td><a data-action="delete" href=""> </a></td>
								</tr>
								<?php

								if ( isset( $data['sessions'][ $i ] ) ) {

									foreach ( $data['sessions'][ $i ] as $key => $session ) {

										if ( 'initial' !== $key && 'row' !== $key ) {

											$people = array();
											if ( isset( $session['people'] ) && ! empty( $session['people'] ) ) {
												$people = $session['people'];
											}

											$organisations = array();
											if ( isset( $session['organisations'] ) && ! empty( $session['organisations'] ) ) {
												$organisations = $session['organisations'];
											}

											?>
											<tr data-type="<?php echo( 1 === $key ? 'first' : 'clone' ); ?>"
												data-id="<?php echo intval( $key ); ?>">
												<td><input type="time" placeholder="Start time.." name="sessions[<?php echo intval( $i ); ?>][<?php echo intval( $key ); ?>][start_time]" id="sessions_start_time" value="<?php echo esc_html( isset( $session['start_time'] ) ? $session['start_time'] : '' ); ?>"/>
												</td>
												<td><input type="time" placeholder="End time.." name="sessions[<?php echo intval( $i ); ?>][<?php echo intval( $key ); ?>][end_time]" id="sessions_end_time" value="<?php echo esc_html( isset( $session['end_time'] ) ? $session['end_time'] : '' ); ?>"/>
												</td>
												<td><input type="text" placeholder="Title.." name="sessions[<?php echo intval( $i ); ?>][<?php echo intval( $key ); ?>][title]" id="sessions_title" value="<?php echo esc_html( isset( $session['title'] ) ? $session['title'] : '' ); ?>"/>
												</td>
												<td>
													<select data-type="people" data-selected='<?php echo wp_json_encode( $people ); ?>' multiple name="sessions[<?php echo intval( $i ); ?>][<?php echo intval( $key ); ?>][people][]" id="sessions_people">
														<option>Select..</option>
													</select>
												</td>
												<td>
													<select data-type="organisations" data-selected='<?php echo wp_json_encode( $organisations ); ?>' multiple name="sessions[<?php echo intval( $i ); ?>][<?php echo intval( $key ); ?>][organisations][]" id="sessions_organisations">
														<option>Select..</option>
													</select>
												</td>
												<td><a data-action="delete" href=""> </a></td>
											</tr>
											<?php
										}
									}
								}
								?>
								</tbody>
								<tfoot>
								<tr>
									<td colspan="6"><a data-action="add" href="#"><?php esc_attr_e( 'Add another session..', 'sympose' ); ?></a>
									</td>
								</tr>
								</tfoot>
							</table>
						<?php } ?>
					</div>
				</div>
				<div class="footer">
					<ul>
						<li data-action="prev"><a class="button" href="#"><?php esc_attr_e( 'Previous Step', 'sympose' ); ?></a></li>
						<li data-action="next"><a class="button" href="#"><?php esc_attr_e( 'Next Step', 'sympose' ); ?></a></li>
						<li data-action="submit"><span class="spinner"> </span> <a class="button" href="#"><?php esc_attr_e( 'Create Event', 'sympose' ); ?></a>
						</li>
					</ul>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Quick start: Disable plugin
	 *
	 * Adds a notice to the admin when Quick Start is enabled
	 */
	public function disable_quick_start_notice() {
		if ( class_exists( 'Sympose_Quick_start' ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					/* translators: %1$s is the link start tag and %2$s is the link end tag. */
					printf( esc_html__( 'Sympose 1.2.5 has integrated Quick Start\'s functionality. To prevent interference, please %1$sdisable the plugin%2$s.', 'sympose' ), '<a href="' . esc_url( admin_url() . 'plugins.php' ) . '">', '</a>' );
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Highlight the current menu item in the WordPress Dashboard
	 *
	 * This function identifies if it's Sympose content and will highlight the Sympose parent menu item if it is.
	 *
	 * @param string $parent_file The default item slug/url.
	 *
	 * @return string The item slug/url.
	 */
	public function highlight_parent_menu_item( $parent_file ) {

		$post_types = apply_filters(
			'sympose_highlight_parent_menu_post_types',
			array(
				'session',
				'organisation',
				'person',
			)
		);

		$taxonomies = apply_filters(
			'sympose_highlight_parent_menu_taxonmies',
			array(
				'event',
			)
		);

		$screen = get_current_screen();

		if ( in_array( $screen->post_type, $post_types, true ) || in_array( $screen->taxonomy, $taxonomies, true ) || 'toplevel_page_sympose' === $screen->base ) {

			$parent_file = esc_html(
				add_query_arg(
					array(
						'post_type' => 'session',
					),
					'edit.php'
				)
			);
		}

		return $parent_file;
	}

	/**
	 *
	 * Higlight the settings item for Sympose
	 *
	 * This is a separate function to prevent overriding #submenu_file in parent_file.
	 *
	 * @param string $submenu_file The current highlighted item.
	 *
	 * @return string The new highlighted item.
	 */
	public function highlight_sub_menu_item( $submenu_file ) {

		$screen = get_current_screen();

		if ( 'toplevel_page_sympose' === $screen->base ) {
			$submenu_file = 'admin.php?page=sympose';
		}

		return $submenu_file;
	}

	/**
	 * Validates the Sympose License
	 */
	public function validate_sympose_license() {

		if ( isset( $_POST['submit-license-activation'] ) ) {
			if ( isset( $_POST['license-key'] ) && ! empty( $_POST['license-key'] ) && isset( $_POST['plugin-name'] ) && ! empty( $_POST['plugin-name'] ) ) {
				if ( ! check_admin_referer( 'activate-license', 'activate-license' ) ) {
					return;
				}

				$options = get_option( 'sympose' );

				if ( ! isset( $options['licenses'] ) || ! is_array( $options['licenses'] ) ) {
					$options['licenses'] = array();
				}

				$plugin_name = sanitize_text_field( wp_unslash( $_POST['plugin-name'] ) );

				$license = sanitize_text_field( wp_unslash( $_POST['license-key'] ) );

				$download_id = 0;

				if ( isset( $_POST['download-id'] ) ) {
					$download_id = intval( $_POST['download-id'] );
				}

				$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $license,
					'item_id'    => $download_id,
					'url'        => home_url(),
				);

				$response = wp_remote_post(
					$this->store_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					)
				);

				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					$error_message = $response->get_error_message();
					$message = ( is_wp_error( $response ) && ! empty( $error_message ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'sympose' );
				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( false === $license_data->success ) {
						switch ( $license_data->error ) {
							case 'expired':
								/* translators: %s is the expiration date. */
								$message = sprintf( __( 'Your license key expired on %s.', 'sympose' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) );
								break;
							case 'revoked':
								$message = __( 'Your license key has been disabled.', 'sympose' );
								break;
							case 'missing':
								$message = __( 'Invalid license.', 'sympose' );
								break;
							case 'invalid':
							case 'site_inactive':
								$message = __( 'Your license is not active for this URL.', 'sympose' );
								break;
							case 'item_name_mismatch':
								/* translators: %s is the plugin name */
								$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'sympose' ), $plugin_name );
								break;
							case 'no_activations_left':
								$message = __( 'Your license key has reached its activation limit.', 'sympose' );
								break;
							default:
								$message = __( 'An error occurred, please try again.', 'sympose' );
								break;
						}
					}
				}

				if ( ! empty( $message ) ) {
					$output = $message;
					$class  = 'error';

					$options['licenses'][ $plugin_name ] = '';

				} else {
					$output = 'License approved!';
					$class  = 'success';

					$options['licenses'][ $plugin_name ] = $license;
				}

				update_option( 'sympose', $options );

				?>
				<div class="notice notice-<?php echo esc_attr( $class ); ?> is-dismissible">
					<p><?php echo esc_html( $output ); ?></p>
				</div>
				<?php

			}
		}

	}

	/**
	 * Get the Sympose icon as encoded SVG.
	 *
	 * @return string SVG icon.
	 */
	public function get_sympose_icon() {
		return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHRpdGxlPmxvZ28tZm9yLXdvcmRwcmVzcy1kYXNoYm9hcmQ8L3RpdGxlPjxwYXRoIGQ9Ik00LjQsMTJBNy42LDcuNiwwLDAsMCw4LDE1LjZMNS4xMSwxNy4zM2E4LjgxLDguODEsMCwxLDAtMi40NC0yLjQ0Wm00LjMzLTEuMzctLjA4LS4xMWEyLjIsMi4yLDAsMCwxLS4xNy0uMjUuNzUuNzUsMCwwLDEtLjA3LS4xMiwyLjE1LDIuMTUsMCwwLDEtLjE0LS4yNi41NC41NCwwLDAsMSwwLS4xMUExLjY2LDEuNjYsMCwwLDEsOC4xLDkuNWwwLS4wN2EzLjg5LDMuODksMCwxLDEsMi41LDIuNWwtLjA3LDBhMS42NiwxLjY2LDAsMCwxLS4zMS0uMTJsLS4xMSwwLS4yNi0uMTQtLjEyLS4wN2EyLjIsMi4yLDAsMCwxLS4yNS0uMTdsLS4xMS0uMDhBMi44OSwyLjg5LDAsMCwxLDksMTEsMi44OSwyLjg5LDAsMCwxLDguNzMsMTAuNjZabS0zLjUyLDBMNi4yNiw5djBhNS41LDUuNSwwLDAsMCw0Ljc5LDQuNzloMGwtMS43MiwxYTYuMjcsNi4yNywwLDAsMS00LjExLTQuMTFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIiBzdHlsZT0iZmlsbDojQTBBNUFBIi8+PHBhdGggZD0iTTE3LjA3LDE3LjA3YTEwLDEwLDAsMSwwLTE0LjE0LDBBMTAsMTAsMCwwLDAsMTcuMDcsMTcuMDdaTTMuMzIsMy4zMmE5LjQ1LDkuNDUsMCwxLDEsMCwxMy4zNkE5LjQ2LDkuNDYsMCwwLDEsMy4zMiwzLjMyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIgc3R5bGU9ImZpbGw6I0EwQTVBQSIvPjwvc3ZnPg==';
	}
}
