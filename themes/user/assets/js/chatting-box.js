var chatting_handler={
    total_popups: 0,
    popups: null,
    unread_message_data:{},
    init:function () {
        //this variable represents the total number of popups can be displayed according to the viewport width
        this.total_popups = 0;
        //arrays of popups ids
        this.popups = [];
        $("body").append('<div class="chatbox-wrap"></div>');
        window.addEventListener("resize", this.calculate_popups());
        window.addEventListener("load", this.calculate_popups());
        this.bindEvents();
        //this.startMessageTracker(150000);
    },
    startMessageTracker:function (step) {
        var self=this;
        var post_data={
            method:"check_my_unread_messages",
            token:token
        };
        ajax_post(post_data,function (res) {
            try {
                var obj=$.parseJSON(res);
                console.log(obj);
                if(obj.result=="OK"){
                    var items=obj.data;
                    for(var i in items){
                        var item=items[i];
                        if(self.unread_message_data[item.service_instance_id]!=item.count){
                            console.log('show chatting box');
                            self.unread_message_data[item.service_instance_id]=item.count;
                        }
                    }
                }
                console.log(self.unread_message_data);
            }catch(e){
                console.log(res);
            }
        });
        setTimeout(function () {
            self.startMessageTracker(step);
        },step);
    },
    array_remove: function(array, from, to) {
        var rest = array.slice((to || from) + 1 || array.length);
        array.length = from < 0 ? array.length + from : from;
        return array.push.apply(array, rest);
    },
    register_popup: function(id, name,data) {
        var self=this;
        for(var iii = 0; iii < self.popups.length; iii++)
        {
            //already registered. Bring it to front.
            if(id == self.popups[iii])
            {
                self.array_remove(self.popups, iii);
                self.popups.unshift(id);
                self.calculate_popups();
                $("#"+id).removeClass("box-closed");
                $("#"+id+" .chat").css("display","block");
                $("#"+id+" .chat-message-input").focus();
                return;
            }
        }
        var service_instance=$.parseJSON(str_decrypt(data));
        console.log("service_instance:");
        console.log(service_instance);
        var user_image=service_instance.user_image;
        var element = '<div class="chat-popup live-chat" id="'+ id +'" user_image="'+user_image+'" data="'+data+'" chatting_page_num="0">';

        element = element + '<div class="popup-head" data-active-color="rose">';
        element = element + '<div class="popup-head-left">'+ name +'</div>';
        element = element + '<div class="popup-head-right"><a class="chatbox-close">&#10005;</a></div>';
        element = element + '<span class="chat-message-counter">0</span>';
        element = element + '<div style="clear: both"></div>';
        element = element + '</div>';
        element = element + '<div class="chat">';
        element = element + '<div class="chat-history">';
        element = element + '</div>';
        element = element + '<p class="chat-feedback">&nbsp;</p>';
        element = element + '<form action="#" method="post">';
        element = element + '<fieldset>';
        element = element + '<textarea rows="1" class="chat-message-input" name="chat-message-input" placeholder="Type your message..." autofocus></textarea>';
        element = element + '<input type="hidden">';
        element = element + '</fieldset>';
        element = element + '</form>';
        element = element + '</div>';

        this.load_chatting_history(service_instance,function (html) {
            $(".chatbox-wrap").prepend(element);
            $("#"+id+" .chat-history").prepend(html);
            self.popups.unshift(id);
            self.calculate_popups();
            var scrill_height=$("#"+id+" .chat-history").prop("scrollHeight");
            console.log(scrill_height);
            $("#"+id+" .chat-history").animate({ scrollTop: scrill_height }, 10);
            $("#"+id+" .chat-message-input").focus();
        });
    },
    load_chatting_history:function (service_instance,callback) {
        var self=this;
        var html="";
        var chat_id="chat-box-"+service_instance.service_instance_id;
        console.log(chat_id);
        if($("#"+chat_id).attr("full_loaded")=='1'){
            return false;
        }
        var page_num=0;
        var old_chatting_page_num=$("#"+chat_id).attr("chatting_page_num");
        if(old_chatting_page_num==undefined || old_chatting_page_num==""){
            page_num=0;
        }else{
            page_num=parseInt(old_chatting_page_num)+1;
        }
        console.log(page_num);
        var user_image=service_instance.user_image;
        if(user_image==""){
            user_image=base_url+"assets/img/default-avatar.png";
        }
        var post_data={
            method:"get_chatting_history",
            service_instance_id:service_instance.service_instance_id,
            page_num:page_num,
            limit:10,
            token:token
        };
        ajax_post(post_data,function (res) {
            var obj=$.parseJSON(res);
            console.log(obj);
            var chatting_history=obj.data;
            for(var i = chatting_history.length-1; i>=0; i--){
                var record=chatting_history[i];
                var chat_timestamp=parseInt(record.chatting_timestamp);
                var show_image_class="";
                var show_time_class="";
                if(i!=chatting_history.length-1){
                    var last_timestamp=parseInt(chatting_history[i+1].chatting_timestamp);
                    if(chat_timestamp-last_timestamp<3600*24 /*&& chatting_history[i+1].sender_id == record.sender_id*/){
                        show_time_class="hidden";
                    }
                }
                if(record.sender_id==member_info.user_id){ //my message(class='me')
                    if(i!=chatting_history.length-1 && show_time_class=="hidden"){
                        if(chatting_history[i+1].sender_id == record.sender_id){
                            show_image_class="me-too";
                        }
                    }
                    html+='<div class="chat-message-row me '+show_image_class+'" chat_timestamp="'+chat_timestamp+'">';
                    html+='<div class="chat-timestamp '+show_time_class+'">'+'____   '+self.get_full_date(chat_timestamp)+'   ____'+'</div>';
                    html+='<div class="chat-message-content">';
                    html+='<div class="chat-message-text">'+record.chatting_content+'</div>';
                    html+='<div class="clearfix"></div>';
                    html+='</div>';
                    html+='</div>';
                }else{ //your message(class='you')
                    if(i!=chatting_history.length-1 && show_time_class=="hidden"){
                        if(chatting_history[i+1].sender_id == record.sender_id){
                            show_image_class="you-too";
                        }
                    }
                    html+='<div class="chat-message-row you '+show_image_class+'" chat_timestamp="'+chat_timestamp+'">';
                    html+='<div class="chat-timestamp '+show_time_class+'">'+'____   '+self.get_full_date(chat_timestamp)+'   ____'+'</div>';
                    html+='<img src="'+user_image+'" alt="" width="32" height="32">';
                    html+='<div class="chat-message-content">';
                    html+='<div class="chat-message-text">'+record.chatting_content+'</div>';
                    html+='<div class="clearfix"></div>';
                    html+='</div>';
                    html+='</div>';
                }
            }
            if(chatting_history.length>0){
                $("#"+chat_id).attr("chatting_page_num",page_num);
                $("#"+chat_id).attr("full_loaded",'0');
            }else{
                $("#"+chat_id).attr("full_loaded",'1');
            }
            callback(html);
        });
    },
    close_popup: function (id) {
        for(var iii = 0; iii < this.popups.length; iii++)
        {
            if(id == this.popups[iii])
            {
                this.array_remove(this.popups, iii);
                $("#"+id).remove();
                this.calculate_popups();
                return;
            }
        }
    },
    display_popups: function () {
        var right = 20;
        var iii = 0;
        for(iii; iii < this.total_popups; iii++) {
            if(this.popups[iii] != undefined) {
                var element = document.getElementById(this.popups[iii]);
                element.style.right = right + "px";
                right = right + 320;
                element.style.display = "block";
            }
        }
        for(var jjj = iii; jjj < this.popups.length; jjj++) {
            var element = document.getElementById(this.popups[jjj]);
            element.style.display = "none";
        }
    },
    calculate_popups: function () {
        var width = window.innerWidth;
        if(width < 540) {
            this.total_popups = 0;
        } else {
            width = width - 200;
            //320 is width of a single popup box
            this.total_popups = parseInt(width/320);
        }
        this.display_popups();
    },
    bindEvents: function () {
        var self=this;
        $(document).ready(function() {
            $('body').on('click','.live-chat', function(e) {
                $(this).removeClass("has-unread-message");
            });
            $('body').on('click','.live-chat .chatbox-close', function(e) {
                //if(e.target.className == "chatbox-close"){
                    var id = $(this).closest(".live-chat").attr('id');
                    chatting_handler.close_popup(id);
                //}
            });
            $('body').on('click','.live-chat .popup-head', function(e) {
                //if(e.target.className == "popup-head"){
                var id = $(this).closest(".live-chat").attr('id');
                $(this).closest('.live-chat').find('.chat').slideToggle(300, 'swing');
                var chat_id = $(this).closest('.live-chat').attr('id');
                if($(this).closest('.live-chat').hasClass("box-closed")){
                    $(this).closest('.live-chat').find('.chat-message-counter').html("0");
                    $(this).closest('.live-chat').find('.chat-message-counter').css("display","none");
                    $(this).closest('.live-chat').removeClass("box-closed");
                    var scrill_height=$("#"+chat_id+" .chat-history").prop("scrollHeight");
                    console.log(scrill_height);
                    $("#"+chat_id+" .chat-history").animate({ scrollTop: scrill_height },1);
                }else{
                    $(this).closest('.live-chat').addClass("box-closed");
                }
                //}
            });
            $("body").on("keypress",".chat-message-input",function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    var message=$.trim($(this).val());
                    if(message!=""){
                        var chat_id=$(this).closest(".live-chat").attr("id");
                        //self.receive_message(chat_id, message);
                        self.send_message(chat_id, message);
                    }
                    $(this).val("");
                    return false;
                }
                $(this).closest(".live-chat").removeClass("has-unread-message");
            });
            $("body").on("wheel",".chat-history",function (event) {
                var id = $(this).closest(".live-chat").attr("id");
                var scroll_top=$(this).scrollTop();
                if(scroll_top==0){
                    var is_loading = $(this).closest(".live-chat").attr('chatting_data_loading');
                    if(is_loading!=1){
                        $("#"+id).attr('chatting_data_loading',1);
                        var service_instance=$.parseJSON(str_decrypt($(this).closest(".live-chat").attr("data")));
                        self.load_chatting_history(service_instance,function (html) {
                            $("#"+id+" .chat-history").prepend(html);
                            $("#"+id).attr('chatting_data_loading',0);
                        });
                    }
                }
            });

        });
    },
    receive_message:function (chat_id, box_title, message,data) {
        var self=this;
        if($("#"+chat_id).length==0){
            chatting_handler.register_popup(chat_id, box_title,data);
        }
        var now=$.now();
        var chat_timestamp=parseInt(now/1000);
        var user_image=$("#"+chat_id).attr('user_image');
        var show_image_class="";
        if($("#"+chat_id+" .chat-history .chat-message-row:last-child").hasClass('you')){
            show_image_class="you-too";
        }
        var show_time_class="";
        var last_timestamp=$("#"+chat_id+" .chat-history .chat-message-row:last-child").attr('chat_timestamp');
        if(chat_timestamp-parseInt(last_timestamp)<3600*24){
            show_time_class="hidden";
        }
        $("#"+chat_id).addClass('has-unread-message');
        var html='';
        html+='<div class="chat-message-row you '+show_image_class+'" chat_timestamp="'+chat_timestamp+'">';
        html+='<div class="chat-timestamp '+show_time_class+'">'+'____   '+self.get_full_date(chat_timestamp)+'   ____'+'</div>';
        html+='<img src="'+user_image+'" alt="" width="32" height="32">';
        html+='<div class="chat-message-content">';
        html+='<div class="chat-message-text">'+message+'</div>';
        html+='<div class="clearfix"></div>';
        html+='</div>';
        html+='</div>';
        $("#"+chat_id+" .chat-history").append(html);

        if($("#"+chat_id).hasClass("box-closed")){
            $("#"+chat_id).find('.chat-message-counter').css("display","block");
            var cnt=$("#"+chat_id).find('.chat-message-counter').html();
            cnt=parseInt(cnt)+1;
            if(cnt>100){
                cnt="100+";
            }
            $("#"+chat_id).find('.chat-message-counter').html(cnt);
        }else{
            var scrill_height=$("#"+chat_id+" .chat-history").prop("scrollHeight");
            console.log(scrill_height);
            $("#"+chat_id+" .chat-history").animate({ scrollTop: scrill_height }, 700);
        }

    },
    send_message:function (chat_id, message) {
        var self=this;
        var data=$.parseJSON(str_decrypt($("#"+chat_id).attr('data')));
        var now=$.now();
        var chat_timestamp=parseInt(now/1000);
        console.log(chat_timestamp);
        var user_image=$("#"+chat_id).attr('user_image');
        var show_image_class="";
        if($("#"+chat_id+" .chat-history .chat-message-row:last-child").hasClass('me')){
            show_image_class="me-too";
        }
        var show_time_class="";
        var last_timestamp=$("#"+chat_id+" .chat-history .chat-message-row:last-child").attr('chat_timestamp');
        if(chat_timestamp-parseInt(last_timestamp)<3600*24){
            show_time_class="hidden";
        }
        var post_data={
            method:"send_message",
            token:token,
            service_instance_id:data.service_instance_id,
            chatting_content:message,
            receiver_id:data.user_id
        };
        $("#"+chat_id+" .chat-feedback").html('Sending message...');
        ajax_post(post_data,function (res) {
            try {
                var obj=$.parseJSON(res);
                if(obj.result=="OK"){
                    var html='';
                    html+='<div class="chat-message-row me '+show_image_class+'" chat_timestamp="'+chat_timestamp+'">';
                    html+='<div class="chat-timestamp '+show_time_class+'">'+'____   '+self.get_full_date(chat_timestamp)+'   ____'+'</div>';
                    html+='<div class="chat-message-content">';
                    html+='<div class="chat-message-text">'+message+'</div>';
                    html+='<div class="clearfix"></div>';
                    html+='</div>';
                    html+='</div>';
                    $("#"+chat_id+" .chat-history").append(html);
                    var scrill_height=$("#"+chat_id+" .chat-history").prop("scrollHeight");
                    console.log(scrill_height);
                    $("#"+chat_id+" .chat-history").animate({ scrollTop: scrill_height }, 700);
                }
            }catch(e){
                console.log(res);
            }
            $("#"+chat_id+" .chat-feedback").html('&nbsp;');
        });
    },
    get_full_date: function (unix_timestamp) {
        var monthNames = ["", "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var date = new Date(parseInt(unix_timestamp)*1000);
        var hours = "0" + date.getHours();
        var minutes = "0" + date.getMinutes();
        var seconds = "0" + date.getSeconds();
        var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

        var year = date.getFullYear();
        var month = date.getMonth()+1;
        var day = date.getDate();
        var full_date=monthNames[month]+" "+day+", "+year+" - "+hours.substr(-2)+":"+minutes.substr(-2);
        return full_date;
    },
    get_timestamp: function (full_date) {
        return new Date(full_date).getTime()/1000;
    },
};
$(document).ready(function () {
    chatting_handler.init();
});

