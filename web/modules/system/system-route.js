angular
  .module('hr_manager.system', [])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.welcome', {
	    cache: true,
	    url: '/welcome',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/welcome.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.dashBoard', {
	    cache: true,
	    url: '/dashboard',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/dashboard.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.info', {
	    cache: true,
	    url: '/info',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/info_page.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.account', {
	    cache: true,
	    url: '/account',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/account_page.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.auth', {
	    cache: true,
	    url: '/auth',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/auth_page.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.nghi-phep', {
	    cache: true,
	    url: '/nghi-phep', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/xin_nghi_phep.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.thong-bao', {
	    cache: true,
	    url: '/thong-bao', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/announcement.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.sua-thong-bao', {
	    cache: true,
	    url: '/thong-bao/chinh-sua/:id', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/edit_announcement.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.xem-thong-bao', {
	    cache: true,
	    url: '/xem-thong-bao/:id', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/view_announcement.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.ds-thong-bao', {
	    cache: true,
	    url: '/ds-thong-bao/:page', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/list_announcement.html',
	        controller: 'SystemController'
	      }
	    }
	  })
	  .state('app.tim-kiem-thong-bao', {
	    cache: true,
	    url: '/ds-thong-bao/tim-kiem/:search/:page',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/system/list_announcement.html',
	        controller: 'SystemController'
	      }
	    }
	  })
  });