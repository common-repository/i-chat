<?php
/**
 * Plugin Name: I Chat
 * Plugin URI: 
 * Description: A cool chatting plugin, To chat with your web vistors.
 * Version: 1.0.0
 * Author: Azhar Khan
 * Author URI: mailto:mazharahmedkhan010@gmail.com
 * License: GPL2
 */

//Defining Constants

include_once( dirname( __FILE__ ) . '/includes/i-chat-settings.php' );
include_once( dirname( __FILE__ ) . "/i-functions.php");
include_once( dirname( __FILE__ ) . "/i-chat-calls.php");
$app_obj = new I_DB_FUNCTIONS();


if ( !defined( 'CHAT_BASE_URL' ) ) {
	define( 'CHAT_BASE_URL' , dirname( __FILE__ ));
}
if ( !defined( 'CHAT_HOST_URL' ) ) {
	define( 'CHAT_HOST_URL' , plugins_url() . "/i-chat/" );
}

function add_chat_popup() {
?>
  <script type="text/javascript">
    jQuery("document").ready(function(){
      jQuery(window).load(function(){
        open_chat_window();
      });
    });
    
    function open_chat_window(){
    	if ( jQuery( "#i-chat-window iframe" ).length>0 ) {
    		if ( document.getElementsByClassName('i-cht-hide').length > 0 ) {
            jQuery("#i_chat_btn").css({"display":"none"});
            var getclass = document.getElementById( 'i-chat-window' ).className = "i-chat-window";
        }else{
            jQuery("#i_chat_btn").css({"display":"block"});
            var getclass = document.getElementById( 'i-chat-window' ).className = "i-chat-window i-cht-hide i-cht-hide";
        }
    	}else{

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
          jQuery("#i-chat-window").addClass( "ssychatMobwin" );
          jQuery("#i-chat-window-frame").addClass( "ssychatMobfram" );
        }
         

        jQuery("#i_chat_btn").css({"display":"none"});
    		jQuery( "#i-chat-window" ).html("<iframe src='<?php echo admin_url("admin-ajax.php");?>?action=ichat_window_action' class='i-chat-window-frame' id='i-chat-window-frame'></iframe>");	
    	}
    }
  </script>
	
	
	<a class="flotappchat" href="javascript:void(0)" onclick="open_chat_window()">
    <img src="<?php echo CHAT_HOST_URL; ?>img/chat-trigger.png" id="i_chat_btn">  
  </a>
	<div class="i-chat-window" id="i-chat-window" style="right:0px;">
	</div>
<?php
}

add_action('wp_footer', 'add_chat_popup');

function test(){
    wp_enqueue_style( 'i_chat_window_styles', plugin_dir_url(__FILE__) . 'css/i-chat-window.css' );
}

//Include Javascript and css
function i_chat_scripts() {
    wp_enqueue_style( 'i_chat_styles', plugin_dir_url(__FILE__) . 'css/i-chat-style.css' );
    wp_enqueue_script( 'i_chat_cookie', plugin_dir_url(__FILE__) . 'js/jquery.cookie.js', array(), '1.0.0', true );
    wp_enqueue_style( 'i_chat_window_styles', plugin_dir_url(__FILE__) . 'css/i-chat-window.css' );
}
add_action( 'wp_enqueue_scripts', 'i_chat_scripts' );

function i_chat_admin() {
    wp_enqueue_style( 'i-chat-admin-style', plugin_dir_url(__FILE__) . 'css/i-chat-admin-style.css' );
}
add_action( 'admin_enqueue_scripts', 'i_chat_admin' );