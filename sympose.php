<?php
/**
 *
 * Sympose
 *
 * @link              https://sympose.net
 * @since             1.0.0
 * @package           Sympose
 *
 * @sympose
 * Plugin Name:       Sympose
 * Plugin URI:        https://sympose.net
 * Description:       Manage events on your WordPress website
 * Version:           1.4.0
 * Author:            Conference7
 * Author URI:        https://conference7.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sympose
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SYMPOSE_VERSION', '1.4.0' );

/**
 * Activation function
 */
function activate_sympose() {
	Sympose_Activator::activate();
}
/**
 * Deactivation function
 */
function deactivate_sympose() {
	Sympose_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sympose' );
register_deactivation_hook( __FILE__, 'deactivate_sympose' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/sympose-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/cmb2/cmb2/init.php';

/**
 * Start the plugin
 *
 * @since    1.0.0
 */
function run_sympose() {

	$plugin = new Sympose();
	$blocks = new Sympose_Blocks();

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true && defined( 'WP_LOCAL_DEV' ) ) {
		$local_dev = filter_var( WP_LOCAL_DEV, FILTER_VALIDATE_BOOLEAN );
		if ( true === $local_dev ) {
			new Sympose_Debug();
		}
	}

}
run_sympose();
