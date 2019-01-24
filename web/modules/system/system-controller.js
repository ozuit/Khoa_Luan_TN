angular
	.module('hr_manager.system')
	.controller('SystemController', ['Data', 'Auth', 'DTOptionsBuilder', '$scope', '$rootScope', '$state', '$stateParams', '$window', '$loading', 'Upload', '$fancyModal', 'urls', '$timeout', '$filter', SystemController]);
	
	function SystemController (Data, Auth, DTOptionsBuilder, $scope, $rootScope, $state, $stateParams, $window, $loading, Upload, $fancyModal, urls, $timeout, $filter) {
		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		$scope.listUser = [];

		switch($state.current.name) {
			case 'app.dashBoard':
			{
				var userLevel = Auth.getTokenClaims().level;
        if (userLevel !== '1' && userLevel !== '2') {
          window.location.href = '/restricted.html';
        }
        Data.get('/user/online').then(
        	function (res) {
        		$scope.userOnline = res.data;
        	});
        Data.get('/user/count_nghiphep').then(
        	function (res) {
        		$scope.countNghiPhep = res.data;
        	});
        Data.get('/user/hethd').then(
        	function (res) {
        		$scope.userHetHD = res.data;
        	});
        Data.get('/user/sinhnhat').then(
        	function (res) {
        		$scope.userSinhNhat = res.data;
        	});
        Data.get('/schedule/future').then(
        	function (res) {
        		$scope.userGhiChu = res.data;
        	});
			}
			case 'app.welcome':
			case 'app.info':
			{		
				$scope.info_company = {};
				Data.get('/info/all')
				.then(function (response){
					$scope.info_company = response.data;
					$scope.logo_image = '/assets/kcfinder/upload/files/images/'+response.data.logo;
				});
				Data.get('/user/info')
				.then(function(res) {
					$rootScope.charm = {
						'email': res.data.email,
						'avatar': '/assets/kcfinder/upload/files/avatars/'+res.data.hinhanh,
					}
					$window.localStorage.charm = angular.toJson($rootScope.charm);
				});	
				break;
			}
			case 'app.auth': 
			case 'app.account': 
			{	
				$loading.start('waiting');
				$scope.data_account = {};
				$scope.dtOptions = DTOptionsBuilder.newOptions()
				.withDisplayLength(10)
				.withOption('bLengthChange', true);
				Data.get('/account/all')
				.then(function (response) {
					$scope.data_account = response.data;
					$loading.finish('waiting');
				});
				break;
			}
		  case 'app.nghi-phep': 
		  {	
		  	Data.get('/user/info').then(function (response) {
		  		$scope.user = response.data;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	break;
		  }
		  case 'app.xem-thong-bao':
		  {	
		  	Data.get('/announce/get', {id: $stateParams.id})
		  	.then(function (response) {
		  		$scope.announcement = response.data;
		  		$('.noidung_TB').html(response.data.noidung);
		  	});
		  	break;
		  }
		  case 'app.ds-thong-bao':
		  {
		  	Data.get('/announce/pagination/'+$stateParams.page)
		  	.then(function (response){
		  		$scope.list_announce = response.data.announcements;
		  		$scope.pagination = response.data.pagination;
		  	});
		  	break;
		  }
		  case 'app.tim-kiem-thong-bao':
		  {
		  	Data.get('/announce/search/'+$stateParams.search+'/'+$stateParams.page)
		  	.then(function (response){
		  		$scope.list_announce = response.data.announcements;
		  		$scope.pagination = response.data.pagination;
		  	});
		  	break;
		  }
		  case 'app.sua-thong-bao':
		  {
		  	Data.get('/announce/get', {id: $stateParams.id})
		  	.then(function (response) {
		  		$scope.editAnnouncement = response.data;
		  	});
		  	break;
		  }
		}

		/*--------------Tài khoản người dùng-------------*/
		$scope.checkActive = function(status) {
			return (status == 1)? true : false;
		};
		$scope.setActive = function(user_id, checked) {
			var data = {
				'user_id': user_id,
				'status': checked
			}
			Data.put('/account/active', data).then(function (res){
				Data.notify_success(res.data.message);
			});
		};
		$scope.showUserOnline = function() {
			var userLevel = Auth.getTokenClaims().level;
      if (userLevel === '1') {
        $scope.toRedirectUrl('account', null, 'system');
      }
		};
		$scope.dsNghiPhep = function() {
			var maCD = Auth.getTokenClaims().chucdanh;
      if (maCD === 'GD' || maCD === 'TP') {
        $scope.toRedirectUrl('ds-nghi-phep', null, 'manage');
      }
		};

		/*---------------Phân quyền người dùng--------------*/
		$scope.setAuth = function(user_id, auth) {
			var data = {
				'user_id': user_id,
				'level': auth
			}
			Data.put('/account/level', data).then(function (res){
				Data.notify_success(res.data.message);
			});
		};

		/*--------------Thông tin công ty-------------------*/
		$scope.updateInfo = function() {
			var unindexed_array = $('#updateInfo_form').serializeArray();
			var indexed_array = {};
			$.map(unindexed_array, function(n, i){
				indexed_array[n['name']] = n['value'];
			});
			Data.put('/info/update', {'data':indexed_array})
			.then(function successCallback(response) {
				$scope.info_company = {};
				Data.get('/info/all')
				.then(function (response){
					$scope.info_company = response.data;
					$scope.logo_image = '/assets/kcfinder/upload/files/images/'+response.data.logo;
				});
				Data.notify_success(response.data.message);
			}, function errorCallback(response) {
				Data.notify_error(response.data.message);
			});
		};
		$scope.uploadFiles = function(file, errFiles) {
			$scope.f = file;
			$scope.errFile = errFiles && errFiles[0];
			if (file) {
				file.upload = Upload.upload({
					url: '/api/info/image',
					data: {file: file},
					method: 'POST',
					headers:{
						'Authorization': 'Bearer ' + $scope.token,
						'Content-Type': file.type
					}
				});

				file.upload.then(function (response) {
					$scope.info_company.logo = response.data.image_name;
				}, function (response) {
					if (response.status > 0)
						console.log(response.status + ': ' + response.data);
				}, function (evt) {
					file.progress = Math.min(100, parseInt(100.0 * 
						evt.loaded / evt.total));
				});
			};  
		}


		/*--------------Thay đổi mật khẩu-------------------*/
		$scope.resetPassword = function() {
			if ($scope.newPass == $scope.rePass) {
				var data = {
					'old_password': sha512($scope.oldPass),
					'new_password': sha512($scope.newPass)
				};

				Data.put('/user/changepass', data)
				.then(function successCallback(response) {
					Data.notify_success(response.data.message);
					$fancyModal.close();
					Auth.logout();
				}, function errorCallback(response) {
					Data.notify_error(response.data.message);
				});
			}
			else {
				Data.notify_error('Mật khẩu mới không trùng khớp!')
			}
		};

		/*---------------Đặt lại mật khẩu------------*/
		$scope.submitResetpass = function () {
			$loading.start('waiting');
			var unindexed_array = $('#resetpass_form').serializeArray();
			var indexed_array = {};
			$.map(unindexed_array, function(n, i){
				indexed_array[n['name']] = n['value'];
			});
			Data.post('/user/fogetpass', indexed_array)
			.then(function successCallback(response) {
				$loading.finish('waiting');
				Data.notify_success(response.data.message);
				$fancyModal.close();
			}, function errorCallback(response) {
				$loading.finish('waiting');
				Data.notify_error(response.data.message);
			});
		}

		/*---------------Backup dữ liệu--------------*/
		var interval1;
		function runPB1(){
			clearInterval(interval1);
			var pb = $("#pb1").data('progress');
			var val = 0;
			interval1 = setInterval(function(){
				val += 1;
				pb.set(val);
				if (val >= 100) {
					val = 0;
					clearInterval(interval1);
				}
			}, 10);
		};
		$scope.submitBackup = function() {
			if(typeof $scope.fileName==='undefined' || $scope.fileName=='') { Data.notify_error('Bạn cần nhập vào tên file backup!') }
			else {
				var data = {
					'fileName': $scope.fileName
				}
				Data.post('/database/backup', data)
				.then(function (response){
					runPB1();
					$timeout(function() {
						$scope.downloadBackup = response.data.fileURL;
						Data.notify_success('File backup đã được tạo thành công');
					}, 1000);
				});
			}
		}


		/*---------------Phục hồi dữ liệu--------------*/
		$scope.submitRestore = function(file) {
		if((typeof $scope.fileBackup==='undefined') || $scope.fileBackup=='') { Data.notify_error('Bạn cần phải chọn một file backup!') }
			else {
				$fancyModal.close();
				$loading.start('waiting');
				$scope.fileBackup = file;
				file.upload = Upload.upload({
					url: '/api/database/restore',
					data: {file: file},
					method: 'POST',
					headers:{
						'Authorization': 'Bearer ' + $scope.token,
						'Content-Type': file.type
					}
				});

				file.upload.then(function (response) {
					$loading.finish('waiting');
					Data.notify_success(response.data.message);
				});
			}
		};
		$scope.dknghiphep = function() {
			var unindexed_array = $('#frm_dk_nghi').serializeArray();
			var indexed_array = {};
			$.map(unindexed_array, function(n, i) {
				indexed_array[n['name']] = n['value'];
			});
			Data.post('/user/dk_nghiphep', {'info':indexed_array})
			.then (function (response) {
				Data.notify_success('Yêu cầu đang chờ phê duyệt, checkmail để cập nhật!');
			});
		};

		/*-------------------Thông báo - nhắc nhở--------------------*/
		$scope.addAnnounce = function(files) {
			if ($scope.tieude == '') {
				Data.notify_error('Bạn chưa nhập tiêu đề cho thông báo!');
			} else {
				if ($scope.mota == '') {
					Data.notify_error('Bạn chưa nhập mô tả cho thông báo!');
				} else {
					$loading.start('waiting');
					var tokenClaims = Auth.getTokenClaims();
					var listUserId = [];
					angular.forEach($scope.listUser, function (item, j) {
						listUserId.push(item.id);
					});
					Upload.upload({
						url: 'api/info/announce',
						data: {files: files, listUser: listUserId, data: {'tieude': $scope.tieude, 'mota':$scope.mota, 'noidung': $scope.noidung, 'nguoitao': tokenClaims.user_id}},
						method: 'POST',
						headers:{
							'Authorization': 'Bearer ' + $scope.token,
							'Content-Type': file.type
						}
					}).then(function (response) {
						Data.notify_success(response.data.message);
						var socket = io.connect(urls.NODE_API);
						var data = {
							'title': $scope.tieude,
							'desc': $scope.mota,
							'id': response.data.id,
							'createdAt': $filter('date')(new Date(new Date().getTime()), 'dd/MM/yyyy - H:mm'),
							'users': listUserId
						}
						connection.setCustomSocketEvent('new-notification');
						connection.socket.emit('new-notification', data);
						$('#frm_announcement')[0].reset();
						$scope.tieude = $scope.mota = $scope.noidung = '';
						$scope.files = {};
						$scope.listUser = [];
						Data.get('/announce/load').then(function (response){$scope.announcements = response.data;});
					}).finally(function() {
						$loading.finish('waiting');
					});
				}
			}
		};
		$scope.removeAnnounce = function() {
			$scope.tieude = '';$scope.mota = '';$scope.noidung = '';$scope.files = null;
		};
		$scope.printAnnounce = function() {
			Data.printElement('.noidung_TB');
		};
		$scope.editAnnounce = function(id) {
			$state.go('app.sua-thong-bao', {'id':id});
		};
		$scope.goBack = function() {
			window.history.back();
		};
		$scope.updateAnnounce = function(files, announcement) {
			if ($scope.tieude == '') {
				Data.notify_error('Bạn chưa nhập tiêu đề cho thông báo!');
			} else {
				if ($scope.mota == '') {
					Data.notify_error('Bạn chưa nhập mô tả cho thông báo!');
				} else {
					$loading.start('waiting');
					var tokenClaims = Auth.getTokenClaims();
					Upload.upload({
						url: 'api/announce/update',
						data: {files: files, data: announcement},
						method: 'POST',
						headers:{
							'Authorization': 'Bearer ' + $scope.token,
							'Content-Type': file.type
						}
					}).then(function (response) {
						Data.notify_success(response.data.message);
						$loading.finish('waiting');
						Data.get('/announce/load').then(function (response){$scope.announcements = response.data;});
					});
				}
			}
		};
		$scope.deleteAnnounce = function() {
			$fancyModal.open({ 
				templateUrl: 'web/templates/system/confirm_delAnnounce.html',
				controller: 'SystemController',
				showCloseButton: true
			});
		};
		$scope.confirmRemoveAnnounce = function() {
			$loading.start('waiting');
			Data.get('/announce/delete', {id: $stateParams.id})
			.then(function (res) {
				$scope.closeModal();
				$loading.finish('waiting');
				$state.go('app.thong-bao');
			});
		};
		$scope.closeModal = function() {
			$fancyModal.close();
		};
		function checkExist(a, idUser) {
	    for (var i = 0; i < a.length; i++) {
	      if (a[i].id === idUser) {
	        return true;
	      }
	    }
	    return false;
		}
		$scope.addUser = function(user) {
			if (!checkExist($scope.listUser, user.id)) {
				$scope.listUser.push(user);
			}
		}
		$scope.removeUser = function(idUser) {
			angular.forEach($scope.listUser, function (obj, i) {
				if (obj.id === idUser) {
					return $scope.listUser.splice(0, 1);
				}
			});
		}
		$scope.downloadFileBackup = function(url) {
			window.open(url, 'Download');
		}
	}