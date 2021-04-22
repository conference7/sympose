<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sympose.net
 * @since      1.0.0
 *
 * @package    Sympose
 * @subpackage Sympose/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sympose
 * @subpackage Sympose/public
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @param string $sympose The name of the plugin.
	 * @param string $version The version of this plugin.
	 * @param string $prefix The prefix of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $sympose = '', $version = '', $prefix = '_sympose_' ) {
		$this->sympose = $sympose;
		$this->version = $version;
		$this->prefix  = $prefix;

		$this->init();
	}

	/**
	 * Initialize the class
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Shortcode.
		add_shortcode( 'sympose', array( $this, 'shortcodes' ) );

		// Register REST.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Add related info to content.
		add_filter( 'the_content', array( $this, 'add_content' ) );

		add_filter( 'sidebars_widgets', array( $this, 'change_sidebars' ) );
	}

	/**
	 * Register REST Endpoints
	 */
	public function register_rest_routes() {
		register_rest_route(
			'sympose/v1',
			'/update_agenda_sessions/',
			array(
				'methods'             => array( 'GET', 'POST' ),
				'callback'            => array( $this, 'update_agenda_sessions' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * REST Endpoint for updating agenda sessions
	 * Used for both localStorage as user meta
	 *
	 * @param object $request The POST or GET request.
	 *
	 * @return string An array with status and result.
	 * @since       1.3.3
	 */
	public function update_agenda_sessions( $request ) {

		if ( ! is_user_logged_in() ) {
			return array(
				'status' => 401,
				'data'   => array(),
			);
		}

		$params  = json_decode( $request->get_body() );
		$user_id = get_current_user_id();

		if ( ! property_exists( $params, 'saved_sessions' ) ) {
			$user_sessions = get_user_meta( $user_id, $this->prefix . 'saved_sessions', true );
			if ( empty( $user_sessions ) || ! is_array( $user_sessions ) ) {
				$user_sessions = array();
			}
			return array(
				'status' => 200,
				'data'   => $user_sessions,
			);
		}

		$saved_sessions = $params->saved_sessions;

		update_user_meta( $user_id, $this->prefix . 'saved_sessions', $saved_sessions );

		return array(
			'status' => 200,
			'data'   => $saved_sessions,
		);
	}

	/**
	 * Add relevant data to the_content
	 *
	 * @param string $content The content.
	 *
	 * @return string Content with extra content.
	 * @since       1.0.0
	 */
	public function add_content( $content ) {

		if ( ! in_the_loop() ) {
			return $content;
		}

		ob_start();

		if ( sympose_get_option( 'render_sidebars_after_content' ) === 'on' ) {

			$sidebar = 'sympose-' . get_post_type() . '-sidebar';

			if ( is_active_sidebar( $sidebar ) ) {
				echo '<div class="sympose-sidebars">';

				dynamic_sidebar( $sidebar );

				echo '</div>';
			}
		}

		$extra = ob_get_clean();

		return $content . $extra;
	}

	/**
	 * Register shortcodes
	 *
	 * @param array $atts An array of arguments.
	 *
	 * @since       1.0.0
	 */
	public function shortcodes( $atts ) {

		$type        = false;
		$category    = false;
		$event       = false;
		$description = false;
		$name        = false;
		$align       = false;

		$style = '';

		$tax_query = array();

		$list_classes = '';

		ob_start();

		if ( isset( $atts['type'] ) && ! empty( $atts['type'] ) ) {
			$type = sanitize_text_field( $atts['type'] );
		}

		if ( isset( $atts['category'] ) && ! empty( $atts['category'] ) ) {
			$category = sanitize_text_field( $atts['category'] );
		}

		if ( isset( $atts['event'] ) && ! empty( $atts['event'] ) ) {
			$event = sanitize_text_field( $atts['event'] );
		} elseif ( is_archive() ) {
			$term = get_queried_object();
			if ( 'event' === $term->taxonomy ) {
				$event = $term->slug;
			}
		}

		if ( isset( $atts['cols'] ) && ! empty( $atts['cols'] ) ) {
			$cols = absint( $atts['cols'] );
		}

		if ( isset( $atts['description'] ) && ! empty( $atts['description'] ) ) {
			$description = filter_var( $atts['description'], FILTER_VALIDATE_BOOLEAN );
		}

		if ( isset( $atts['name'] ) && ! empty( $atts['name'] ) ) {
			$name = filter_var( $atts['name'], FILTER_VALIDATE_BOOLEAN );
		}

		if ( isset( $atts['align'] ) && ! empty( $atts['align'] ) ) {
			$align = sanitize_text_field( $atts['align'] );
		}

		switch ( $align ) {
			case 'center':
				$style .= 'justify-content: center;';
				break;
			case 'left':
				$style .= 'justify-content: flex-start;';
				break;
			case 'right':
				$style .= 'justify-content: flex-end;';
				break;
		}

				// Quit early if not found.
		if ( ! $type ) {
			return __( 'Nothing found.', 'sympose' );
		}

		if ( 'schedule' === $type ) {
			return $this->render_schedule( $event, $atts );
		}

		if ( $event ) {
			$tax_query[] = array(
				'taxonomy' => 'event',
				'terms'    => $event,
				'field'    => 'slug',
				'operator' => 'IN',
			);
		}

		$term_children = false;

		// Get main category specified from $category.
		$mainterm = get_term_by( 'slug', $category, $type . '-category', array( 'include_children', true ) );
		if ( $mainterm ) {
			// Check if main category has children.
			$term_children = get_terms(
				array(
					'taxonomy'   => $type . '-category',
					'orderby'    => 'meta_value_num',
					'parent'     => $mainterm->term_id,
					'order'      => 'ASC',
					'meta_query' => array(
						array(
							'key'  => $this->prefix . 'sort_id',
							'type' => 'NUMERIC',
						),
					),
				)
			);
		}

		if ( $term_children ) {
			foreach ( $term_children as $term ) {
				$tax_query['category'] = false;
				if ( count( $tax_query ) > 0 ) {
					$tax_query['relation'] = 'AND';
				}

				$tax_query['category'] = array(
					'taxonomy' => $type . '-category',
					'terms'    => $term->term_id,
				);

				$posts = get_posts(
					array(
						'post_type'   => $type,
						'tax_query'   => $tax_query,
						'numberposts' => - 1,
						'orderby'     => 'menu_order',
					)
				);

				echo '<div class="sym-list shortcode ' . esc_attr( $type ) . '">';
				echo '<span class="title">' . esc_html( $term->name ) . '</span>';
				echo '<div class="list-inner" style="' . esc_attr( $style ) . '">';
				foreach ( $posts as $post ) {
					// phpcs:disable
					echo $this->render_item(
						$post->ID,
						array(
							'size' => esc_attr( $post->post_type . '-medium' ),
							'name' => false,
							'desc' => esc_html( $description ),
						)
					);
					// phpcs:enable
				}
				echo '</div>';
				echo '</div>';

			}
		} else {
			if ( $mainterm ) {
				if ( count( $tax_query ) > 0 ) {
					$tax_query['relation'] = 'AND';
				}

				$tax_query[] = array(
					'taxonomy' => $type . '-category',
					'terms'    => $mainterm->term_id,
				);
			}

			$posts = get_posts(
				array(
					'post_type'   => $type,
					'tax_query'   => $tax_query,
					'numberposts' => - 1,
					'orderby'     => 'menu_order',
					'order'       => 'ASC',
				)
			);

			if ( ! $posts ) {
				return esc_html__( 'Nothing found', 'sympose' );
			}

			echo '<div class="sym-list shortcode ' . esc_attr( $type ) . '">';
			echo '<div class="list-inner" style="' . esc_attr( $style ) . '">';
			foreach ( $posts as $post ) {
				// phpcs:disable
				echo $this->render_item(
					$post->ID,
					array(
						'name' => true,
						'size' => esc_attr( $post->post_type . '-medium' ),
						'desc' => esc_html( $description ),
						'name' => esc_attr( $name ),
					)
				);
				// phpcs:enable
			}
			echo '</div>';
			echo '</div>';

		}

		return ob_get_clean();

	}

	/**
	 * Render item
	 *
	 * @param int     $id The id of the item.
	 *
	 * @param array   $args An array of arguments.
	 * @param boolean $is_admin If an admin link should be returned.
	 *
	 * @return string HTML output.
	 * @since   1.0.0
	 */
	public function render_item( $id, $args, $is_admin = false ) {

		$output = '';

		$post          = get_post( $id );
		$post_type     = $post->post_type;
		$object_terms  = get_the_terms( $post, $post_type . '-category' );
		$title         = false;
		$responsiveimg = false;
		$description   = false;

		$defaults = array(
			'link'  => true,
			'image' => true,
			'name'  => true,
			'style' => 'square',
			'size'  => 'medium',
			'desc'  => false,
		);

		$terms = array();
		if ( ! is_wp_error( $object_terms ) && ! empty( $object_terms ) ) {
			foreach ( $object_terms as $term ) {
				$terms[] = $term->slug;
			}
		}

		$args = array_merge( $defaults, $args );

		$classes = array(
			'sym',
			$post->post_type,
		);

		if ( ! empty( $args['style'] ) ) {
			$classes[] = $args['style'];
		}

		if ( $args['desc'] ) {
			$description = get_post_meta( $post->ID, $this->prefix . 'description', true );
		}

		$output .= '<span class="' . implode( ' ', $classes ) . '" data-terms="' . implode( ' ', $terms ) . '">';

		if ( $args['link'] ) {
			if ( ! $is_admin ) {
				$output .= '<a title="' . __( 'Go to', 'sympose' ) . ' ' . $post->post_title . '" href="' . get_permalink( $post->ID ) . '">';
			} else {
				$output .= '<a title="' . __( 'Go to', 'sympose' ) . ' ' . $post->post_title . '" href="' . get_edit_post_link( $post->ID ) . '">';
			}
		}

		if ( $args['image'] ) {
			$img_id  = sympose_get_image( $post );
			$output .= $this->render_image( $img_id, $args['size'], $post_type );
		}

		if ( $args['name'] || isset( $args['name_or_image'] ) && $args['name_or_image'] ) {
			$output .= '<span class="title">' . $post->post_title . '</span>';
		}

		if ( $description ) {
			$output .= '<span class="desc">' . $description . '</span>';
		}

		if ( 'session' === $post->post_type ) {

			// TODO - Is this still in use?
			$output .= '<div class="inner-content">';
			$output .= '<div class="session-info">';
			$start   = get_post_meta( $post->ID, $this->prefix . 'session_start', true );
			$end     = get_post_meta( $post->ID, $this->prefix . 'session_end', true );

			$term = $this->get_session_day( $post->ID );
			if ( $term ) {
				$output .= '<p class="day">' . $term->name . '</p>';
			}
			if ( $start && $end ) {
				$output .= '<p class="time">' . $start . ' - ' . $end . '</p>';
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		if ( $args['link'] ) {
			$output .= '</a>';
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * Render Image
	 *
	 * @param int    $id The id of the image.
	 *
	 * @param string $size the size of the image.
	 *
	 * @param string $type the type of the image.
	 *
	 * @return string Filters the image.
	 * @since 1.0.10
	 */
	public function render_image( $id = 0, $size = '', $type = '' ) {
		$img = wp_get_attachment_image( $id, $size ); // @todo - set custom image size
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			$img = wp_filter_content_tags( $img );
		}
		$output = $img;

		return apply_filters( 'sympose_render_image', $output, $id, $size, $type );
	}

	/**
	 * Get session event
	 *
	 * @param int $id the id of the session.
	 *
	 * @return object the term object.
	 * @since 1.0.0
	 */
	public function get_session_event( $id = 0 ) {
		if ( ! $id ) {
			$id = get_the_ID();
		}

		$terms = get_the_terms( $id, 'event' );

		foreach ( $terms as $term ) {
			if ( 0 === $term->parent ) {
				return $term;
			}
		}

		return false;
	}

	/**
	 * Get the session day
	 *
	 * @param int $id the id of the session.
	 *
	 * @return  object the term object.
	 * @since 1.0.0
	 */
	public function get_session_day( $id = 0 ) {
		if ( ! $id ) {
			$id = get_the_ID();
		}

		$day    = false;
		$parent = false;

		$terms = get_the_terms( $id, 'event' );
		if ( ! empty( $terms ) ) {

			foreach ( $terms as $term ) {
				if ( 0 !== $term->parent ) {
					$day = $term;
				} else {
					$parent = $term;
				}
			}

			if ( ! $day ) {
				$day = $parent;
			}

			return $day;

		} else {
			return false;
		}

	}

	/**
	 * Render schedule
	 *
	 * @param string  $event the slug of the event.
	 * @param array   $atts The attributes from the shortcode.
	 * @param boolean $show_edit_link Show an edit link in the schedule?.
	 *
	 * @return string return the schedule for the event.
	 */
	public function render_schedule( $event = '', $atts = array(), $show_edit_link = true ) {

		$settings = array(
			'show_people'            => 'false',
			'show_organisations'     => 'false',
			'enable_personal_agenda' => 'false',
			'rows'                   => 6,
			'show_read_more'         => 'true',
		);

		$show_people            = sympose_get_option( 'show_people_on_schedule' );
		$show_organisations     = sympose_get_option( 'show_organisations_on_schedule' );
		$enable_personal_agenda = sympose_get_option( 'enable_personal_agenda' );

		$this->stars = apply_filters( 'sympose_customize_favorite_stars', '<div class="stars"><div class="star" data-state="on"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg></div><div class="star" data-state="off"><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"/></svg></div>' );

		if ( 'on' === $show_people ) {
			$settings['show_people'] = 'true';
		}

		if ( 'on' === $show_organisations ) {
			$settings['show_organisations'] = 'true';
		}

		if ( 'on' === $enable_personal_agenda ) {
			$settings['enable_personal_agenda'] = 'true';
		}

		$settings = array_merge( $settings, $atts );

		$terms = false;

		if ( ! $event ) {
			return __( 'No event specified', 'sympose' );
		}

		// Get Event.
		$term = get_term_by( 'slug', $event, 'event' );

		if ( is_wp_error( $term ) || ! $term ) {
			return __( 'Error: The event does not exist.', 'sympose' );
		}

		$row_args = array();

		// How should people and organisations show on the schedule?
		$person_format       = get_term_meta( $term->term_id, $this->prefix . 'schedule_people_format', true );
		$organisation_format = get_term_meta( $term->term_id, $this->prefix . 'schedule_organisations_format', true );

		if ( empty( $person_format ) || 'default' === $person_format ) {
			$person_format = sympose_get_option( 'schedule_people_format' );
		}

		if ( empty( $organisation_format ) || 'default' === $organisation_format ) {
			$organisation_format = sympose_get_option( 'schedule_organisations_format' );
		}

		$row_args['person_format']       = $person_format;
		$row_args['organisation_format'] = $organisation_format;

		// Get days.
		$terms = get_terms(
			array(
				'taxonomy'   => $term->taxonomy,
				'parent'     => $term->term_id,
				'hide_empty' => true,
				'orderby'    => 'meta_value_num',
				'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'  => $this->prefix . 'event_date',
						'type' => 'NUMERIC',
					),
					array(
						'key'     => $this->prefix . 'event_date',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		// If no children terms are specified.
		if ( ! $terms ) {
			$terms = array( $term );
		}

		// Influence amount of rows.
		if ( 'true' !== $settings['show_people'] ) {
			$settings['rows'] = $settings['rows'] - 1;
		}

		// Influence amount of rows.
		if ( 'true' !== $settings['show_organisations'] ) {
			$settings['rows'] = $settings['rows'] - 1;
		}

		if ( 'true' !== $settings['show_read_more'] ) {
			$settings['rows'] = $settings['rows'] - 1;
		}

		if ( 'true' !== $settings['enable_personal_agenda'] ) {
			$settings['rows'] = $settings['rows'] + 1;
		}

		if ( ! $show_edit_link ) {
			$settings['rows'] = $settings['rows'] - 1;
		} else {
			if ( ! current_user_can( 'manage_options' ) ) {
				$settings['rows'] = $settings['rows'] - 1;
			}
		}

		$settings = apply_filters( 'sympose_schedule_settings', $settings, $event );

		ob_start();

		// @todo - Add filters.
		echo '<div class="sympose-schedule">';

		// Build up schedule per day.
		foreach ( $terms as $term ) {

			if ( is_user_logged_in() ) {
				$saved_sessions = get_user_meta( get_current_user_id(), $this->prefix . 'saved_sessions', true );

				if ( isset( $saved_sessions->{$term->term_id} ) && ! empty( $saved_sessions->{$term->term_id} ) ) {
					$event_sessions = $saved_sessions->{$term->term_id};
				} else {
					$event_sessions = array();
				}
			} else {
				$event_sessions = array();
			}

			echo '<table class="sympose-schedule event" data-id="' . absint( $term->term_id ) . '" data-stars-hidden="0" data-show-favorites=false>';

			$description = '';

			if ( ! empty( $term->description ) ) {
				$description = ' - <span>' . $term->description . '</span>';
			}

			if ( 0 !== $term->parent ) {
				if ( ! isset( $settings['hide_title'] ) || 'true' !== $settings['hide_title'] ) {
					echo '<tr class="title-column">';
					echo '<th colspan="' . ( esc_attr( $settings['rows'] ) ) . '"><h3><span>' . esc_html( $term->name ) . '</span>' . esc_html( $description ) . '</h3></th>';
					// phpcs:disable
					echo ( $enable_personal_agenda === 'on' ) ? '<th colspan="1"><div class="saved-sessions-control" data-state="hidden">' . $this->stars . '</div></th>' : '';
					// phpcs:enable
					echo '</tr>';
				}
			}

			// Get sessions for day.
			$posts = get_posts(
				array(
					'post_type'   => 'session',
					'numberposts' => - 1,
					'post_parent' => 0,
					'tax_query'   => array(
						array(
							'taxonomy' => 'event',
							'terms'    => $term->term_id,
						),
					),
					'orderby'     => 'post_date',
					'order'       => 'ASC',
				)
			);

			// Display sessions.
			foreach ( $posts as $post ) {
				$this->render_schedule_row( $post, $settings, $term, $row_args, $show_edit_link, in_array( $post->ID, $event_sessions, true ) );
			}

			echo '<tfoot></tfoot>';

			echo '</table>';
		}

		echo '</div>';

		$output = ob_get_clean();

		return apply_filters( 'sympose_render_schedule', $output, $event, true );
	}

	/**
	 *
	 * Function for rendering a row in the schedule
	 *
	 * @param object  $post session post object.
	 * @param array   $settings array of settings.
	 * @param object  $term The term object.
	 * @param array   $args An array of arguments.
	 * @param boolean $show_edit_link To hide or show the edit link in the row.
	 * @param boolean $session_saved The saved state of the session.
	 */
	public function render_schedule_row( $post, $settings, $term, $args = array(), $show_edit_link, $session_saved ) {

		$defaults = array(
			'show_time'   => true,
			'row_classes' => array(),
		);

		$args = array_merge( $defaults, $args );

		$people        = array();
		$organisations = array();
		$classes       = array();

		$classes[] = 'session-row';

		if ( $session_saved ) {
			$classes[] = 'is-favorite';
		}

		$classes = array_merge( $classes, $args['row_classes'] );

		$start_time = get_post_meta( $post->ID, $this->prefix . 'session_start', true );
		$end_time   = get_post_meta( $post->ID, $this->prefix . 'session_end', true );

		$people        = get_post_meta( $post->ID, $this->prefix . 'session_people', true );
		$organisations = get_post_meta( $post->ID, $this->prefix . 'session_organisations', true );

		$running = has_term( 'running', 'session-status', $post );

		$static_session = get_post_meta( $post->ID, $this->prefix . 'session_static', true );

		if ( $running ) {
			$classes[] = 'running';
		}

		$people_html = '';

		$people_args = array(
			'name'  => false,
			'desc'  => false,
			'image' => false,
			'size'  => 'person-schedule',
		);

		switch ( $args['person_format'] ) {
			case 'name':
				$people_args['name'] = true;
				break;
			case 'photo':
				$people_args['image'] = true;
				break;
			case 'photo_name':
				$people_args['name']  = true;
				$people_args['image'] = true;
				break;
			default:
				$people_args['image'] = true;
				break;
		}

		if ( is_array( $people ) ) {
			$people_html = '<div class="sym-list">';
			foreach ( $people as $id ) {
				$people_html .= $this->render_item(
					$id,
					$people_args
				);
			}
			$people_html .= '</div>';
		}

		$organisations_html = '';

		$organisation_args = array(
			'name'  => false,
			'desc'  => false,
			'image' => false,
			'size'  => 'organisation-schedule',
		);

		switch ( $args['organisation_format'] ) {
			case 'name':
				$organisation_args['name'] = true;
				break;
			case 'photo':
				$organisation_args['image'] = true;
				break;
			case 'logo_name':
				$organisation_args['name']  = true;
				$organisation_args['image'] = true;
				break;
			default:
				$organisation_args['image'] = true;
				break;
		}

		if ( is_array( $organisations ) ) {
			$organisations_html = '<div class="sym-list">';
			foreach ( $organisations as $id ) {
				$organisations_html .= $this->render_item(
					$id,
					$organisation_args
				);
			}
			$organisations_html .= '</div>';
		}

		$time = $start_time . ' - ' . $end_time;

		$link_start = '';
		$link_end   = '';

		if ( ! $static_session ) {
			$link_start = '<a href="' . get_permalink( $post->ID ) . '">';
			$link_end   = '</a>';
		}

		ob_start();

		//phpcs:disable
		echo '<tr class="' . implode( ' ', sanitize_html_class( $classes ) ) . '" data-id="' . esc_attr( $post->ID ) . '">';
		if ( current_user_can( 'manage_options' ) && $show_edit_link ) {
			echo '<td class="edit-link"><a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '"><span class="dashicons dashicons-edit"></span></a></td>';
		}
		echo '<td class="time">' . ( $args['show_time'] ? $link_start . $time . $link_end : '' ) . '</td>';
		echo '<td class="title">';
		do_action('sympose_before_schedule_title', $post);
		echo apply_filters( 'sympose_schedule_title', $link_start . $post->post_title . $link_end, $post->ID, $link_start, $post->post_title, $link_end );
		do_action('sympose_after_schedule_title', $post);
		echo '</td>';
		if ( 'true' === $settings['show_people'] ) {
			echo '<td class="people"><div class="inner">' . $people_html . '</div></td>';
		}
		if ( 'true' === $settings['show_organisations'] ) {
			echo '<td class="organisations"><div class="inner">' . $organisations_html . '</div></td>';
		}
		echo apply_filters( 'sympose_schedule_row_before_read_more', '', $post->ID );
		if ( 'true' === $settings['show_read_more'] ) {
			$read_more = '<td class="sympose-read-more">';
			if ( ! $static_session ) {
				$read_more .= $link_start . __( 'Read more »', 'sympose' ) . $link_end;
			}
			$read_more .= '</td>';
			echo apply_filters( 'sympose_schedule_read_more', $read_more );
		}

		if ( 'true' === $settings['enable_personal_agenda'] ) {
			echo '<td class="session-saved" data-state="' . ( true === $session_saved ? 'on' : 'off' ) . '"><div class="inner">' . $this->stars . '</div></td>';
		}
		echo '</tr>';

		$row = ob_get_clean();
		echo apply_filters( 'sympose_schedule_row', $row, $post->ID );
		// phpcs:enable

		// Render children.
		$children = get_posts(
			array(
				'post_type'   => 'session',
				'numberposts' => - 1,
				'post_parent' => $post->ID,
			)
		);

		$args = array_merge(
			$args,
			array(
				'show_time'   => false,
				'row_classes' => array( 'session-child' ),
			)
		);

		// Display sessions.
		foreach ( $children as $child ) {
			$this->render_schedule_row(
				$child,
				$settings,
				$term,
				$args,
				$show_edit_link,
				$session_saved
			);
		}
	}

	/**
	 * Render result
	 */
	public function render_result() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/' . $this->sympose . '-item-list.php';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( sympose_get_option( 'enable_css' ) ) {
			wp_enqueue_style( $this->sympose, plugin_dir_url( dirname( __FILE__ ) ) . 'css/dist/public/sympose.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->sympose, plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/public/sympose.' . ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? 'min.' : '' ) . 'js', array( 'jquery', 'wp-api' ), $this->version, false );
	}

	/**
	 * Getter function
	 *
	 * @param var $variable A variable.
	 *
	 * @return var returns the variable.
	 * @since   1.0.5
	 */
	public function get( $variable ) {
		return $this->{$variable};
	}

	/**
	 * Change sidebar
	 *
	 * @param array $widgets Array of widgets.
	 *
	 * @return Array of widgets.
	 * @since   1.0.5
	 */
	public function change_sidebars( $widgets ) {

		if ( ! is_admin() ) {

			$post_type = get_post_type( get_the_ID() );

			if ( in_array( $post_type, array( 'session', 'person', 'organisation' ), true ) && is_single() ) {

				$default_sidebar    = sympose_get_option( 'default_sidebar' );
				$overwrite_sidebars = sympose_get_option( 'overwrite_sidebars' );

				if ( false === $default_sidebar ) {
					return $widgets;
				}

				if ( false === $overwrite_sidebars ) {
					return $widgets;
				}

				$widgets[ $default_sidebar ] = $widgets[ 'sympose-' . $post_type . '-sidebar' ];
			}
		}

		return $widgets;
	}

	/**
	 *
	 * Renders a session, used for session informatinoa nd related sessions extension
	 *
	 * @param bool  $id The id of the session.
	 * @param array $args Array of arguments.
	 *
	 * @throws Exception Throws exception.
	 */
	public function render_session( $id = false, $args ) {

		$default_args = apply_filters(
			'sympose_extend_session_information_widget_default_fields',
			array(
				'render_link'        => true,
				'show_session_link'  => 'on',
				'show_session_title' => 'on',
				'show_schedule_link' => 'on',
				'show_session_date'  => 'on',
				'show_session_time'  => 'on',
				'show_event_title'   => 'on',
			)
		);

		if ( is_array( $args ) ) {
			$args = array_merge( $default_args, $args );
		} else {
			$args = $default_args;
		}

		if ( false === $id ) {
			$id = get_the_ID();
		}

		$sympose = new Sympose_Public();
		$prefix  = $sympose->get( 'prefix' );

		$timestamp = time();

		$day = $sympose->get_session_day( $id );
		if ( $day ) {
			$timestamp = get_term_meta( $day->term_id, $prefix . 'event_date', true );
		}

		if ( empty( $timestamp ) || ! is_numeric( $timestamp ) ) {
			$timestamp = get_term_meta( $day->parent, $prefix . 'event_date', true );
			if ( empty( $timestamp ) || ! is_numeric( $timestamp ) ) {
				if ( is_user_logged_in() ) {
					esc_html_e( 'Error: Please set a valid date for the event.', 'sympose' );
				}

				return;
			}
		}

		$start_time = get_post_meta( $id, $prefix . 'session_start', true );

		$end_time = get_post_meta( $id, $prefix . 'session_end', true );

		$date = new Datetime();
		$date->setTimestamp( $timestamp );

		if ( $day && $day->parent ) {
			$event = $sympose->get_session_event();
		}

		$date_string = $date->format( get_option( 'date_format' ) );

		if ( $args['show_session_title'] ) {

			if ( $args['render_link'] ) {
				echo '<h5><a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html( get_the_title( $id ) ) . '</a></h5>';
			} else {
				echo '<h5>' . esc_html( get_the_title( $id ) ) . '</h5>';
			}
		}

		echo '<strong>';

		if ( $args['show_schedule_link'] ) {

			$event_day        = '';
			$schedule_page_id = false;

			if ( $args['show_event_title'] ) {
				if ( isset( $event ) ) {
					if ( ! empty( $event->name ) ) {
						$event_day .= $event->name;
					}
					$schedule_page_id = sympose_get_schedule_page( $event );
				}
			}

			if ( isset( $day ) ) {
				if ( ! empty( $day->name ) ) {
					if ( ! empty( $event_day ) ) {
						$event_day .= ': ';
					}
					$event_day .= $day->name;
				}
				$schedule_page_id = sympose_get_schedule_page( $day );
			}

			if ( $schedule_page_id ) {
				echo '<a href="' . esc_url( get_permalink( $schedule_page_id ) ) . '">';
			}

			echo esc_html( $event_day );

			if ( $schedule_page_id ) {
				echo '</a>';
			}
		}

		echo '</strong>';

		if ( 'on' === $args['show_session_date'] || 'on' === $args['show_session_time'] || 'on' === $args['show_session_link'] ) {

			echo '<p>';

			if ( 'on' === $args['show_session_date'] ) {
				echo esc_html( $date_string );
			}

			if ( 'on' === $args['show_session_time'] ) {
				echo '<br/>' . esc_html( $start_time ) . ' - ' . esc_html( $end_time );
			}

			if ( 'on' === $args['show_session_link'] ) {
				echo '<br/>';
				echo '<a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html__( 'Go to session', 'sympose' ) . ' &raquo;</a>';
			}
			echo '</p>';

		}

		do_action( 'sympose_extend_information_widget', $id, $args );
	}

}
