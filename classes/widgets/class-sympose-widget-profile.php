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
		add_action( 'admin_notices', array( $this, 'plugin_notice' ) );
	}

	/**
	 * Show a notice when the deprecated plugin is active.
	 */
	public function plugin_notice() {
		if ( is_plugin_active( 'sympose-person-profile/sympose-person-profile.php' ) || is_plugin_active( 'sympose-organisation-profile/sympose-organisation-profile.php' ) ) {
			$class = 'notice notice-error is-dismissible';
			/* translators: %1$s is the version. %2$s is the functionality. %3$s is the link start tag and %4$s is the link end tag. */
			$message = sprintf( esc_html__( 'Sympose %1$s has integrated %2$s functionality. To prevent interference, please %3$sdisable %5$s%4$s.', 'sympose' ), SYMPOSE_VERSION, 'profile', '<a href="' . esc_url( admin_url() . 'plugins.php' ) . '">', '</a>', 'Sympose Person Profile and Sympose Organisation Profile' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
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
		echo '<div class="sympose-person-widget sympose-widget">';

		if ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) {
			// phpcs:ignore
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$img_id = sympose_get_image( get_the_ID() );

		$sympose = new Sympose_Public();
		// phpcs:ignore
		echo $sympose->render_image( $img_id, 'person-medium', 'person' );

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
