<?php
/**
 * Social Media
 *
 * @link       https://sympose.net
 * @since      1.4.0
 *
 * @package    Sympose
 * @subpackage Sympose/classes
 */

/**
 * Social Media function
 *
 * @since      1.4.0
 * @package    Sympose
 * @subpackage Sympose/classes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Social_Media {
	/**
	 * Sympose_Social_Media constructor.
	 *
	 * @param string $sympose The plugin slug.
	 * @param string $version The plugin version number.
	 * @param string $prefix The plugin prefix.
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
		add_action( 'sympose_register_general_custom_fields', array( $this, 'register_fields' ) );
		add_filter( 'sympose_organisation_after_content', array( $this, 'profile_after_content' ), 10, 2 );
		add_action( 'sympose_widget_profile_extend', array( $this, 'add_widget_content' ) );
		add_action( 'admin_notices', array( $this, 'plugin_notice' ) );
	}

	/**
	 * Show a notice when the deprecated plugin is active.
	 */
	public function plugin_notice() {
		if ( is_plugin_active( 'sympose-social-media/sympose-social-media.php' ) ) {
			$class = 'notice notice-error is-dismissible';
			/* translators: %1$s is the version. %2$s is the functionality. %3$s is the link start tag and %4$s is the link end tag. */
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), sprintf( esc_html__( 'Sympose %1$s has integrated %2$s functionality. To prevent interference, please %3$sdisable the plugin%4$s.', 'sympose' ), esc_html( $this->version ), 'Social Media', '<a href="' . esc_url( admin_url() . 'plugins.php' ) . '">', '</a>' ) );
		}
	}

	/**
	 * Register Custom Fields
	 *
	 * @since 1.4.0
	 *
	 * @param object $cmb The $cmb class for adding new fields.
	 */
	public function register_fields( $cmb ) {

		$social_media = $cmb->add_field(
			array(
				'name'    => __( 'Social Media', 'sympose' ),
				'type'    => 'group',
				'id'      => '_sympose_social_media',
				'options' => array(
					'group_title'   => __( 'Network {#}', 'sympose' ),
					'add_button'    => __( 'Add Network', 'sympose' ),
					'remove_button' => __( 'Remove Network', 'sympose' ),
					'sortable'      => true,
					'closed'        => true,
				),
			)
		);

		$cmb->add_group_field(
			$social_media,
			array(
				'name'       => __( 'Icon', 'sympose' ),
				'type'       => 'select',
				'id'         => 'fa',
				'options_cb' => function () {
					$default = array(
						'facebook-square' => 'Facebook',
						'twitter-square'  => 'Twitter',
						'link'            => 'Website',
					);
					$options = apply_filters( 'sympose_social_media_icons', $default );

					return $options;
				},
			)
		);

		$cmb->add_group_field(
			$social_media,
			array(
				'name' => __( 'URL', 'sympose' ),
				'type' => 'text_url',
				'id'   => 'url',
			)
		);
	}

	/**
	 * Inject social media icons in the front-end profiles
	 *
	 * @since 1.4.0
	 */
	public function add_widget_content() {
		$social_media = get_post_meta( get_the_ID(), '_sympose_social_media', true );
		if ( $social_media ) {
			?>
			<div class="social-media">
			<?php
			foreach ( $social_media as $item ) {
				if ( isset( $item['url'] ) && ! empty( $item['url'] ) ) {
					// phpcs:ignore
					echo '<a target="_blank" href="' . esc_url($item['url']) . '">' . $this->render_social_media_icon( $item['fa'] ) . '</a>';
				}
			}
			?>
			</div>
			<?php
		}
	}

	/**
	 * Render social media icons
	 *
	 * @param string $icon The name of the icon.
	 */
	public function render_social_media_icon( $icon ) {
		$output = '';
		switch ( $icon ) {
			case 'facebook-square':
				$output = '<svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24"><rect x="0" fill="none" width="24" height="24"/><g><path d="M12 2C6.5 2 2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12c0-5.5-4.5-10-10-10z"/></g></svg>';
				break;
			case 'twitter-square':
				$output = '<svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24"><rect x="0" fill="none" width="24" height="24"/><g><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-2.534 6.71c.004.099.007.198.007.298 0 3.045-2.318 6.556-6.556 6.556a6.52 6.52 0 01-3.532-1.035 4.626 4.626 0 003.412-.954 2.307 2.307 0 01-2.152-1.6 2.295 2.295 0 001.04-.04 2.306 2.306 0 01-1.848-2.259v-.029c.311.173.666.276 1.044.288a2.303 2.303 0 01-.713-3.076 6.54 6.54 0 004.749 2.407 2.305 2.305 0 013.926-2.101 4.602 4.602 0 001.463-.559 2.31 2.31 0 01-1.013 1.275c.466-.056.91-.18 1.323-.363-.31.461-.7.867-1.15 1.192z"/></g></svg>';
				break;
			case 'link':
				$output = '<svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>';
				break;
		}
		$output = apply_filters( 'sympose_social_media_icons_output', $output, $icon );
		return $output;
	}
}
