<?php
/**
 * Compatibility
 *
 * @link       https://sympose.net
 * @since      1.4.0
 *
 * @package    Sympose
 * @subpackage Sympose/includes
 */

/**
 * Blocks
 *
 * @since      1.4.0
 * @package    Sympose
 * @subpackage Sympose/includes
 * @author     Sympose <info@sympose.io>
 */
class Sympose_Migrations {

	/**
	 * Construct
	 *
	 * @since    1.4.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'migration_notices' ) );
	}

	/**
	 * Migration notices.
	 *
	 * @since 1.4.0
	 */
	public function migration_notices() {
		// $this->migrate_notices_140();
	}

	/**
	 * Migration index
	 *
	 * @param int $version The version number.
	 *
	 * @since 1.4.0
	 */
	public static function migrate_to( $version ) {
		if ( method_exists( self::class, "migrate_to_$version" ) ) {
			self::{"migrate_to_$version"}();
		}
	}

	/**
	 * Migration notices for version 1.4.0
	 *
	 * @since 1.4.0
	 */
	public function migrate_notices_140() {
		$plugins = array(
			'sympose-session-people/sympose-session-people.php',
			'sympose-session-organisations/sympose-session-organisations.php',
			'sympose-person-profile/sympose-person-profile.php',
			'sympose-organisation-profile/sympose-organisation-profile.php',
			'sympose-social-media/sympose-social-media.php',
		);

		if ( ! empty( $this->plugins_active( $plugins ) ) ) {
			$class   = 'notice notice-error is-dismissible';
			$message = sprintf(
				/* translators: %1$s is the version. %2$s is the functionality. %3$s is the link start tag and %4$s is the link end tag. */
				esc_html__(
					'You\'re using extensions that have been migrated to Sympose %1$s %2$sStart migration%3$s',
					'sympose'
				),
				SYMPOSE_VERSION,
				'&nbsp; <a class="button" data-version="' . esc_attr( SYMPOSE_VERSION ) . '" data-action="sympose-start-migration" href="#">',
				'</a>'
			);
			// phpcs:ignore
			printf( '<div class="%1$s"><p>%2$s<span class="spinner"></span></p></div>', esc_attr( $class ), $message );
		}
	}


	/**
	 * Migrate function for 1.4.0
	 *
	 * @since 1.4.0
	 */
	public static function migrate_to_140() {
		// Add session info to session sidebar.
		$sidebar_widgets   = get_option( 'sidebars_widgets' );
		$widgets_to_create = array();

		$replacements = array(
			array(
				'prev' => 'sympose_person_profile',
				'new'  => 'sympose_profile',
			),
			array(
				'prev' => 'sympose_organisation_profile',
				'new'  => 'sympose_profile',
			),
			array(
				'prev' => 'sympose_session_people',
				'new'  => 'sympose_session_participants',
			),
			array(
				'prev' => 'sympose_session_organisations',
				'new'  => 'sympose_session_participants',
			),
		);

		foreach ( $sidebar_widgets as $key => &$sidebar ) {
			if ( is_array( $sidebar ) && ! empty( $sidebar ) ) {
				foreach ( $sidebar as &$widget ) {
					foreach ( $replacements as $search ) {
						if ( strpos( $widget, $search['prev'] ) !== false ) {

							$new_widget_name = $search['new'];
							$new_option_name = "widget_{$new_widget_name}";

							if ( ! isset( $widgets_to_create[ $new_widget_name ] ) ) {
								$widgets_to_create[ $new_widget_name ] = array();
							}

							if ( ! isset( $widgets_to_create[ $new_widget_name ]['widgets'] ) ) {
								$widgets_to_create[ $new_widget_name ]['widgets'] = array();
							}

							if ( ! isset( $widgets_to_create[ $new_widget_name ]['widget_config'] ) ) {
								$widgets_to_create[ $new_widget_name ]['widget_config'] = array();
							}

							$count = count( $widgets_to_create[ $new_widget_name ]['widgets'] );

							$new_widget = $new_widget_name . '-' . ( $count + 1 );

							$prev_widget = $widget;
							$widget      = $new_widget;

							$name            = strstr( $prev_widget, '-', true );
							$old_option_name = "widget_{$name}";
							$widget_config   = get_option( $old_option_name );

							foreach ( $widget_config as $config_key => $config ) {
								if ( is_array( $config ) ) {
									$widgets_to_create[ $new_widget_name ]['widget_config'][ $count + 1 ] = $config;
								} else {
									$widgets_to_create[ $new_widget_name ]['widget_config'][ $config_key ] = $config;
								}
							}

							$create_widget = array(
								'sidebar'         => $key,
								'old_widget_name' => $name,
								'new_widget_name' => $new_widget_name,
								'prev_widget'     => $prev_widget,
								'new_widget'      => $new_widget,
								'old_option_name' => $old_option_name,
								'new_option_name' => $new_option_name,
							);

							$widgets_to_create[ $new_widget_name ]['widgets'][] = $create_widget;
						}
					}
				}
			}
		}

		// Clean up.
		foreach ( $widgets_to_create as $widget_type => &$widgets_collection ) {

			if ( ! isset( $widgets_collection['widgets'] ) || ! is_array( $widgets_collection['widgets'] ) ) {
				continue;
			}

			foreach ( $widgets_collection['widgets'] as &$widget ) {

				// Set parameters.
				$type = '';
				switch ( $widget['old_widget_name'] ) {
					case 'sympose_session_people':
						$type = 'people';
						break;
					case 'sympose_session_organisations':
						$type = 'organisations';
						break;
				}

				if ( ! empty( $type ) ) {
					foreach ( $widgets_collection['widget_config'] as $conf_key => &$widget_conf ) {
						if ( is_array( $widget_conf ) ) {
							$count = substr( strstr( $widget['new_widget'], '-', false ), 1, 1 );

							if ( intval( $count ) === $conf_key ) {
								$widget_conf['type'] = $type;
							}
						}
					}
				}
			}
		}

		// Process changes.
		foreach ( $widgets_to_create as $widget_type => &$widgets_collection ) {
			foreach ( $widgets_collection['widgets'] as $widget ) {
				delete_option( $widget['old_option_name'] );
			}
			update_option( "widget_{$widget_type}", $widgets_collection['widget_config'] );
		}
		update_option( 'sidebars_widgets', $sidebar_widgets );

		$to_delete = array(
			'sympose-session-people/sympose-session-people.php',
			'sympose-session-organisations/sympose-session-organisations.php',
			'sympose-person-profile/sympose-person-profile.php',
			'sympose-organisation-profile/sympose-organisation-profile.php',
			'sympose-social-media/sympose-social-media.php',
		);

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		foreach ( $to_delete as $plugin ) {
			deactivate_plugins( $plugin );
		}
	}

	/**
	 * Check for plugin activity
	 *
	 * @param array $plugins Array of plugins.
	 *
	 * @since 1.4.0
	 */
	public function plugins_active( $plugins ) {
		$plugins_active = array();
		foreach ( $plugins as $plugin ) {
			if ( is_plugin_active( $plugin ) ) {
				$plugins_active[] = $plugin;
			}
		}

		return $plugins_active;
	}

}
