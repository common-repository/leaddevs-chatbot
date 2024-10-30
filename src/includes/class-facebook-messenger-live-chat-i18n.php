<?php
namespace Leaddevs\WPFBMessenger\includes;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://messengerchat.leaddevs.com/
 * @since      1.0.0
 *
 * @package    FacebookMessengerLiveChat
 * @subpackage FacebookMessengerLiveChat/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    FacebookMessengerLiveChat
 * @subpackage FacebookMessengerLiveChat/includes
 * @author     Najmul Ahmed <dev.najmul@gmail.com>
 */
class FacebookMessengerLiveChat_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'facebook-messenger-bot',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