var Chat = {
    socket:null,
    connect:0,//1:connected,0:disconnected
    interval:1,
    hasmore : true,
    //show message step
    last_draw_time: new Date().getTime(),
    getconnect:function(){
        var self = this;
        clearInterval(self.interval);
        if ( io !== undefined && io !==null ) {
            self.socket = io.connect(CHAT_CONNECT_URL);
            console.log('emit authorization...........');
            self.socket.emit('authorization', { user_id : member_info.user_id });

            self.socket.on('message', function(data){
                data=JSON.parse(data);
                console.log(data);
                /*------------------------------------------------------------------------------------------------
                 - notification type:
                 0: chatting notification
                 1: award notification
                 2: accept notification
                 3: swap award notification
                 4: swap accept notification
                 41: swap pay notification
                 5: cancel notification
                 6: reject notification
                 7: delete notification
                 8: change notification
                 100: admin notification
                 ------------------------------------------------------------------------------------------------*/
                var service_instance=data.data.service_instance;
                if(data.type=='0'){
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    chatting_handler.receive_message('chat-box-'+service_instance.service_instance_id, service_instance.user_name+": "+service_instance.service_name,data.data.message,str_encrypt(JSON.stringify(service_instance)));
                }else if(data.type=='1'){ //award notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='2'){ //accept notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='3'){ //swap award notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='4'){ //swap accept notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='41'){ //swap pay notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='5'){ //cancel notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='6'){ //reject notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='7'){ //delete notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='8'){ //change notification
                    service_instance=data.data.service_instance;
                    console.log(service_instance);
                    demo.showNotification(data.data.message);
                    try {
                        managed_services.loadData();
                    } catch (e){ }
                    try {
                        subscribed_services.loadData();
                    } catch (e){ }
                }else if(data.type=='100'){ //admin notification
                    demo.showNotification(data.data.message);
                }
            });

            self.socket.on('disconnect', function () {
                self.connect = 0;
                self.interval = setInterval( self.getconnect, 120000);//断开1分钟后重新连接服务器
            });
            //callback && callback();
        }
    },//Get new connect by socket.io
    disconnect:function(){
    },//Disconnect current session
    init:function(){
        var self = this;
        self.getconnect();
    }
};
$(document).ready(function () {
    console.log('chat init:');
    Chat.init();
});

