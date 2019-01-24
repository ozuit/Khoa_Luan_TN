angular.module('hr_manager.mainController', ['datatables', 'datatables.fixedcolumns', 'datatables.scroller', 'ngFileUpload', 'chart.js', 'ckeditor', 'ui.select']).controller('MainController', ['$scope', '$rootScope', '$window', '$cookies', '$timeout', '$interval', '$fancyModal', '$location', 'Auth', 'Data', 'urls', '$translate', MainController]);

function MainController($scope, $rootScope, $window, $cookies, $timeout, $interval, $fancyModal, $location, Auth, Data, urls, $translate) {
    setActive();
    var socket = null;
    var socketIO = null;
    if ($window.localStorage.charm) {
        $rootScope.charm = JSON.parse($window.localStorage.charm)
    };
    $scope.$on('$viewContentLoaded', function() {
        Auth.isAuthenticated(successAuth, function() {
            Auth.logout()
        });
        Data.get('/announce/load').then(function(response) {
            $scope.announcements = response.data
        });
        if (langKey = $cookies.get('lang')) {
            $translate.use(langKey);
        }
    });

    function successAuth(data) {
        if ($window.localStorage.getItem('token')) {
            $window.localStorage.setItem('token', data.refresh_token)
        } else {
            $cookies.put('token', data.refresh_token)
        }
    };
    $scope.init = function() {
        $window.localStorage.setItem('isFullScreen', false);
        $rootScope.hoten_user = $window.localStorage.hoten;
        $scope.tokenClaims = Auth.getTokenClaims();
        $rootScope.userID = $scope.tokenClaims.user_id;
        switch ($scope.tokenClaims.level) {
            case '1':
                {
                    $scope.holidayCtr = true;
                    break
                };
            case '2':
                {
                    $scope.tab_wage = true;
                    $scope.accountCtr = $scope.authCtr = $scope.resetpassCtr = $scope.backupCtr = true;
                    if($scope.tokenClaims.chucdanh !== 'TP') $scope.duyetNghiPhep = true;
                    break
                };
            case '3':
                {
                    $scope.tab_manage = $scope.tab_file = true;
                    $scope.accountCtr = $scope.authCtr = $scope.resetpassCtr = $scope.backupCtr = true;
                    break
                };
            case '4':
                {
                    $scope.tab_manage = $scope.tab_wage = $scope.tab_statistic = true;
                    $scope.accountCtr = true;
                    $scope.workingCtr = $scope.new_memberCtr = $scope.membersCtr = true;
                    break
                };
            case '5':
                {
                    $scope.tab_manage = $scope.tab_wage = $scope.tab_statistic = true;
                    $scope.homeCtr = $scope.accountCtr = $scope.authCtr = $scope.resetpassCtr = $scope.backupCtr = $scope.announceCtr = true;
                    $scope.workingCtr = $scope.new_memberCtr = $scope.membersCtr = true;
                    break
                }
        };
        connection.getSocket().on('invite-user', function(data) {
            $rootScope.user_income = data.hoten;
            $rootScope.avatar = data.avatar;
            $rootScope.roomID = data.roomId;
            for (var i = 0; i < data.inviteUser.length; i++) {
                if (data.inviteUser[i] === $scope.tokenClaims.user_id) {
                    startAudioCall();
                    $fancyModal.open({
                        templateUrl: 'web/templates/videocall/call.html',
                        controller: 'MainController',
                        showCloseButton: true,
                    });
                    break
                }
            }
        })
    };
    $scope.init();
    $scope.users = [];
    Data.get('/user/select').then(function(response) {
        $scope.users = response.data
    });
    Data.get('/info/footer').then(function(response) {
        $scope.infoFooter = response.data
    });

    function transition() {
        $scope.currentDate = new Date().getTime();
        $timeout(function() {
            transition()
        }, 50000)
    };
    transition();
    document.documentElement.style.overflow = 'hidden';
    document.body.scroll = "no";
    var color = $window.localStorage.theme_color;
    Data.change_theme(color);
    $scope.logout = function() {
        Auth.logout()
    };
    $scope.changeBackground = function(color) {
        Data.change_theme(color);
        if ($window.localStorage.theme_color) {
            delete $window.localStorage.theme_color
        };
        $window.localStorage.theme_color = color
    };
    $scope.changeFontSize = function(size) {
        $('body').css('font-size', size);
        $window.localStorage.font_size = size
    };
    $scope.enlarge = true;
    $scope.f_enlarge = function() {
        $window.localStorage.setItem('isFullScreen', true);
        $scope.shrink = true;
        $scope.enlarge = false;
        toggleFullScreen()
    };
    $scope.f_shrink = function() {
        $window.localStorage.setItem('isFullScreen', false);
        $scope.shrink = false;
        $scope.enlarge = true;
        toggleFullScreen()
    };
    $scope.searchData = function(keyword) {
        if (keyword && keyword !== '') {
            $location.path(decodeURIComponent('/ds-thong-bao/tim-kiem/' + keyword + '/'));
            $scope.keyword = ''
        }
    };
    $scope.openChangepass = function() {
        $rootScope.avatar = (JSON.parse($window.localStorage.charm)).avatar;
        $fancyModal.open({
            templateUrl: 'web/templates/system/changepass_page.html',
            controller: 'SystemController',
            showCloseButton: true
        })
    };
    $scope.openResetpass = function() {
        $('#manv').focus();
        $fancyModal.open({
            templateUrl: 'web/templates/system/resetpass_page.html',
            controller: 'SystemController',
            showCloseButton: true,
        })
    };
    $scope.openBackup = function() {
        $fancyModal.open({
            templateUrl: 'web/templates/system/backup_page.html',
            controller: 'SystemController',
            showCloseButton: true
        })
    };
    $scope.openRestore = function() {
        $fancyModal.open({
            templateUrl: 'web/templates/system/restore_page.html',
            controller: 'SystemController',
            showCloseButton: true
        })
    };
    $scope.toRedirectUrl = function(url, origin, tab) {
        if (tab) {
            $(".tabs").removeClass("active");
            $(".tab-panel").css("display", "none");
            $("#"+tab).addClass("active");
            $("#tab_"+tab).css("display", "block");
        }
        if (origin) {
            window.location.href = url
        } else {
            $location.path('/' + url)
        }
    };
    $scope.redirectHome = function() {
        $(".tabs").removeClass("active");
        $(".tab-panel").css("display", "none");
        $("#system").addClass("active");
        $("#tab_system").css("display", "block");
        var userLevel = Auth.getTokenClaims().level;
        if (userLevel !== '1' && userLevel !== '2') {
          $location.path('/welcome');
        } else {
           $location.path('/dashboard'); 
        }
    };
    $scope.checkNotify = function() {
        if (Notification.permission !== "granted" || !$window.localStorage.notification) {
            $scope.acceptNotify = false;
            $scope.declineNotify = true
        } else {
            $scope.acceptNotify = true;
            $scope.declineNotify = false
        }
    };
    $scope.checkNotify();
    $scope.getNotify = function(isValid) {
        if (isValid) {
            if (Notification.permission === 'default') {
                WebNotification.requestPermission(function(permission) {
                    if (permission === 'granted') {
                        $scope.acceptNotify = true;
                        $scope.declineNotify = false;
                        $window.localStorage.notification = true;
                        window.location.reload()
                    }
                })
            };
            if (Notification.permission === 'denied') {
                $.Notify({
                    content: 'Bạn cần phải cấp quyền truy cập trên thiết bị này!',
                    keepOpen: true,
                    type: 'alert',
                })
            };
            if (Notification.permission === 'granted') {
                $scope.acceptNotify = true;
                $scope.declineNotify = false;
                $window.localStorage.notification = true;
                window.location.reload()
            }
        } else {
            $scope.acceptNotify = false;
            $scope.declineNotify = true;
            delete $window.localStorage.notification;
            window.location.reload()
        }
    };
    $scope.setNotify = function() {
        if (Notification.permission === "granted" && $window.localStorage.notification) {
            connection.getSocket().on('new-notification', function(data) {
                $('.announce').prepend('<li><span class="mif-chevron-right"></span><a href="#/xem-thong-bao/' + data.id + '"> ' + data.title + '</a> <span class="date-created">(' + data.createdAt + ')</span><span class="is-read"> - <strong>mới</strong></span></li>');
                if (data.users.length > 0) {
                    for (var i = 0; i < data.users.length; i++) {
                        if (data.users[i] === $scope.tokenClaims.user_id) {
                            notifyBrowser(data.title, data.desc, '#/xem-thong-bao/' + data.id);
                            break
                        }
                    }
                } else {
                    notifyBrowser(data.title, data.desc, '#/xem-thong-bao/' + data.id)
                }
            })
        }
    };
    $scope.setNotify();
    $scope.hangDown = function() {
        stopAudioCall();
        $fancyModal.close();
        var data = {
            id: $scope.tokenClaims.user_id,
            username: $window.localStorage.hoten,
            avatar: (JSON.parse($window.localStorage.charm)).avatar
        };
        connection.setCustomSocketEvent('decline-call');
        connection.socket.emit('decline-call', data)
    };
    $scope.hangUp = function() {
        openVideoCall($rootScope.roomID);
        stopAudioCall();
        $fancyModal.close();
        var data = {
            id: $scope.tokenClaims.user_id,
            username: $window.localStorage.hoten,
            avatar: (JSON.parse($window.localStorage.charm)).avatar
        };
        connection.setCustomSocketEvent('accept-call');
        connection.socket.emit('accept-call', data)
    };
    $scope.changeLanguage = function (langKey) {
        $cookies.put('lang', langKey);
        $translate.use(langKey);
        //Update to server
    };
};