angular.module('hr_manager.video').controller('VideoController', ['Data', 'Auth', 'urls', '$scope', '$rootScope', '$state', '$stateParams', '$fancyModal', '$window', '$cookies', '$translate', VideoController]);

function VideoController(Data, Auth, urls, $scope, $rootScope, $state, $stateParams, $fancyModal, $window, $cookies, $translate) {
    $scope.listUser = [];
    $scope.onlineUsers = [];

    function checkExist(a, idUser) {
        for (var i = 0; i < a.length; i++) {
            if (a[i].id === idUser) {
                return true
            }
        }
        return false
    }
    $scope.init = function() {
        if (langKey = $cookies.get('lang')) {
            $translate.use(langKey);
        }
        if (!$window.localStorage.getItem('videocall')) {
            $window.localStorage.setItem('videocall', true);
            Auth.isAuthenticated(function() {
                initVideoCall();
                connection.extra = {
                    fullname: $window.localStorage.hoten,
                    avatar: (JSON.parse($window.localStorage.charm)).avatar
                };
                connection.updateExtraData()
            }, function() {
                Auth.logout()
            })
        }
        var mouse_position;
        var animating = false;
        $(document).mousemove(function(e) {
            if (animating) {
                return
            }
            mouse_position = e.clientX;
            if (mouse_position <= 10) {
                animating = true;
                $('#cms_bar').animate({
                    left: 0,
                    opacity: 1
                }, 200, function() {
                    animating = false
                })
            } else if (mouse_position > 60) {
                animating = true;
                $('#cms_bar').animate({
                    left: -60,
                    opacity: 0
                }, 500, function() {
                    animating = false
                })
            }
        });
        var tokenClaims = Auth.getTokenClaims();
        if (!$scope.users) {
            $scope.users = [];
            Data.get('/user/select').then(function(response) {
                var arrayUser = [];
                angular.forEach(response.data, function(item, index) {
                    if (item.accountID !== (tokenClaims.user_id).toString()) {
                        arrayUser.push(item)
                    }
                });
                $scope.users = arrayUser
            })
        }
    };
    $scope.init();
    $scope.openInvitePopup = function() {
        $rootScope.curentURL = 'https://' + window.location.host + '/#/video-call?roomid=' + localStorage.getItem('roomid');
        $fancyModal.open({
            templateUrl: 'web/templates/videocall/invite.html',
            controller: 'VideoController',
            showCloseButton: true,
        })
    };
    $scope.copyURL = function() {
        new Clipboard('#copyURL');
        var alertSuccessCopy = ($cookies.get('lang') == 'en_US') ? "Link copied to clipboard!" : "Đã sao chép liên kết vào khay nhớ tạm!";
        $.Notify({
            content: alertSuccessCopy,
            timeout: 2000
        })
    };
    $scope.addUser = function(user) {
        if (!checkExist($scope.listUser, user.id)) {
            $scope.listUser.push(user)
        }
    };
    $scope.removeUser = function(idUser) {
        angular.forEach($scope.listUser, function(obj, i) {
            if (obj.id === idUser) {
                return $scope.listUser.splice(0, 1)
            }
        })
    };
    $scope.inviteUser = function() {
        $fancyModal.close();
        var inviteUser = [];
        angular.forEach($scope.listUser, function(obj, i) {
            inviteUser.push(obj.id)
        });
        startAudioCall();
        var data = {
            inviteUser: inviteUser,
            roomId: $window.localStorage.getItem('roomid'),
            hoten: $window.localStorage.hoten,
            avatar: (JSON.parse($window.localStorage.charm)).avatar
        };
        connection.setCustomSocketEvent('invite-user');
        connection.socket.emit('invite-user', data);
        connection.getSocket().on('accept-call', function(data) {
            stopAudioCall()
        });
        connection.getSocket().on('decline-call', function(data) {
            stopAudioCall();
            var alertJoinCall = ($cookies.get('lang') == 'en_US') ? " unable to join the call!" : " không thể tham gia cuộc gọi!";
            $.Notify({
                content: '<strong>' + data.username + '</strong> ' + alertJoinCall,
                timeout: 10000,
                icon: "<img src='" + data.avatar + "'>"
            })
        })
    };
    $scope.closeVideoCall = function() {
        self.close()
    };

    $scope.shareFile = function() {
        var fileSelector = new FileSelector();
        fileSelector.selectSingleFile(function(file) {
            onFileSelected(file);
        });
    };
}