var notification_handler={
    init:function () {
        var self=this;
        self.load();
        self.bindEvents();
    },
    load:function () {
        var self=this;
        self.loadData(function (obj) {
            self.drawUI(obj);
        });
    },
    loadData:function (callback) {
        var self=this;
        var post_data={
            "method":"get_notification_history"
            ,"is_seen":'0'
            ,"token":token
            ,"page_num":"0"
            ,"limit":"50"
        };
        ajax_post(post_data,function (res) {
            var obj=JSON.parse(res);
            callback(obj);

        });
    },
    drawUI:function (obj) {
        var self=this;
        console.log(obj);
        var html="";
        var notification_list=obj.data;
        if(parseInt(obj.unread_notification_cnt)>0){
            $(".notifications-info .notification").html(obj.unread_notification_cnt);
            $(".notifications-info .notification").removeClass('hidden');
        }else{
            $(".notifications-info .notification").html(0);
            $(".notifications-info .notification").addClass('hidden')
        }
        for(var i in notification_list){
            var notification=notification_list[i];
            html+='<li class="notification-menu-item" notification_data="'+str_encrypt(JSON.stringify(notification))+'">';
            html+='<a href="#">'+self.minify_str(notification.message)+'</a>';
            html+='</li>';
        }
        $(".notifications-info .dropdown-menu").html(html);
    },
    minify_str:function (str) {
        if(str.length>50){
            str=str.substring(0,50);
            str=str+"...";
        }
        return str;
    },
    read_notification:function (notification_id) {
        var self=this;
        var post_data={
            "method":"read_notification"
            ,"token":token
            ,"notification_id":notification_id
        };
        ajax_post(post_data,function (res) {
            self.load();
        });
    },
    bindEvents: function () {
        var self=this;
        $("body").on("click",".notification-menu-item",function () {
            var notification_data=JSON.parse(str_decrypt($(this).attr("notification_data")));
            console.log(notification_data);
            var notification_id=notification_data.notification_id;
            var service_instance=notification_data.data.data.service_instance;
            var notification_type=notification_data.data.type;
            self.read_notification(notification_id);
            
            if(notification_type=='0'){
                var service_instance_data=str_encrypt(JSON.stringify(service_instance));
                chatting_handler.register_popup('chat-box-'+service_instance.service_instance_id, service_instance.user_name+": "+service_instance.service_name,service_instance_data);
            }else{
                var service_instance_id=service_instance.service_instance_id;
                var post_data={
                    "method":"get_service_instance_detail_from_id"
                    ,"token":token
                    ,"service_instance_id":service_instance_id
                };
                ajax_post(post_data,function (res) {
                    var obj=JSON.parse(res);
                    console.log(obj);
                    if(obj.result=="ERR"){
                        demo.showNotification(obj.message,'danger','error');
                        return false;
                    }
                    service_instance=obj.data.service_instance;
                    if(obj.data.user_type=='subscriber'){
                        location.href=base_url+"user/subscribed_services/";
                    }else if(notification_data.data.data.user_type=='manager'){
                        location.href=base_url+"user/managed_services/";
                    }
                });
            }
        });
    }
};
$(document).ready(function () {
    notification_handler.init();
});