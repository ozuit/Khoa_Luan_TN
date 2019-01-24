angular
  .module('hr_manager.manage', [])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.list-rooms', {
	    cache: true,
	    url: '/list-rooms',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/list_rooms.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.detail-room', {
	    cache: true,
	    url: '/detail-room/:mpb',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/detail_room.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.chuc-danh', {
	    cache: true,
	    url: '/chuc-danh',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/list_chucdanh.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.chuc-vu', {
	    cache: true,
	    url: '/chuc-vu', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/list_chucvu.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.chuyen-mon', {
	    cache: true,
	    url: '/chuyen-mon', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/list_chuyenmon.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.ds-nghi-phep', {
	    cache: true,
	    url: '/ds-nghi-phep', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/management/list_nghiphep.html',
	        controller: 'ManageController'
	      }
	    }
	  })
	  .state('app.upload-image', {
	  	cache: true,
	  	url: '/du-lieu/hinh-anh',
	  	views: {
	  		'content': {
	  			templateUrl: 'web/templates/management/upload-image.html',
	  			controller: 'ManageController'
	  		}
	  	}
	  })
	  .state('app.upload-other', {
	  	cache: true,
	  	url: '/du-lieu/khac',
	  	views: {
	  		'content': {
	  			templateUrl: 'web/templates/management/upload-other.html',
	  			controller: 'ManageController'
	  		}
	  	}
	  })

  });