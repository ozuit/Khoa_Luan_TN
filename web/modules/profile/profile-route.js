angular
  .module('hr_manager.profile', [])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.members', {
	    cache: true,
	    url: '/members',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/listmember.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.new-member', {
	    cache: true,
	    url: '/new-member',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/addmember.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.update-member', {
	    cache: true,
	    url: '/update-member/:mnv',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/editmember.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.edit-member', {
	    cache: true,
	    url: '/edit-member',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/updatemember.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.view-member', {
	    cache: true,
	    url: '/view-member/:mnv',
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/view_member.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.ktkl', {
	    cache: true,
	    url: '/ktkl', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/ktkl.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.sinhnhat', {
	    cache: true,
	    url: '/sinh-nhat', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/ds_sinhnhat.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.new-ktkl', {
	    cache: true,
	    url: '/new-ktkl/:manv', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/add_ktkl.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.update-ktkl', {
	    cache: true,
	    url: '/update-ktkl/:manv/:id', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/update_ktkl.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.cong-tac', {
	    cache: true,
	    url: '/cong-tac', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/congtac.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.new-hoatdong', {
	    cache: true,
	    url: '/new-hoatdong/:manv', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/add_hoatdong.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.update-hoatdong', {
	    cache: true,
	    url: '/update-hoatdong/:manv/:id', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/update_hoatdong.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.ds-hop-dong', {
	    cache: true,
	    url: '/ds-hop-dong', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/contract.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
	  .state('app.show-hop-dong', {
	    cache: true,
	    url: '/show-hop-dong/:maNV', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/files/show_contract.html',
	        controller: 'ProfileController'
	      }
	    }
	  })
  });