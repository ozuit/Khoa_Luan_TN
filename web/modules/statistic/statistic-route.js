angular
  .module('hr_manager.statistic', ['chart.js'])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.tk-chuc-danh', {
      cache: true,
      url: '/tk-chuc-danh', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/chucdanh.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-trinh-do', {
      cache: true,
      url: '/tk-trinh-do', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/trinhdo.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-do-tuoi', {
      cache: true,
      url: '/tk-do-tuoi', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/dotuoi.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-gioi-tinh', {
      cache: true,
      url: '/tk-gioi-tinh', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/gioitinh.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-ngay-nghi', {
      cache: true,
      url: '/tk-ngay-nghi', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/nghiphep.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-ton-giao', {
      cache: true,
      url: '/tk-ton-giao', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/tongiao.html',
          controller: 'StatisticController as vm'
        }
      }
    })
    .state('app.tk-dan-toc', {
      cache: true,
      url: '/tk-dan-toc', 
      views: {
        'content': {
          templateUrl: 'web/templates/statistic/dantoc.html',
          controller: 'StatisticController as vm'
        }
      }
    });
  });