angular.module('hr_manager', ['ui.router', 'ngResource', 'pascalprecht.translate', 'ngCookies', 'ngSanitize', 'darthwade.dwLoading', 'vesparny.fancyModal', 'hr_manager.directive', 'hr_manager.entities', 'hr_manager.utilities', 'hr_manager.authController', 'hr_manager.mainController', 'hr_manager.statistic', 'hr_manager.manage', 'hr_manager.profile', 'hr_manager.salary', 'hr_manager.help', 'hr_manager.system', 'hr_manager.video']).constant('urls', {
    BASE_API: 'https://192.168.159.1/api',
    NODE_API: 'https://192.168.159.1:9001/'
}).config(function($httpProvider, $stateProvider, $urlRouterProvider, $fancyModalProvider, $translateProvider) {
    $urlRouterProvider.otherwise("/");
    $stateProvider.state('login', {
        cache: true,
        url: '/login',
        views: {
            'main': {
                templateUrl: 'web/templates/index.html',
                controller: 'AuthController'
            }
        }
    }).state("init", {
        cache: false,
        url: '/',
        views: {
            'main': {
                templateUrl: 'web/templates/layout/blank.html',
                controller: 'AppController',
            }
        }
    }).state('app', {
        cache: false,
        url: '',
        abstract: true,
        views: {
            'main': {
                templateUrl: 'web/templates/layout/main.html',
                controller: 'MainController'
            }
        }
    });
    $translateProvider.useStaticFilesLoader({
            prefix: 'assets/lang/locale-',
            suffix: '.json'
        });
    $translateProvider.preferredLanguage('vn_VN');
    $translateProvider.useSanitizeValueStrategy('escapeParameters');
    $fancyModalProvider.setDefaults({
        closeOnEscape: false,
        closeOnOverlayClick: false
    });
    $httpProvider.interceptors.push(function($q, $location, $window, $cookies) {
        return {
            'request': function(config) {
                config.headers = config.headers || {};
                if ($window.localStorage.token) {
                    config.headers.Authorization = 'Bearer ' + $window.localStorage.token;
                }
                if ($cookies.get('token')) {
                    config.headers.Authorization = 'Bearer ' + $cookies.get('token');
                }
                return config;
            },
            'responseError': function(response) {
                if (response.status === 401 || response.status === 403) {
                    $location.path('/login');
                }
                return $q.reject(response);
            }
        };
    });
}).filter('unsafe', function($sce) {
    return function(val) {
        return $sce.trustAsHtml(val);
    };
}).filter('propsFilter', function() {
    return function(items, props) {
        var out = [];
        if (angular.isArray(items)) {
            var keys = Object.keys(props);
            items.forEach(function(item) {
                var itemMatches = false;
                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }
                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            out = items;
        }
        return out;
    };
}).controller('AppController', ['$scope', '$state', '$timeout', 'Auth', '$translate', AppController]);

function AppController($scope, $state, $timeout, Auth, $translate) {
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
}