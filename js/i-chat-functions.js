jQuery("document").ready(function(){
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        var d = window.parent.document.getElementById("i-chat-window-frame");
        d.className += " ssychatMobfram";
    }

    // Chat Registration
    jQuery("#chat_login").submit(function(e){
        e.preventDefault();
        if ( jQuery("#name").val().trim() == "" ) {
            alert("Please enter your name");
            return false;
        }
        else if ( jQuery("#email").val().trim() == "" ) {
            alert("Please enter your email");
            return false;
        }else{
            jQuery.ajax({
                type:"POST",
                url  : i_chat_admin_url,
                data : { 
                    action: "ichat_ajax_action",
                    name: jQuery("#name").val(),
                    email: jQuery("#email").val(),
                    type: jQuery("#type").val()
                },
                dataType: 'json',
                success:function(data, status){ 
                    if ( data != "" ) {
                        jQuery.cookie("user_id", data, { expires : 1,  path: '/' });
                        window.location.reload();
                    }
                },
                error:function(err){
                }
            });
        }
    });

    //Chat messages
    jQuery("#chat_form").submit(function(e){
        e.preventDefault();
        if ( jQuery("#chat_msg").val().trim() != "" ) {
            jQuery.ajax({
                type:"POST",
                url: i_chat_admin_url,
                data: { 
                    action: "ichat_ajax_action",
                    chat_id: jQuery("#chat_id").val(),
                    chat_who: jQuery("#chat_who").val(),
                    type: jQuery("#type").val(),
                    chat_msg: jQuery("#chat_msg").val()
                },
                success:function(data){ 
                    var dNow = new Date();
                    var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
                    if ( dNow.getHours() > 12 ) {
                        var ampm = "PM";
                    }else{
                        var ampm = "AM";
                    }
                    var localdate= dNow.getDate() + " "+ ( monthNames[dNow.getMonth( "M" )] ) + ',' + dNow.getFullYear() + ' ' + dNow.getHours() % 12 + ':' + dNow.getMinutes() + " " + ampm;
                    
                    var mine_msg = "";
                    mine_msg += "<div class='i-chat-chatTo-parent'>";
                        mine_msg += "<div class='i-chat-chatTo'>";
                            mine_msg += jQuery("#chat_msg").val();
                            mine_msg += "<div class='i-chat-chatTo-on'>";
                                mine_msg += localdate;
                            mine_msg += "</div>";
                        mine_msg += "</div>";
                    mine_msg += "</div>";

                    jQuery("#chat_messages").append(mine_msg);
                    jQuery("#chat_msg").val("");
                    scroll_bottom();
                },
                error:function(err){
                }
            });
        }
    });
    
    // Get all messages
    jQuery.ajax({
        type:"POST",
        url: i_chat_admin_url,
        data: {
            action: "ichat_ajax_action",
            type:"all_chat_msg",
            chat_id:jQuery.cookie("user_id")
        },
        success:function(data){ 
            var result = JSON.parse(data);
            var html = "";
            for (var i = 0; i < result.length; i++) {
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
            };
            jQuery("#chat_messages").html(html);
            scroll_bottom();
        },
        error:function(err){
        }
    });

    setInterval(function(){
        // Get Organiser messages
        jQuery.ajax({
            type:"POST",
            url: i_chat_admin_url,
            data: {
                action: "ichat_ajax_action",
                type:"all_chat_msg_organiser",
                chat_id:jQuery.cookie("user_id")
            },
            success:function(data){ 
                var result = JSON.parse(data);
                var html ="";
                for (var i = 0; i < result.length; i++) {
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
                }
                //alert( html );
                if ( html != null && html != "" ) {
                	jQuery("#chat_messages").append(html);
                	scroll_bottom();	
                }
            },
            error:function(err){
            }
        });
    },3000);
});
function show_hide(){
    if ( window.parent.document.getElementsByClassName('i-cht-hide').length > 0 ) {
        var getclass = window.parent.document.getElementById( 'i-chat-window' ).className = "i-chat-window";
    }else{
        var getclass = window.parent.document.getElementById( 'i-chat-window' ).className = "i-chat-window i-cht-hide i-cht-hide";
    }
}
function close_chat() {
    var x = confirm( "If you close the window than your chat session will be end you need to relogin to chat again" );
    if ( x == true ) {
        jQuery.removeCookie("user_id");
        window.parent.document.getElementById("i_chat_btn").style.display = "block";
        window.parent.document.getElementById("i-chat-window-frame").remove();    
    }	
}
function scroll_bottom(){
    jQuery("body, html").animate({
        scrollTop: jQuery(document).height()
    }, 400);
}