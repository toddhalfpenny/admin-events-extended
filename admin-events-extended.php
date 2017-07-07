<?php
/**
 * Admin Events Extended
 *
 * @link              https://datamad.co.uk
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
 * Is the URL the one used to get community events
 *
 * @since  0.0.1
 * @param  string $url The URL that has been called.
 * @return boolean      Is the URL the one used to get community events.
 */
function is_community_events_call( $url ) {
	if ( 'https://api.wordpress.org/events/1.0/' == $url ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Is the event near London
 *
 * @since  0.0.1
 * @param  integer $lat  Latitude.
 * @param  integer $long Longitude.
 * @return boolean       Is the event near London.
 */
function is_near_london( $lat, $long ) {
	$london_lat = 51.51279;
	$london_long = -0.09184;
	if ( ( $lat > ( $london_lat - 1 ) ) &&
			 ( $lat < ( $london_lat + 1 ) ) &&
			 ( $long > ( $london_long - 1 ) ) &&
			 ( $long < ( $london_long + 1 ) ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Gets the events from external API
 *
 * @since  0.0.1
 * @return object Object representing one event.
 */
function get_london_events() {
	$my_events  = array();

	$url = 'https://api.meetup.com/London-WordPress/events?photo-host=public&sig_id=20603821&sig=590d8cc9ba769db34c73bac1e2c54c8dc5d405f9';

	$response = wp_remote_get( $url );
	if (  wp_remote_retrieve_response_code( $response ) == 200 ) {
		$body = wp_remote_retrieve_body( $response );
		$body_json = json_decode( $body );

		// We just take the first event, for now.
		$first_event = reset( $body_json );

		// Try to get local time - only works if setting is on the site, otherwise use GMT.
		$time_zone_str = get_option( 'timezone_string' );
		if ( '' != $time_zone_str ) {
			$my_date = get_date_from_gmt( date( 'Y-m-d H:i:s', $first_event->time / 1000 ) );
		} else {
			$my_date = date( 'Y-m-d H:i:s', $first_event->time / 1000 );
		}

		$my_event = array(
			'type'  => 'meetup',
			'title' => $first_event->name,
			'url'   => $first_event->link,
			'meetup' => 'London WordPress',
			'meetup_url' => 'https://www.meetup.com/London-WordPress/',
			'date'   => $my_date,
			'location' => array(
				'location' => $first_event->venue->city . ', ' . $first_event->venue->name,
				'country' => 'gb',
				),
			);
	}

	return json_encode( $my_event );

}

/**
 * Hook that handles the HTTP responses
 *
 * @since  0.0.1
 * @param array  $response HTTP response.
 * @param array  $args HTTP request arguments.
 * @param string $url The request URL.
 * @return array Updated HTTP response.
 */
function aee_handle_http_requests( $response, $args, $url ) {
	// Parse request and response body to a hash for human readable log output.
	$log_response = $response;
	if ( isset( $args['body'] ) ) {
		parse_str( $args['body'], $args['body_parsed'] );
	} else {
		return $response;
	}

	if ( isset( $log_response['body'] ) ) {
		parse_str( $log_response['body'], $log_response['body_parsed'] );
	} else {
		return $response;
	}

	$logfile = plugin_dir_path( __FILE__ ) . '/http_requests.log';

	if ( is_community_events_call( $url ) ) {
		$my_json = json_decode( $log_response['body'] );
		if ( is_near_london( $my_json->location->latitude, $my_json->location->longitude ) ) {

			$london_events = json_decode( get_london_events() );

			array_unshift( $my_json->events, $london_events );

			/*
			Temp Log code
			file_put_contents( $logfile, json_encode( $my_json->events ), FILE_APPEND );
			file_put_contents( $logfile, "\n\n", FILE_APPEND );
			*/
			$log_response['body'] = json_encode( $my_json );
			return $log_response;
		}
	}

	/*
	Write into logfile
	file_put_contents( $logfile, sprintf( "### %s, URL: %s\nREQUEST: %sRESPONSE: %s\n", date( 'c' ), $url, print_r( $args, true ), print_r( $log_response, true ) ), FILE_APPEND );
	return $response;
	*/
}

// Hook into WP_Http::_dispatch_request().
add_filter( 'http_response', 'aee_handle_http_requests', 10, 3 );
