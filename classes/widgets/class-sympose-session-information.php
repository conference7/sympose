<?php
/**
 * Session information widget
 *
 * @link       https://sympose.net
 * @since      1.1.4
 *
 * @package    Sympose
 * @subpackage Sympose/admin/widgets
 */

/**
 * Session information widget
 *
 * @package    Sympose
 * @subpackage Sympose/admin/widgets
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Session_Information extends WP_Widget {

	/**
	 * Sympose_Session_Information constructor.
	 */
	public function __construct() {
		parent::__construct( false, 'Sympose Session Information' );
	}

	/**
	 *
	 * The Widget
	 *
	 * @param array $args Array of args.
	 * @param array $instance instance.
	 */
	public function widget( $args, $instance ) {

		$show_on = array( 'session' );

		$id        = get_the_ID();
		$post_type = get_post_type( $id );

		if ( ! in_array( $post_type, $show_on, true ) ) {
			return;
		}

		// phpcs:disable

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} else {
			echo $title;
		}

		$instance['show_session_link'] = false;

		$sympose = new Sympose_Public();
		
		echo '<div class="sympose-session-information sympose-widget">';
		
		$sympose->render_session( $id, $instance );
		
		echo '</div>';

		echo $args['after_widget'];

		// phpcs:enable
	}

	/**
	 * Update functon
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 *
	 * @return array instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                       = $old_instance;
		$instance['title']              = sanitize_text_field( $new_instance['title'] );
		$instance['show_session_title'] = sanitize_text_field( $new_instance['show_session_title'] );
		$instance['show_schedule_link'] = sanitize_text_field( $new_instance['show_schedule_link'] );
		$instance['show_session_date']  = sanitize_text_field( $new_instance['show_session_date'] );
		$instance['show_session_time']  = sanitize_text_field( $new_instance['show_session_time'] );
		$instance['show_event_title']   = sanitize_text_field( $new_instance['show_event_title'] );
		$instance['show_read_more']     = sanitize_text_field( $new_instance['show_read_more'] );

		$instance = apply_filters( 'sympose_extend_session_information_widget_fields_update', $instance, $new_instance );

		return $instance;
	}

	/**
	 * Form function
	 *
	 * @param array $instance Form instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			apply_filters(
				'sympose_extend_session_information_widget_default_fields',
				array(
					'title'              => '',
					'show_session_title' => 'on',
					'show_schedule_link' => 'on',
					'show_session_date'  => 'on',
					'show_session_time'  => 'on',
					'show_event_title'   => 'on',
					'show_read_more'     => 'on',
				)
			)
		);
		$title    = $instance['title'];
		?>
		<p><i><?php esc_html_e( 'Shows the session information.', 'sympose' ); ?></i></p>
		<?php do_action( "sympose_widget_{$this->id_base}_content" ); ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
			</label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_session_title'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_session_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_session_title' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_session_title' ) ); ?>"><?php esc_html_e( 'Show session title', 'sympose' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_schedule_link'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_schedule_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_schedule_link' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_schedule_link' ) ); ?>"><?php esc_html_e( 'Show link to schedule', 'sympose' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_event_title'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_event_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_event_title' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_event_title' ) ); ?>"><?php esc_html_e( 'Show event title in link', 'sympose' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_session_date'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_session_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_session_date' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_session_date' ) ); ?>"><?php esc_html_e( 'Show session date', 'sympose' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_session_time'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_session_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_session_time' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_session_time' ) ); ?>"><?php esc_html_e( 'Show session time', 'sympose' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_read_more'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_read_more' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_read_more' ) ); ?>"/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_read_more' ) ); ?>"><?php esc_html_e( 'Show read more link', 'sympose' ); ?></label>
		</p>
		<?php
		do_action( 'sympose_extend_session_information_widget_fields', $this, $instance );
	}
}
