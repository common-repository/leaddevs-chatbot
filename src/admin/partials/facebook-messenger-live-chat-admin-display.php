<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://messengerbot.leaddevs.com/
 * @since      1.0.0
 *
 * @package    FacebookMessengerLiveChat
 * @subpackage FacebookMessengerLiveChat/admin/partials
 */

$messengerChatBot = new Leaddevs\WPFBMessenger\includes\Application\WPFBMessengerChatBot();
$pages = $messengerChatBot->fb_messenger_chat_bot_page_option_setting();
$settings = $messengerChatBot->fb_messenger_chat_bot_option_setting();

?>

<br> <br>
<h2><?php echo _e('Facebook Messenger Live Chat With Customers', 'wpfbmb-fb-messenger-chat-bot'); ?></h2>
<?php wp_nonce_field('wpfbmb_feed_form_nonce'); ?>
    <div>
        <form action="#" id="wpfbmb-fb_messenger_bot-manage-form">
            <ul class="wpfbmb-fb-messenger-bot-tabs">
                <li>
                    <input type="radio" name="wpfbmb-fb-messenger-bot-tabs" id="tab1" class="wpfbmb-fb-messenger-bot-tab-input" checked/>
                    <label class="wpfbmb-fb-messenger-bot-tabs-name" for="tab1"><?php echo _e( 'Authorize', 'social-feed' ); ?></label>

                    <div id="wpfbmb-fb-messenger-bot-tab-content1" class="wpfbmb-fb-messenger-bot-tab-content">
                        <table class="table tree widefat fixed sorted_table mtable" width="100%" id="table-1">

                            <tbody>
                            <tr>
                                <td style="padding-top: 30px;">
                                    <?php echo _e( 'Log In with Facebook', 'wpfbmb-fb-messenger-chat-bot' ); ?>
                                </td>
                                <td  style="padding-top: 0px;">
                                    <div class="wpfbmb-fb-messenger-bot-login">
                                        <a href="<?php echo $messengerChatBot->get_authorization_url();?>">
                                            <?php
                                            echo _e('<img class="wpfbmb_feed_fb_messenger_bot_login_logo" width="auto" style="display:flex !important;" height="100px" src="' . plugins_url( 'admin/images/facebook-login-icon.jpg', dirname(__DIR__) ) . '" >  ','wpfbmb-fb-messenger-chat-bot');
                                            ?>
                                        </a>

                                    </div>
                                </td>
                                <td style="padding-top:20px;">
                                    <?php if($messengerChatBot->is_authenticated()){?>
                                    <div style="">
                                        <h4 style="color: darkgreen;">Your are logged in! Your access token is working.</h4>
                                        <?php }else{ ?>
                                            <h4 style="color: orangered;">Not logged in ! Please login to make messenger live chat.</h4>
                                        <?php }?>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </li>
                <li>
                    <input type="radio" name="wpfbmb-fb-messenger-bot-tabs" class="wpfbmb-fb-messenger-bot-tab-input" id="tab2"/>
                    <label class="wpfbmb-fb-messenger-bot-tabs-name" for="tab2"><?php echo _e( 'Settings', 'wpfbmb-fb-messenger-chat-bot' ); ?></label>

                    <div id="wpfbmb-fb-messenger-bot-tab-content2" class="wpfbmb-fb-messenger-bot-tab-content">

                        <h3>General</h3>

                        <table class="table widefat fixed mtable" width="100%">
                            <tbody>
                            <tr>
                                <td><?php echo _e( 'Select Page', 'wpfbmb-fb-messenger-chat-bot' ); ?></td>
                                <td>
                                    <select name="page_id"  style="width: 15vw;">
                                        <?php

                                        echo _e($messengerChatBot->page_drop_down_lists($pages, $settings['page_id']),'wpfbmb-fb-messenger-chat-bot');
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo _e( 'Position', 'wpfbmb-fb-messenger-chat-bot' ); ?></td>
                                <td>
                                    <?php
                                    if(isset($settings['position'])){
                                        $selectedPosition = $settings['position'];
                                    }else{
                                        $selectedPosition = 'left';
                                    }
                                    ?>
                                    <select name="position"  style="width: 15vw;">
                                        <?php echo _e($messengerChatBot->position_list($selectedPosition),'wpfbmb-fb-messenger-chat-bot');?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><?php echo _e( 'Background Color', 'wpfbmb-fb-messenger-chat-bot' ); ?></td>
                                <td><input placeholder="#e302ff" class="jscolor" type="color" value="<?php echo _e(isset($settings['background'])? '#'.$settings['background']: '#ffffff');?>" name="background"/></td>
                            </tr>
                            <tr>
                                <td><?php echo _e( 'Enable/Disable Messenger Chat', 'wpfbmb-fb-messenger-chat-bot' ); ?></td>
                                <td>
                                    <label class="wpfbmb_switch_en_dis">
                                        <input class="wpfbmb_switch_input" id="wpfbmb_switch_input_id" name="mc_is_enabled" type="checkbox" value="<?php echo isset($settings['mc_is_enabled'])? $settings['mc_is_enabled']: 'false' ?>" <?php echo _e((isset($settings['mc_is_enabled']) && $settings['mc_is_enabled'] == true) ? 'checked' : '' )?>>
                                        <span class="wpfbmb_switch_en_dis_slider wpfbmb_switch_en_dis_round"></span>
                                    </label>
                                </td>
                            </tr>


                            </tbody>
                        </table>
                        <table class="widefat fixed">

                            <tr>
                                <td align="left" class="">


                                </td>
                                <td align="right">
                                    <span class="wpfbmb-save-success-alert-message">Successfully Saved</span>
                                    <span class="wpfbmb-save-error-alert-message">Failed</span>

                                    <button type="button"  class="wpfbmb-btn wpfbmb-fb-messenger-bot-manage-settings">
                                        <?php echo _e( 'Save', 'wpfbmb-fb-messenger-chat-bot' ); ?>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>

                </li>
            </ul>
        </form>
    </div>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
