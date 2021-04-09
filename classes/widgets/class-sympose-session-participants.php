<?php
/**
 * Session participants widget
 *
 * @link       https://sympose.net
 * @since      1.1.4
 *
 * @package    Sympose
 * @subpackage Sympose/admin/widgets
 */

/**
 * Session participants widget
 *
 * @package    Sympose
 * @subpackage Sympose/admin/widgets
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Session_Participants extends WP_Widget {

	/**
	 * Sympose_Session_Participants constructor.
	 */
	public function __construct() {
		parent::__construct( false, 'Sympose Session Participants' );
		add_action( 'admin_notices', array( $this, 'plugin_notice' ) );
	}

	/**
	 * Show a notice when the deprecated plugin is active.
	 */
	public function plugin_notice() {
		if ( is_plugin_active( 'sympose-session-people/sympose-session-people.php' ) || is_plugin_active( 'sympose-session-organisations/sympose-session-organisations.php' ) ) {
			$class = 'notice notice-error is-dismissible';
			/* translators: %1$s is the version. %2$s is the functionality. %3$s is the link start tag and %4$s is the link end tag. */
			$message = sprintf( esc_html__( 'Sympose %1$s has integrated %2$s functionality. To prevent interference, please %3$sdisable %5$s%4$s and re-evaluate your setup.', 'sympose' ), SYMPOSE_VERSION, 'people & organisations session widget', '<a href="' . esc_url( admin_url() . 'plugins.php' ) . '">', '</a>', 'Sympose Session People & Sympose Session Organisations' );
			// phpcs:ignore
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}

	/**
	 *
	 * The Widget
	 *
	 * @param array $args Array of args.
	 * @param array $instance instance.
	 */
	public function widget( $args, $instance ) {

		$show_on = array( 'session', 'organisation' );

		$id        = get_the_ID();
		$post_type = get_post_type( $id );

		if ( ! in_array( $post_type, $show_on, true ) ) {
			return;
		}

		// phpcs:disable

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$type  = (isset($instance['type']) ? $instance['type'] : 'person');

		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} else {
			echo $title;
		}

		$sympose = new Sympose_Public();

		?>
		<div class="sympose-session-items sympose-widget">
			<div class="sym-list">
				<?php
				$post_ids = get_post_meta( $id, '_sympose_session_' . $type, true );

				if ( $post_ids ) {

					foreach ( $post_ids as $id ) {
						$post = get_post( $id );
						echo $sympose->render_item( $post->ID, array( 'size' => 'person-medium', 'name' => true, 'desc' => true ) );
					}
				}
				?>
			</div>
		</div>
		<?php

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
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['type']  = sanitize_text_field( $new_instance['type'] );

		return $instance;
	}

	/**
	 * Form function
	 *
	 * @param array $instance Form instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		$type     = ( isset( $instance['type'] ) ? $instance['type'] : 'person' );
		?>
		<p><i><?php esc_html_e( 'Shows the people/organisations linked to the session.', 'sympose' ); ?></i></p>
		<?php do_action( "sympose_widget_{$this->id_base}_content" ); ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
					value="<?php echo esc_attr( $title ); ?>"/>
			</label>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">Post Type:
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
					<option value="organisations" <?php echo ( ( isset( $type ) && ( 'organisation' === $type ) ) ? ' selected="selected"' : '' ); ?>>Organisation</option>
					<option value="people" <?php echo ( ( isset( $type ) && ( 'person' === $type ) ) ? ' selected="selected"' : '' ); ?>>Person</option>
				</select>
			</label>
		</p>
		<?php
	}
}
