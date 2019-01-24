angular
  .module('hr_manager.salary', [])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.kh-cham-cong', {
	    cache: true,
	    url: '/ki-hieu-cham-cong', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/finance/khchamcong.html',
	        controller: 'SalaryController'
	      }
	    }
	  })
	  .state('app.bang-cham-cong', {
	    cache: true,
	    url: '/bang-cham-cong', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/finance/bangchamcong.html',
	        controller: 'SalaryController'
	      }
	    }
	  })
	  .state('app.tl-luong', {
	    cache: true,
	    url: '/thiet-lap-luong', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/finance/setupSalary.html',
	        controller: 'SalaryController'
	      }
	    }
	  })
	  .state('app.phu-cap', {
	    cache: true,
	    url: '/phu-cap', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/finance/phucap.html',
	        controller: 'SalaryController'
	      }
	    }
	  })
	  .state('app.bang-luong', {
	    cache: true,
	    url: '/bang-luong-thang', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/finance/bangluong.html',
	        controller: 'SalaryController'
	      }
	    }
	  })
  });