<?php
namespace Leaddevs\WPFBMessenger\includes\Application;
/**
 * The file that defines the core youtube feed handler class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://messengerchat.leaddevs.com/
 * @since      1.0.0
 *
 * @package    Fb_Messenger_Bot
 * @subpackage Fb_Messenger_Bot/includes/application
 */

use Exception;

/**
 * The core plugin fb messenger chat bot class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fb_Messenger_Bot
 * @subpackage Fb_Messenger_Bot/includes/application
 * @author     Najmul Ahmed <dev.najmul@gmail.com>
 */

class WPFBMessengerChatBot{

    // variables declaration
    private $access_token;

    private $admin_redirect_url;

    public $authorization_api_url;

    private $state;

    private $api_key;

    private $pluginName = "messengerBot";

    private $page_end = "";
    private $hostSync;
    private $client_id;

    private $client_secret ;

    private $mode = "production";
    

    /**
     * SFWPYoutube constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return mixed|void
     */
    public function fb_messenger_chat_bot_credentials_options(){
        try{
            return get_option('wpfbmb_feed_fb_messenger_chat_bot_application_credentials');
        }catch (Exception $exception){

        }
    }
    /**
     * @return mixed|void
     */
    public function get_fb_messenger_chat_bot_credentials_option_name(){
        return 'wpfbmb_feed_fb_messenger_chat_bot_application_credentials';
    }
    /**
     * @return mixed|void
     */
    public function fb_messenger_chat_bot_option_setting(){
        return get_option('wpfbmb_feed_fb_messenger_chat_bot_application_option_setting');
    }
    public function fb_messenger_chat_bot_page_option_setting(){
        return get_option('wpfbmb_fb_messenger_pages_settings');
    }
    public function fb_messenger_chat_bot_page_option_setting_name(){
        return 'wpfbmb_fb_messenger_pages_settings';
    }

    /**
     * @return mixed|void
     */
    public function get_fb_messenger_chat_bot_setting_option_name(){
        return 'wpfbmb_feed_fb_messenger_chat_bot_application_option_setting';
    }

    /**
     * Initialize the values , required for sign in and getting access token
     */
    private function init()
    {
        $this->authorization_api_url = "https://api.leaddevs.com/api/plugin/fb/messenger/livechat/".$this->mode."/authenticate?";
        $this->admin_redirect_url = admin_url("/admin.php?page=leaddevs-facebook-messenger-live-chatbot");
        $this->page_end = "https://graph.facebook.com/me/accounts?";
        $this->hostSync = "https://graph.facebook.com/v5.0/me/messenger_profile?";
        $credentials_options = $this->fb_messenger_chat_bot_credentials_options();
        $this->api_key = isset($credentials_options['api_key']) ? $credentials_options['api_key']: "";
        $this->client_id = isset($credentials_options['client_id']) ? $credentials_options['client_id']: "";
        $this->client_secret = isset($credentials_options['secret_key']) ? $credentials_options['secret_key']: "";
        $this->access_token = isset($credentials_options['access_token']) ? $credentials_options['access_token']: "";

    }

    /**
     * @return string
     */
    public function get_authorization_url()
    {

        $this->state = $this->set_state($this->get_fb_messenger_chat_bot_credentials_option_name());
        return $this->authorization_api_url."redirect_url=". $this->admin_redirect_url. "&state=".$this->state. '&merchant='.$this->pluginName. '&ptype=BD02FF' ;
    }

    /**
     * @param $option
     * @return string
     */
    public function set_state($option)
    {
        # Setting LinkedIn state value
        $credentialsArray = get_option($option);
        $state = time(). uniqid();
        if(isset($credentialsArray) && !empty($credentialsArray)){
            $credentialsArray['state'] = $state;
        }else{
            $credentialsArray = array(
                'state' => $state
            );
        }

        update_option($option, $credentialsArray);
        return $state;
    }

