angular
  .module('hr_manager.video', [])
  .config(function config($stateProvider) {
    $stateProvider

	  .state('video-call-room', {
	    cache: true,
	    url: '/video-call',
	    views: {
	      'main': {
	        templateUrl: 'web/templates/videocall/room.html',
	        controller: 'VideoController'
	      }
	    }
	  });
  });