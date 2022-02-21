<?php
/**
 * Sympose Profile Widget
 *
 * @link       https://sympose.net
 * @since      1.4.0
 *
 * @package    Sympose
 * @subpackage Sympose/classes/widgets
 */

/**
 * Profile widgets
 *
 * @link       https://sympose.net
 * @since      1.4.0
 *
 * @package    Sympose
 * @subpackage Sympose/classes/widgets
 */
class Sympose_Widget_Profile extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( false, 'Sympose Profile' );
	}

	/**
	 * Register widget
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance The widget instance.
	 */
	public function widget( $args, $instance ) {

		$show_on = array( 'person', 'organisation' );

		$id        = get_the_ID();
		$post_type = get_post_type( $id );

		if ( ! in_array( $post_type, $show_on, true ) ) {
			return;
		}

		// phpcs:ignore
		echo $args['before_widget'];
		echo '<div class="sympose-' . esc_html( $post_type ) . '-widget sympose-widget">';

		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			// phpcs:ignore
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$img_id = sympose_get_image( get_the_ID() );

		$sympose = new Sympose_Public();
		do_action( 'sympose_before_profile_image', $id, $img_id );
		// phpcs:ignore
		echo $sympose->render_image( $img_id, esc_html( $post_type ) . '-medium', esc_html( $post_type ) );
		do_action( 'sympose_after_profile_image', $id, $img_id );

		$post = get_post( get_the_ID() );
		?>

		<h3><?php echo esc_html( $post->post_title ); ?></h3>

		<?php
		$desc = get_post_meta( get_the_ID(), '_sympose_description', true );

		if ( $desc ) {
			echo '<p>' . esc_html( $desc ) . '</p>';
		}

		echo '</div>';

		do_action( 'sympose_widget_profile_extend' );

		// phpcs:ignore
		echo $args['after_widget'];
	}

	/**
	 * Update function
	 *
	 * @param array $new_instance Old widget instance.
	 * @param array $old_instance New widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Form output in dashboard
	 *
	 * @param array $instance The widget instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		?>
		<p><i><?php esc_html_e( 'Shows a person or organisation profile.', 'sympose' ); ?></i></p>
		<?php
		// phpcs:ignore
		do_action( "sympose_widget_{$this->id_base}_content" );
		// phpcs:disable
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
					   value="<?php echo $title; ?>"/>
			</label>
		</p>
		<?php
		// phpcs:enable
	}
}