    /**
     * @param $code
     * @param $tokenCode
     * @return bool
     */
    function set_token($tokenCode)
    {
        # Setting fb_messenger_chat_bot code and state
        try{
            if(!empty($tokenCode) && isset($tokenCode)){
                $tokenCodeExtract = base64_decode($tokenCode); // decoding token body
                parse_str( $tokenCodeExtract, $tokenBody);
                $credentialsArray = $this->fb_messenger_chat_bot_credentials_options();
                $accessToken = isset($tokenBody['access_token'])? $tokenBody['access_token']: "";
                $credentialsArray['access_token'] = $accessToken;
                $credentialsArray['expires_in'] = isset($tokenBody['expires_in']) ? $tokenBody['expires_in']: 0;
                $credentialsArray['will_expire'] = date('d-m-Y H:i:s', time()+ $tokenBody['expires_in']);
                update_option('wpfbmb_feed_fb_messenger_chat_bot_application_credentials',$credentialsArray);

                $pageDetails = array();
                $pages = $this->get_pages_details($accessToken);
                if(isset($pages['data']) && !empty($pages['data'])){

                    $pageDetails = json_encode($pages['data']);
                }
                update_option($this->fb_messenger_chat_bot_page_option_setting_name(), $pageDetails);
                return true;
            }
            return false;
        }catch (Exception $exception){
            return false;
        }
    }


    /**
     * @return mixed
     */
    function get_code()
    {
        # Getting fb_messenger_chat_bot code

        $credentialsArray = $this->fb_messenger_chat_bot_credentials_options();
        return $credentialsArray['code'];
    }
    /**
     * @return string
     */
    function get_state(){
        $credentialsArray = get_option("wpfbmb_feed_fb_messenger_chat_bot_application_credentials");
        return isset($credentialsArray['state'])? $credentialsArray['state']: "";
    }

    /**
     * @return bool
     */
    public function is_authenticated()
    {
        $options = $this->fb_messenger_chat_bot_credentials_options();
        if(isset($options['access_token']) && !empty($options['access_token'])){
            return $this->validate_access_token();
        }
        return false;

    }

    /**
     * validate access token
     */
    public function validate_access_token()
    {
        $options = $this->fb_messenger_chat_bot_credentials_options();
        if(isset($options['refresh_token'])&& isset($options['will_expire'])){
            $current_time = date('d-m-Y H:i:s', time());
            $expired_in = $options['will_expire'];
            if(strtotime($current_time) > strtotime($expired_in)){
                return false;
            }
        }
        return true;
    }

    /**
     * @param $access_token
     * @return array|bool|mixed|object
     */
    public function get_pages_details($access_token)
    {

        $params = array(
            'access_token' => $access_token
        );
        $requestBody = array(
            'timeout' => 3600
        );
        $requestUrl = $this->page_end . http_build_query($params);
        try{
            // call ugcPosts api
            $result = wp_remote_get($requestUrl, $requestBody);
            //return $result;
            // retrieve success code
            $responseCode = wp_remote_retrieve_response_code($result);

            if($responseCode == 200){
                $jsonDecode = json_decode($result['body'], 1);
                return $jsonDecode;
            }

        }catch (Exception $exception){

        }
        return false;
    }

    /**
     * @param $pages
     * @param $selectedPage
     * @return string
     */
    public function page_drop_down_lists($pages, $selectedPage)
    {

        $options = "<option value=''>Select Page</option>";
        try{
            if($pages && !empty($pages)){
                $pages = json_decode($pages, true);
                foreach ($pages as $key => $page){
                    $selected = '';
                    $pageId = $page['id'];
                    $pageName = $page['name'];
                    if($pageId == $selectedPage){
                        $selected = 'selected';
                    }

                    $options .= "<option $selected name='page_id' value='$pageId'>". $pageName . "</option>";
                }
            }
            return $options;
        }catch (Exception $exception){
            return $options;
        }
    }

    /**
     * @param $selectedPosition
     * @return string
     */
    public function position_list($selectedPosition)
    {

        $positionList = array(
            "left" => "Left",
            "right" => "Right",
        );
        $options = "";
        foreach ($positionList as $key => $value){
            $selected = '';
            if($key == $selectedPosition){
                $selected = 'selected';
            }
            $options .= "<option $selected name='position_option' value='$key'>". $value . "</option>";
        }

        return $options;
    }

