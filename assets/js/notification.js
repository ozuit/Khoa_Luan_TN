function WebNotification(i,t){return new NativeNotification(i,t)}function notifyBrowser(i,t,o){if(!Notification)return void console.log("Desktop notifications not available in your browser...");if("granted"!==Notification.permission)Notification.requestPermission();else{$("#audioNotification").trigger("play");var n=$($.parseHTML(t)).text(),e=new Notification(i,{icon:"https://viettops.net/assets/images/favicon.png",body:n});e.onclick=function(){window.focus();window.open(o,"_self"),e.close()},e.onclose=function(){}}}var NativeNotification=window.Notification,PrefixedNotification=window.webkitNotifications,utils={isFunction:function(i){return"function"==typeof i}};WebNotification.getPermission=function(){if(NativeNotification){if(NativeNotification.permission)return NativeNotification.permission;if(utils.isFunction(NativeNotification.permissionLevel))return NativeNotification.permissionLevel()}if(PrefixedNotification&&utils.isFunction(PrefixedNotification.checkPermission))switch(PrefixedNotification.checkPermission()){case 0:return"granted";case 1:return"default";case 2:return"denied"}return"default"},WebNotification.requestPermission=function(i){function t(){o.permission=o.getPermission(),utils.isFunction(i)&&i(o.permission)}var o=this;if(NativeNotification&&utils.isFunction(NativeNotification.requestPermission))NativeNotification.requestPermission(t);else{if(!PrefixedNotification||!utils.isFunction(PrefixedNotification.requestPermission))throw"Could not call requestPermission";PrefixedNotification.requestPermission(t)}},WebNotification.permission=WebNotification.getPermission(),"function"==typeof define&&define.amd?define(function(){if("Notification"in window)return window.Notification=WebNotification,WebNotification}):"Notification"in window&&(window.Notification=WebNotification);