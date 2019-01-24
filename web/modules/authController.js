angular.module('hr_manager.authController', ['vcRecaptcha']).controller('AuthController', ['$rootScope', '$scope', '$state', '$cookies', '$window', '$fancyModal', '$timeout', 'Auth', 'anchorSmoothScroll', '$translate', AuthController]);

function AuthController($rootScope, $scope, $state, $cookies, $window, $fancyModal, $timeout, Auth, anchorSmoothScroll, $translate) {
    Auth.isAuthenticated(function() {
        var userLevel = Auth.getTokenClaims().level;
        if (userLevel === '1' || userLevel === '2') {
            $state.go("app.dashBoard");
        } else {
            $state.go("app.welcome");
        }
    }, function() {
        $state.go('login');
    });
    $scope.uppercase = function() {
        var curr = $scope.username;
        $scope.username = curr.toUpperCase();
    };
    $scope.$on('$viewContentLoaded', function() {
        if (langKey = $cookies.get('lang')) {
            $translate.use(langKey);
        }
    });
    
    function successAuth(res) {
        var email = res.email;
        var password = sha512($scope.password);
        firebase.auth().signInWithEmailAndPassword(email, password).then(function(success) {
            console.log('Login Firechat Successful');
        }, function(error) {
            firebase.auth().createUserWithEmailAndPassword(email, password).then(function(success) {
                firebase.auth().signInWithEmailAndPassword(email, password);
            }, function(error) {
                console.log('ERROR Create Account Firechat: ', error);
            });
        });
        if ($scope.rememberMe) {
            $window.localStorage.token = res.token;
        } else {
            $cookies.put('token', res.token);
        }
        $window.localStorage.hoten = res.name;
        var userLevel = Auth.getTokenClaims().level;
        if (userLevel === '1' || userLevel === '2') {
            $state.go("app.dashBoard");
        } else {
            $state.go("app.welcome");
        }
    }
    $scope.submitLogin = function() {
        var checkPass = $scope.password;
        if ($scope.username && $scope.password && checkPass.length >= 6) {
            var unindexed_array = $('#login_form').serializeArray();
            var indexed_array = {};
            $.map(unindexed_array, function(n, i) {
                indexed_array[n['name']] = n['value'];
            });
            indexed_array['password'] = sha512($scope.password);
            Auth.login(indexed_array, successAuth, function(res) {
                grecaptcha.reset();
                $.Notify({
                    caption: 'Lỗi đăng nhập',
                    content: res.message,
                    timeout: 5000,
                    type: 'alert',
                    icon: "<span class='mif-warning'></span>",
                });
            });
        };
    };
    $scope.gotoLogin = function() {
        anchorSmoothScroll.scrollTo('login');
        $('#login_user').focus();
    }
};