    /**
     * @param $selectedLanguage
     * @return string
     */
    public function language_list($selectedLanguage)
    {
        $languages = array(
            "af_ZA" =>	"Afrikaans",
            "gn_PY" =>	"Guaraní",
            "ay_BO" => "Aymara",
            "az_AZ" => "Azeri",
            "id_ID" => "Indonesian",
            "ms_MY" => "Malay",
            "jv_ID" => "Javanese",
            "bs_BA" => "Bosnian",
            "ca_ES" => "Catalan",
            "cs_CZ" => "Czech",
            "ck_US" => "Cherokee",
            "cy_GB" => "Welsh",
            "da_DK" => "Danish",
            "se_NO" => "Northern Sámi",
            "de_DE" => "German",
            "et_EE" => "Estonian",
            "en_IN" => "English (India)",
            "en_PI" => "English (Pirate)",
            "en_GB" => "English (UK)",
            "en_UD" => "English (Upside Down)",
            "en_US" => "English (US)",
            "es_LA" => "Spanish",
            "es_CL" => "Spanish (Chile)",
            "es_CO" => "Spanish (Colombia)",
            "es_ES" => "Spanish (Spain)",
            "es_MX" => "Spanish (Mexico)",
            "es_VE" => "Spanish (Venezuela)",
            "eo_EO" => "Esperanto",
            "eu_ES" => "Basque",
            "tl_PH" => "Filipino",
            "fo_FO" => "Faroese",
            "fr_FR" => "French (France)",
            "fr_CA" => "French (Canada)",
            "fy_NL" => "Frisian",
            "ga_IE" => "Irish",
            "gl_ES" => "Galician",
            "ko_KR" => "Korean",
            "hr_HR" => "Croatian",
            "xh_ZA" => "Xhosa",
            "zu_ZA" => "Zulu",
            "is_IS" => "Icelandic",
            "it_IT" => "Italian",
            "ka_GE" => "Georgian",
            "sw_KE" => "Swahili",
            "tl_ST" => "Klingon",
            "ku_TR" => "Kurdish",
            "lv_LV" => "Latvian",
            "fb_LT" => "Leet Speak",
            "lt_LT" => "Lithuanian",
            "li_NL" => "Limburgish",
            "la_VA" => "Latin",
            "hu_HU" => "Hungarian",
            "mg_MG" => "Malagasy",
            "mt_MT" => "Maltese",
            "nl_NL" => "Dutch",
            "nl_BE" => "Dutch (België)",
            "ja_JP" => "Japanese",
            "nb_NO" => "Norwegian (bokmal)",
            "nn_NO" => "Norwegian (nynorsk)",
            "uz_UZ" => "Uzbek",
            "pl_PL" => "Polish",
            "pt_BR" => "Portuguese (Brazil)",
            "pt_PT" => "Portuguese (Portugal)",
            "qu_PE" => "Quechua",
            "ro_RO" => "Romanian",
            "rm_CH" => "Romansh",
            "ru_RU" => "Russian",
            "sq_AL" => "Albanian",
            "sk_SK" => "Slovak",
            "sl_SI" => "Slovenian",
            "so_SO" => "Somali",
            "fi_FI" => "Finnish",
            "sv_SE" => "Swedish",
            "th_TH" => "Thai",
            "vi_VN" => "Vietnamese",
            "tr_TR" => "Turkish",
            "zh_CN" => "Simplified Chinese (China)",
            "zh_TW" => "Traditional Chinese (Taiwan)",
            "zh_HK" => "Traditional Chinese (Hong Kong)",
            "el_GR" => "Greek",
            "gx_GR" => "Classical Greek",
            "be_BY" => "Belarusian",
            "bg_BG" => "Bulgarian",
            "kk_KZ" => "Kazakh",
            "mk_MK" => "Macedonian",
            "mn_MN" => "Mongolian",
            "sr_RS" => "Serbian",
            "tt_RU" => "Tatar",
            "tg_TJ" => "Tajik",
            "uk_UA" => "Ukrainian",
            "hy_AM" => "Armenian",
            "yi_DE" => "Yiddish",
            "he_IL" => "Hebrew",
            "ur_PK" => "Urdu",
            "ar_AR" => "Arabic",
            "ps_AF" => "Pashto",
            "fa_IR" => "Persian",
            "sy_SY" => "Syriac",
            "ne_NP" => "Nepali",
            "mr_IN" => "Marathi",
            "sa_IN" => "Sanskrit",
            "hi_IN" => "Hindi",
            "bn_IN" => "Bengali",
            "pa_IN" => "Punjabi",
            "gu_IN" => "Gujarati",
            "ta_IN" => "Tamil",
            "te_IN" => "Telugu",
            "kn_IN" => "Kannada",
            "ml_IN" => "Malayalam",
            "km_KH" => "Khmer"
        );


        $options = "";
        foreach ($languages as $key => $value){
            $selected = '';
            if($key == $selectedLanguage){
                $selected = 'selected';
            }
            $options .= "<option $selected name='position_option' value='$key'>". $value . "</option>";
        }

        return $options;
    }
    /**
     * @param $pid
     * @return array|bool|mixed|object
     */
    public function syncFBPageData($pid)
    {
        $pages = $this->fb_messenger_chat_bot_page_option_setting();
        if($pages && !empty($pages)) {
            try{
                $pages = json_decode($pages, true);
                foreach ($pages as $key => $page){
                    $pageId = $page['id'];
                    if($pageId == $pid){
                        $accessToken = $page['access_token'];
                    }
                }
                if(empty($accessToken)){
                    return false;
                }

                $hostArray = parse_url(admin_url());
                $hostUrl = $hostArray['host'];
                //return $hostUrl;
                $params = array(
                    'setting_type' => 'domain_whitelisting',
                    'whitelisted_domains' => array(
                        $hostUrl,
                    ),
                    'domain_action_type' => 'add',
                );
                $headers = array(
                    'Content-Type' => 'application/json'
                );
                $requestBody = array(
                    'timeout' => 3600,
                    'headers' => $headers,
                    'body' => $params
                );

                $requestUrl = $this->hostSync .'access_token='. $accessToken;
                try{
                    // call  api
                    $result = wp_remote_post($requestUrl, $requestBody);
                    //return $result;
                    // retrieve success code
                    $responseCode = wp_remote_retrieve_response_code($result);

                    if($responseCode == 200){
                        $jsonDecode = json_decode($result['body'], 1);
                        return $jsonDecode;
                    }
                    return false;

                }catch (Exception $exception){
                    return false;
                }
            }catch (Exception $exception){
                return false;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function get_shortcode(){
        return "[fb_messenger_chat_bot-feeds]";
    }


    /**
     * @return string
     */
    public function get_fb_messenger_layout()
    {
        $option = $this->fb_messenger_chat_bot_option_setting();
        $layout = '';
        if($option && isset($option['page_id'])){
            $pageId = $option['page_id'];
            $isEnabled = isset($option['mc_is_enabled'])? $option['mc_is_enabled']: 'false';
            $color = isset($option['background']) ? '#'. $option['background']: "#fffff";
            $position = isset($option['position']) ? $option['position']: '';
            $language = isset($option['language']) ? $option['language']: 'en_US';
        }else{
            return $layout;
        }

        if ($isEnabled == 'true'){
            $layout .= '<style>

            .fb_iframe_widget iframe {
             '. $position .': 0pt !important;
            }
            .fb_reset>div {
            '. $position .': 9pt !important;
            }
        </style>';

        $layout .= '<div id="fb-root"></div>
        <div id="wpfbmb-fb-language" lan="' .$language.'"></div>
        <script>
            window.fbAsyncInit = function () {
                FB.init({
                    xfbml: true,
                    version: "v3.3",
                    autoLogAppEvents: 1
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                var language = d.getElementById("wpfbmb-fb-language").getAttribute("lan");

                js.src = "https://connect.facebook.net/"+language +"/sdk/xfbml.customerchat.js";
                fjs.parentNode.insertBefore(js, fjs);
                
            }(document, "script", "facebook-jssdk"));</script>;'

        .'<div class="fb-customerchat" attribution=setup_tool page_id='. $pageId .' theme_color='.$color.' >
        </div>';
        }
        return $layout;
    }

}
