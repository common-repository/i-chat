<?php
include_once("i-functions.php");
add_action( 'wp_ajax_ichat_ajax_action', 'ichat_callback' );
add_action( 'wp_ajax_nopriv_ichat_ajax_action', 'ichat_callback' );
function ichat_callback() {
	//Add Class and Create class object
	include_once("i-functions.php");
	$app_obj = new I_DB_FUNCTIONS();

	//Add User/Login User
	if( isset( $_POST['type'] ) && $_POST['type'] == "Login" ){
		$get_id = $app_obj->register_chat_user( $_POST['name'], $_POST['email'] );
		echo json_encode( $get_id );
		die();
	}

	// Save User Messages
	if( isset( $_POST['chat_msg'] ) && isset( $_POST['type'] ) && $_POST['type'] == "chat_msg" ){
		$get_id = $app_obj->chatting_messages( $_POST['chat_id'], $_POST['chat_msg'], $_POST['chat_who'] );
		echo $get_id;
		die();
	}

	//Get All Chat Messages
	if( isset( $_POST['type'] ) && $_POST['type'] == "all_chat_msg" && isset( $_POST['chat_id'] ) && !empty( $_POST['chat_id'] ) ){
		$get_id = $app_obj->get_all_chat_messages( $_POST['chat_id'] );
		echo json_encode( $get_id );
		die();
	}

	//Get All Organisers
	if( isset( $_POST['type'] ) && $_POST['type'] == "all_chat_msg_organiser" && isset( $_POST['chat_id'] ) && !empty( $_POST['chat_id'] ) ){
		$get_id = $app_obj->get_all_chat_messages_organiser( $_POST['chat_id'] );
		echo json_encode( $get_id );
		die();
	}

	//Get All Users Messages
	if( isset( $_POST['type'] ) && $_POST['type'] == "all_chat_msg_user" && isset( $_POST['chat_id'] ) && !empty( $_POST['chat_id'] ) ){
		$get_id = $app_obj->get_all_chat_messages_user( $_POST['chat_id'] );
		echo json_encode( $get_id );
		die();
	}

	//Save Organiser Messages
	if ( isset( $_REQUEST['chat_id'] ) && isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "chat_msg_admin" ) {
		$get_chats = $app_obj->chatting_admin_messages( $_REQUEST['chat_id'], $_REQUEST['msg_box'] );
		echo json_encode( "sent" );
		die();
	}

	//Get Chat Users New
	if ( isset( $_REQUEST['type'] ) && $_REQUEST['type'] == "get_chat_messages" ) {
		$get_chats = $app_obj->get_chat_messages_unseen();
		echo json_encode( $get_chats );
		die();
	}
}

// Calling Chat window
add_action( 'wp_ajax_ichat_window_action', 'ichat_call_window' );
add_action( 'wp_ajax_nopriv_ichat_window_action', 'ichat_call_window' );
function ichat_call_window() {    
	wp_enqueue_style( 'i_chat_window_styles', plugin_dir_url(__FILE__) . 'css/i-chat-window.css' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'i_chat_cookie', plugin_dir_url(__FILE__) . 'js/jquery.cookie.js', array(), '1.0.0', true );
	wp_enqueue_script( 'i_chat_functions', plugin_dir_url(__FILE__) . 'js/i-chat-functions.js', array(), '1.0.0', true );
	
	remove_action('admin_enqueue_scripts', 'wp_auth_check_load');
    remove_action('admin_enqueue_scripts', 'i_chat_admin');

    echo wp_iframe( 'i_chat_window' );
    //include( plugin_dir_path( __FILE__ ) . 'i-chat-window.php' );

    die(); // this is required to return a proper result
}

// Create Chat Window
function i_chat_window() {
    ?>
    <script type="text/javascript">
    var i_chat_admin_url = "<?php echo admin_url('admin-ajax.php');?>";
    </script>
    <?php
    if ( isset ( $_COOKIE['user_id'] ) && !empty( $_COOKIE['user_id'] ) ) {
        echo '<div class="i-chat-header">';
            echo '<div class="i-chat-headTabCont">';
                echo '<a href="javascript:void(0)" onclick="show_hide()">';
                    echo '<div class="i-chat-visiname">Live Chat</div>';
                echo '</a>';
                echo '<a href="javascript:void(0)" onclick="close_chat()">';
                    echo '<div class="i-chat-close"></div>';
                echo '</a>';
            echo '</div>';
        echo '</div>';
        echo '<div class="i-chat-wrapper">';
            echo '<div class="i-chat-chatWrap" id="chat_messages">';
            echo '</div>';
        echo '</div>';
        echo '<div class="i-chat-sendChatWrap">';
            echo '<form id="chat_form" action="" method="POST">';
            	$user_id = "";
            	if (isset( $_COOKIE['user_id'] ) && !empty( $_COOKIE['user_id'] ) ) { 
            		$user_id = $_COOKIE['user_id'];
            	}
                echo '<input type="hidden" name="chat_id" id="chat_id" value="'.$user_id.'"/>';
                echo '<input type="hidden" name="chat_who" id="chat_who" value="user"/>';
                echo '<input type="hidden" name="type" id="type" value="chat_msg"/>';
                echo '<textarea class="i-chat-sendArea" name="chat_msg" id="chat_msg" placeholder="Type a message"></textarea>';
                echo '<input type="submit" class="i-chat-send" value="">';
            echo '</form>';
        echo '</div>';
    } else {
        echo '<div class="i-chat-header">';
           echo '<div class="i-chat-headTabCont">';
                echo '<a href="javascript:void(0)" onclick="show_hide()">';
                    echo '<div class="i-chat-visiname">Let\'s talk? - Online</div>';
                echo '</a>';
                echo '<a href="javascript:void(0)" onclick="close_chat()">';
                    echo '<div class="i-chat-close"></div>';
                echo '</a>';
            echo '</div>';
        echo '</div>';
        echo '<div class="i-chat-wrapper">';
            echo '<div class="i-chat-chatLogCont">';
                echo '<div class="i-chat-chatNote">Please fill out the form below to start chatting with the next available agent.</div>';
                echo '<form id="chat_login" action="" method="post">';
                    echo '<input type="text" id="name" name="name" class="i-chat-chatfieldTxt" placeholder="Name"/>';
                    echo '<input type="text" id="email" name="email" class="i-chat-chatfieldTxt" placeholder="Email">';
                    echo '<input type="hidden" name="type" id="type" value="Login"/>';
                    echo '<input type="submit" class="i-chat-chatSubmit" value="Start Chat" />';
                echo '</form>';
            echo '</div>';
        echo '</div>';
    }
}
?>