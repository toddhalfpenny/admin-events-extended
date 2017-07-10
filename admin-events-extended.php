<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://datamad.co.uk/wordpress-plugins/admin-events-extended/
 * @since             0.0.1
 * @package           admin_events_extended
 *
 * @wordpress-plugin
 * Plugin Name: Admin Events Extended
 * Plugin URI:        https://datamad.co.uk/wordpress-plugins/admin-events-extended/
 * Description:       Adds further sources of events and news to the Community Events admin dashboard widget
 * Version:           0.0.1
 * Author:            Todd Halfpenny
 * Author URI:        http://toddhalfpenny.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-events-extended
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-events-extended-activator.php
 */
function activate_admin_events_extended() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-events-extended-activator.php';
	Admin_Events_Extended_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-events-extended-deactivator.php
 */
function deactivate_admin_events_extended() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-events-extended-deactivator.php';
	Admin_Events_Extended_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_admin_events_extended' );
register_deactivation_hook( __FILE__, 'deactivate_admin_events_extended' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-admin-events-extended.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_admin_events_extended() {

	$plugin = new Admin_Events_Extended();
	$plugin->run();

}
run_admin_events_extended();
