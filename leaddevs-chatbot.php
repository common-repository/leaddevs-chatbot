<?php

if(file_exists(dirname(__FILE__ ) . '/vendor/autoload.php')){
        require_once  dirname(__FILE__ ) . '/vendor/autoload.php';
}


use Leaddevs\WPFBMessenger\includes\Application\WPFBMessengerChatBot;
use Leaddevs\WPFBMessenger\includes\FacebookMessengerLiveChatActivator;
use Leaddevs\WPFBMessenger\includes\FacebookMessengerLiveChatDeactivator;
use Leaddevs\WPFBMessenger\includes\FacebookMessengerLiveChat;



/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://messengerchat.leaddevs.com/
 * @since             1.0.0
 * @package           Facebook_messenger_live_chatbot
 *
 * @wordpress-plugin
 * Plugin Name:       Leaddevs Chatbot
 * Plugin URI:        https://www.messengerchat.leaddevs.com/
 * Description:       Facebook messenger chat for wordpress , It can manage your facebook page to integrate with your website and real time interaction with your clients.
 * Version:           1.0.0
 * Author:            Leaddevs
 * Author URI:        https://www.leaddevs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       facebook-messenger-live-chatbot
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SOCIAL_FEEDS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-social-feeds-activator.php
 */
if(!function_exists('wpfbmb_activate_facebook_messenger_live_chat')){
    function wpfbmb_activate_facebook_messenger_live_chat() {
        FacebookMessengerLiveChatActivator::activate();
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-social-feeds-deactivator.php
 */
if(!function_exists('wpfbmb_deactivate_facebook_messenger_live_chat')){
    function wpfbmb_deactivate_facebook_messenger_live_chat() {
        FacebookMessengerLiveChatDeactivator::deactivate();
    }
}

register_activation_hook( __FILE__, 'wpfbmb_activate_facebook_messenger_live_chat' );
register_deactivation_hook( __FILE__, 'wpfbmb_deactivate_facebook_messenger_live_chat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if(!function_exists('wpfbmb_run_facebook_messenger_live_chat')){
    /**
     *
     */
    function wpfbmb_run_facebook_messenger_live_chat() {

        $plugin = new FacebookMessengerLiveChat();
        $plugin->run();

    }
}
wpfbmb_run_facebook_messenger_live_chat();


/**
 * Manage setting page display
 */
if(!function_exists('wpfbmb_facebook_messenger_live_chat_page_display')){
    function wpfbmb_facebook_messenger_live_chat_page_display()
    {


        try{
            $token = sanitize_text_field($_GET['token']);
            $messenger_state = sanitize_text_field($_GET['messenger_state']);
            if(isset($token) && (!empty($token)) && isset($messenger_state) && !empty($messenger_state)){
                $messengerChatbot = new WPFBMessengerChatBot();

                if($messengerChatbot->get_state() == $messenger_state){
                    $messengerChatbot->set_token($token);
                }

            }
            require plugin_dir_path(__FILE__) . 'src/admin/partials/facebook-messenger-live-chat-admin-display.php';

        }catch (Exception $exception){

        }
    }
}


add_action('wp_ajax_wpfbmb_facebook_messenger_bot_manage_setting', 'wpfbmb_facebook_messenger_bot_option_setting');


/**
 * manage option settings
 */
if(!function_exists('wpfbmb_facebook_messenger_bot_option_setting')){
    function wpfbmb_facebook_messenger_bot_option_setting()
    {
        // nonce check
        check_ajax_referer('wpfbmb_feed_nonce');
        try{
            $formData = sanitize_text_field($_POST['formData']); // filter data
            if(isset($formData) && !empty($formData)){
                parse_str($formData, $messengerChatBotSettingData);
                $messengerChatBot = new WPFBMessengerChatBot();
                $status = false;
                if((isset($messengerChatBotSettingData['page_id']) && !empty($messengerChatBotSettingData['page_id']) || $messengerChatBotSettingData['mc_is_enabled'] == 'false')){
                    $status = $messengerChatBot->syncFBPageData($messengerChatBotSettingData['page_id']);
                    if($status['result']== 'success'){
                        $status = true;
                    }
                }
                update_option($messengerChatBot->get_fb_messenger_chat_bot_setting_option_name(), $messengerChatBotSettingData);
                if($status){
                    $data['status'] = 'success';
                    wp_send_json_success($data);
                    die();
                }else{
                    $data['status'] = 'error';
                    wp_send_json_error($data);
                    die();
                }

            }
        }catch (Exception $exception){
            $data['status'] = 'error';
            wp_send_json_error($data);
            die();
        }

    }
}

add_action( 'wp_footer', 'wpfbmb_add_facebook_messenger_chat_to_website' );
/**
 * @return bool
 */
if(!function_exists('wpfbmb_add_facebook_messenger_chat_to_website')){
    function wpfbmb_add_facebook_messenger_chat_to_website() {
        try{
            $messengerChatBot = new WPFBMessengerChatBot();
            echo $messengerChatBot->get_fb_messenger_layout();

        }catch (Exception $exception){
            echo "";
        }

    }
}
