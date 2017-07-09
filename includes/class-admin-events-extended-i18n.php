<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://datamad.co.uk/wordpress-plugins/admin-events-extended/
 * @since      0.0.1
 *
 * @package    Admin_Events_Extended
 * @subpackage Admin_Events_Extended/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    Admin_Events_Extended
 * @subpackage Admin_Events_Extended/includes
 * @author     Todd Halfpenny <todd@toddhalfpenny.com>
 */
class Admin_Events_Extended_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'admin-events-extended',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
