<?php
require_once( ABSPATH . '/wp-config.php' );
class I_DB_FUNCTIONS {
		
	function __construct() {
		global $wpdb;

		//Create Chat User Table
		$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."chat_user (
	          	id bigint(20) NOT NULL auto_increment,
	          	name varchar(255) default NULL,
	          	email varchar(255) default NULL,
	          	chat_ip varchar(100) default NULL,
	          	in_time varchar(100) default NULL,
	          	PRIMARY KEY  (id)
	        )";
	    $results = $wpdb->query($sql);

	    //Create Chat Messages Table
	 	$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."chat_messages (
	          	id bigint(20) NOT NULL auto_increment,
	          	user_id int(11) default NULL,
	          	chat_msg Text default NULL,
	          	msg_time varchar(100) default NULL,
	          	chat_who varchar(10) default NULL,
	          	chat_seen_u varchar(10) default NULL,
	          	chat_seen_a varchar(10) default NULL,
	          	PRIMARY KEY  (id)
	        )";
	    $results = $wpdb->query($sql);
	}

	// Insert new chat User
	function register_chat_user( $name, $email ){
		global $wpdb;

		$chk_user = $wpdb->get_results("select * from ".$wpdb->prefix."chat_user where email = '".$email."'");
		if ( $chk_user )
		{
			$new_id = $chk_user[0]->id;
		}else{
			$wpdb->insert( 
				$wpdb->prefix.'chat_user', 
				array( 
					'name' => $name, 
					'email' => $email,
					'chat_ip' => $_SERVER['REMOTE_ADDR'],
					'in_time' => date("Y-m-d H:i:s")
				) 
			);
			$new_id = $wpdb->insert_id;
		}
		return $new_id;
	}

	// Save User chantting Messages
	function chatting_messages( $chat_id, $chat_msg, $chat_who ){

		global $wpdb;
		$wpdb->insert( 
			$wpdb->prefix.'chat_messages', 
			array( 
				'user_id' => $chat_id, 
				'chat_msg' => $chat_msg,
				'msg_time' => date( "Y-m-d H:i:s" ),
				'chat_who' => $chat_who,
				'chat_seen_u' => 'yes',
				'chat_seen_a' => 'no',
			) 
		);
		
		$wpdb->update( 
			$wpdb->prefix.'chat_messages', 
			array( 
				'chat_seen_u' => 'yes',	// string
			), 
			array( 'user_id' => $chat_id )
		);
		return "done";
	}

	// Chatting Admin Messages
	function chatting_admin_messages( $chat_id, $chat_msg ){
		$msg_time = date( "Y-m-d H:i:s" );
		$chat_who = "organiser";
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix.'chat_messages', 
			array( 
				'user_id' => $chat_id, 
				'chat_msg' => $chat_msg,
				'msg_time' => $msg_time,
				'chat_who' => $chat_who,
				'chat_seen_a' => 'yes',	// string
			)
		);
	}

	//Get All Messages By User
	function get_all_chat_messages( $chat_id ){
		global $wpdb;
		$result = "";
		
		$sql = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."chat_messages WHERE user_id = '".$chat_id."'");
		if ( $sql )
		{
			foreach ( $sql as $output ) 
			{
				$output->msg_time = date( "d M,Y H:i A ", strtotime( $output->msg_time ) );
				$result[] = $output;
			}
		}
		return( $result );
	}

	//Get All Messages By Admin
	function get_all_chat_messages_organiser( $chat_id ){
		global $wpdb;
		$result = "";

		$sql = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."chat_messages WHERE user_id = '".$chat_id."' AND ( chat_seen_u = 'no' OR chat_seen_u IS NULL)");
		if ( $sql )
		{
			foreach ( $sql as $output ) 
			{
				$output->msg_time = date( "d M,Y H:i A ", strtotime( $output->msg_time ) );
				$result[] = $output;
			}
		}
		$wpdb->update( 
			$wpdb->prefix.'chat_messages', 
			array( 
				'chat_seen_u' => 'yes',	// string
			), 
			array( 'user_id' => $chat_id )
		);
		return( $result );
	}

	//Get All Messages By User
	function get_all_chat_messages_user( $chat_id ){
		global $wpdb;
		$result = "";

		$sql = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."chat_messages WHERE user_id = '".$chat_id."' AND ( chat_seen_a = 'no' OR chat_seen_a IS NULL)");
		if ( $sql )
		{
			foreach ( $sql as $output ) 
			{
				$output->msg_time = date( "d M,Y H:i A ", strtotime( $output->msg_time ) );
				$result[] = $output;
			}
		}
		$wpdb->update( 
			$wpdb->prefix.'chat_messages', 
			array( 
				'chat_seen_a' => 'yes',	// string
			), 
			array( 'user_id' => $chat_id )
		);
		return( $result );
	}

	//Get Chat Messages unseen
	function get_chat_messages_unseen(){
		global $wpdb;
		$result = "";
		$get_msg = $wpdb->get_results("select * from ".$wpdb->prefix."chat_messages where chat_seen_a = 'no'");
		if ( $get_msg )
		{
			foreach ($get_msg as $output) {
				$output->msg_time = date( "d M,Y H:i A ", strtotime( $output->msg_time ) );
				$result[] = $output;
				$wpdb->update(
					$wpdb->prefix.'chat_messages', 
					array( 
						'chat_seen_a' => 'yes',	// string
					), 
					array( 'user_id' => $output->user_id )
				);
			}
		}
		return( $result );
	}
}
?>