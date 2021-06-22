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
		$type  = (isset($instance['type']) ? $instance['type'] : 'people');

		$list_post_type = '';

		if ($type === 'people') {
			$list_post_type = 'person';
		} else {
			$list_post_type = 'organisation';
		}

		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} else {
			echo $title;
		}

		$sympose = new Sympose_Public();

		?>
		<div class="sympose-session-items sympose-widget">
			<div class="sym-list <?php echo $list_post_type; ?>">
				<?php
				$post_ids = get_post_meta( $id, '_sympose_session_' . $type, true );

				if ( is_array( $post_ids ) ) {
					echo '<div class="list-inner">';
					foreach ( $post_ids as $id ) {
						$post = get_post( $id );
						echo $sympose->render_item( $post->ID, array( 'size' => $list_post_type . '-medium', 'name' => true, 'desc' => true ) );
					}
					echo '</div>';
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
		$type     = ( isset( $instance['type'] ) ? $instance['type'] : 'people' );
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
					<option value="organisations" <?php echo ( ( isset( $type ) && ( 'organisations' === $type ) ) ? ' selected="selected"' : '' ); ?>>Organisations</option>
					<option value="people" <?php echo ( ( isset( $type ) && ( 'people' === $type ) ) ? ' selected="selected"' : '' ); ?>>People</option>
				</select>
			</label>
		</p>
		<?php
	}
}
