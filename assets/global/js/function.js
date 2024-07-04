function showAlert(msg, type, callback) {
    if (!msg) msg = '';
    if (!type) type = 'info';
    if (typeof toastr !== "undefined") {
        if (typeof msg === 'object') msg = msg[0];
        toastr[type](msg, "Notice");
        setTimeout(function() {
            callback && callback();
        }, 2000);
    } else {
        alert(msg);
    }
}
function showDesktopNotification(title,body,icon) {
    icon=base_url+"assets/global/img/logo-con2.png";

    // Let's check if the browser supports notifications
    if (!("Notification" in window)) {
        //alert("This browser does not support desktop notification");
        showChattingAlert(title, body);
    }
    else if (Notification.permission === "denied") {
        showChattingAlert(title, body);
    }
    // Let's check if the user is okay to get some notification
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        var options = {
            body: body,
            icon: icon,
            dir : "ltr"
        };
        var notification = new Notification(title,options);
    }

    // Otherwise, we need to ask the user for permission
    // Note, Chrome does not implement the permission static property
    // So we have to check for NOT 'denied' instead of 'default'
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            // Whatever the user answers, we make sure we store the information
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            // If the user is okay, let's create a notification
            if (permission === "granted") {
                var options = {
                    body: body,
                    icon: icon,
                    dir : "ltr"
                };
                var notification = new Notification(title,options);
            }
        });
    }

    // At last, if the user already denied any notification, and you
    // want to be respectful there is no need to bother them any more.
}
function showChattingNotification(title,body,icon) {
    if(enable_notification!='1'){
        return false;
    }
    icon=base_url+"assets/global/img/logo-con2.png";

    console.log('notification permission: ');
    console.log(Notification.permission);
    // Let's check if the browser supports notifications
    if (!("Notification" in window)) {
        //alert("This browser does not support desktop notification");
        showChattingAlert(title, body);
    }
    else if (Notification.permission === "denied") {
        showChattingAlert(title, body);
    }
    // Let's check if the user is okay to get some notification
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        var options = {
            body: body,
            icon: icon,
            dir : "ltr"
        };
        var notification = new Notification(title,options);
    }
    // Otherwise, we need to ask the user for permission
    // Note, Chrome does not implement the permission static property
    // So we have to check for NOT 'denied' instead of 'default'
    else if (Notification.permission !== 'denied') {
        console.log('notification is denied');
        Notification.requestPermission(function (permission) {
            // Whatever the user answers, we make sure we store the information
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            // If the user is okay, let's create a notification
            if (permission === "granted") {
                var options = {
                    body: body,
                    icon: icon,
                    dir : "ltr"
                };
                var notification = new Notification(title,options);
            }
        });
    }

    // At last, if the user already denied any notification, and you
    // want to be respectful there is no need to bother them any more.
}
function showChattingAlert(msg, title, type) {
    if (!msg) msg = '';
    if (!type) type = 'info';
    if (typeof toastr !== "undefined") {
        if (typeof msg === 'object') msg = msg[0];
        toastr[type](msg, title);
    } else {
        alert(msg);
    }
}
