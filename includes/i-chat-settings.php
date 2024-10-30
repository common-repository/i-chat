<?php

//Create a menu in settings tab
add_action( 'admin_menu', 'i_chat_setting_menu' );
function i_chat_setting_menu() {
    add_options_page(
        'I Chat',
        'I Chat',
        'manage_options',
        'i-chat-plugin',
        'i_chat_options_page'
    );
}


//add_action('admin_init', array($this, 'admin_init'));
function i_chat_options_page() {
    global $wpdb;
    echo admin_url('admin-ajax.php');
    ?>
    
    <div class="wrap">
        <h2>I Chat Visitors</h2>
        <div class="i-chat-tab-divs" id="i-chat-general">
            <div class="i-chat-users">
                <div class="i-chat-users-header">
                    <div class="rowElem">Sr No.</div>
                    <div class="rowElem">Name</div>
                    <div class="rowElem">Email</div>
                    <div class="rowElem">Date</div>
                </div>
                <?php
                $result = "";
                $sql = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."chat_user ORDER BY id DESC");
                if ( $sql )
                {
                    $i = 1;
                    foreach ( $sql as $output ) 
                    {
                        // Create Chat Html
                    ?>
                        
                        <!-- Chat History-->                       
                        <div class="i-chat-users-row" onclick="open_i_chat('<?php echo $output->id;?>')">
                            <a href="javascript:void(0)">
                                <div class="rowElem"><?php echo $i;?></div>
                                <div class="rowElem">
                                    <?php
                                    if ( !empty( $output->name ) ) {
                                        echo $output->name;
                                    }else{
                                        echo $output->chat_ip;
                                    }
                                    ?>
                                    <div class="msg-counter" id="msg-counter<?php echo $output->id;?>"></div>
                                </div>
                                <div class="rowElem">
                                    <?php
                                    if ( !empty( $output->email ) ) {
                                        echo $output->email;
                                    }else{
                                        echo $output->chat_ip;
                                    }
                                    ?>                                    
                                </div>
                                <div class="rowElem">
                                    <?php
                                    echo date("Y M, d", strtotime( $output->in_time ));
                                    ?>                            
                                </div>
                            </a>
                        </div>

                        <!--Chat Window-->
                        <div class="i-chat-admin-window" id="admin-chat-window<?php echo $output->id;?>" style="display:none">
                            <div class="i-chat-header">
                                <div class="i-chat-headTabCont">
                                    <a href="javascript:void(0)">
                                        <div class="i-chat-visiname"><?php echo $output->name;?></div>
                                    </a>
                                </div>
                            </div>
                            <div class="i-chat-wrapper<?php echo $output->id;?>">
                                <div class="i-chat-chatWrap" id="chat_messages<?php echo $output->id;?>">
                                    <?php
                                    $msg = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."chat_messages WHERE user_id='".$output->id."'");
                                    if ( $msg )
                                    {
                                        foreach ( $msg as $msg_output ) 
                                        {
                                            $msg_output->msg_time = date( "d M,Y H:i A ", strtotime( $msg_output->msg_time ) );
                                            if( $msg_output->chat_who == "user" ){
                                            ?>
                                                <div class="i-chat-chatTo-parent">
                                                    <div class="i-chat-chatTo">
                                                        <?php echo $msg_output->chat_msg; ?>
                                                        <div class="i-chat-chatTo-on">
                                                            <?php echo $msg_output->msg_time; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if( $msg_output->chat_who == "organiser" ){
                                            ?>
                                                <div class="i-chat-chatFrom-parent">
                                                    <div class="i-chat-chatFrom">
                                                        <?php echo $msg_output->chat_msg; ?>
                                                        <div class="i-chat-chatFrom-on">
                                                            <?php echo $msg_output->msg_time; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="i-chat-sendChatWrap">
                                <form id="chat_form_admin" action="" method="POST" >
                                    <input type="hidden" name="chat_id" id="chat_id<?php echo $output->id;?>" value="<?php echo $output->id;?>"/>
                                    <input type="hidden" name="chat_who" id="chat_who<?php echo $output->id;?>" value="organiser"/>
                                    <input type="hidden" name="type" id="type<?php echo $output->id;?>" value="chat_msg_admin"/>
                                    <textarea name="chat_msg" class="i-chat-sendArea" id="chat_admin_message<?php echo $output->id;?>"></textarea>
                                    <input type="submit" class="i-chat-send" value="" onclick="return submit_i_chat('<?php echo $output->id;?>')">
                                </form>
                            </div>
                        </div>
                    <?php
                    $i = $i + 1;
                    }
                }
                ?>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        function i_toggle_me(x){
            jQuery("#toggle-"+x).toggle();
        }

        function submit_i_chat(chat_id){
            jQuery.ajax({
                type:"POST",
                url  : "<?php echo admin_url('admin-ajax.php');?>",
                data : { 
                    action: "ichat_ajax_action",
                    chat_id: jQuery("#chat_id"+chat_id).val(),
                    chat_who: jQuery("#chat_who"+chat_id).val(),
                    type: jQuery("#type"+chat_id).val(),
                    msg_box: jQuery("#chat_admin_message"+chat_id).val()
                },
                dataType: 'json',
                success:function(data, status){ 
                    var dNow = new Date();
                    var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                    if ( dNow.getHours() > 12 ) {
                        var ampm = "PM";
                    }else{
                        var ampm = "AM";
                    }
                    var localdate= dNow.getDate() + " "+ ( monthNames[dNow.getMonth( "M" )] ) + ',' + dNow.getFullYear() + ' ' + dNow.getHours() % 12 + ':' + dNow.getMinutes() + " " + ampm;
                    
                    var mine_msg = "";
                    mine_msg += "<div class='i-chat-chatFrom-parent'>";
                        mine_msg += "<div class='i-chat-chatFrom'>";
                            mine_msg += jQuery("#chat_admin_message"+chat_id).val();
                            mine_msg += "<div class='i-chat-chatFrom-on'>";
                                mine_msg += localdate;
                            mine_msg += "</div>";
                        mine_msg += "</div>";
                    mine_msg += "</div>";

                    jQuery("#chat_messages"+chat_id).append(mine_msg);
                    jQuery("#chat_admin_message"+chat_id).val("");
                    scroll_bottom(chat_id);
                },
                error:function(err){
                }
            });
            return false;
        }

        function scroll_bottom( chat_id ){
            jQuery("#chat_messages"+chat_id).animate({
                scrollTop: jQuery("#chat_messages"+chat_id).height() * 1000
            }, 400);
        }

        function open_i_chat( data ){
            jQuery("#admin-chat-window"+data).toggle();
            scroll_bottom( data );
        }
        jQuery("document").ready(function(){
            setInterval(function(){
                // Get Organiser messages
                jQuery.ajax({
                    type:"POST",
                    url: "<?php echo admin_url('admin-ajax.php');?>",
                    data: {
                        action: "ichat_ajax_action",
                        type:"get_chat_messages",
                    },
                    success:function(data){ 
                        var result = JSON.parse(data);
                        for (var i = 0; i < result.length; i++) {
                            var html ="";
                            if( result[i].chat_who == "user" ){
                                html += "<div class='i-chat-chatTo-parent'>";
                                    html += "<div class='i-chat-chatTo'>";
                                        html += result[i].chat_msg;
                                        html += "<div class='i-chat-chatTo-on'>";
                                            html += result[i].msg_time;
                                        html += "</div>";
                                    html += "</div>";
                                html += "</div>";
                            }
                            if( result[i].chat_who == "organiser" ){
                                html += "<div class='i-chat-chatFrom-parent'>";
                                    html += "<div class='i-chat-chatFrom'>";
                                        html += result[i].chat_msg;
                                        html += "<div class='i-chat-chatFrom-on'>";
                                            html += result[i].msg_time;
                                        html += "</div>";
                                    html += "</div>";
                                html += "</div>";   
                            }

                            if ( html != null && html != "" ) {
                                jQuery("#chat_messages"+result[i].user_id).append(html);
                                jQuery("#msg-counter"+result[i].user_id).css({"display":"inline-block"});
                                scroll_bottom();    
                            }
                        }
                    },
                    error:function(err){
                    }
                });
            },3000);
        });
        
    </script>
    <?php
}
?>