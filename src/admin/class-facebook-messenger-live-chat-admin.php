<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://messengerchat.leaddevs.com/
 * @since      1.0.0
 *
 * @package    FacebookMessengerLiveChat
 * @subpackage FacebookMessengerLiveChat/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FacebookMessengerLiveChat
 * @subpackage FacebookMessengerLiveChat/admin
 * @author     Najmul Ahmed <dev.najmul@gmail.com>
 */
class FacebookMessengerLiveChat_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FacebookMessengerBot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FacebookMessengerBot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/facebook-messenger-live-chat.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FacebookMessengerBot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FacebookMessengerBot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/facebook-messenger-live-chat.js', array( 'jquery' ), $this->version, false );

        $sfwp_feed_nonce = wp_create_nonce('wpfbmb_feed_nonce');
        wp_localize_script($this->plugin_name, 'wpfbmb_ajax_obj', array(
            'wpfbmb_ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $sfwp_feed_nonce,
        ));
	}
    public function load_admin_pages()
    {
        /**
         * This function is provided for making admin pages into admin area.
         *
         * An instance of this class should be passed to the run() function
         * defined in WOO_FEED_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The WOO_FEED_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (function_exists('add_options_page')) {

            add_menu_page(__('FB Messenger Live Chat', 'facebook-messenger-live-chat'), __('Messenger Live Chat', 'facebook-messenger-chat-bot'), 'manage_options', "leaddevs-facebook-messenger-live-chatbot", 'wpfbmb_facebook_messenger_live_chat_page_display', 'dashicons-format-status');
        }
    }

}
