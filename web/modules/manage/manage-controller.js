angular
	.module('hr_manager.manage')
	.controller('ManageController', ['Data', 'DTOptionsBuilder', 'DTColumnDefBuilder', '$scope', '$rootScope', '$state', '$stateParams', '$fancyModal', '$loading', ManageController]);
	
	function ManageController (Data, DTOptionsBuilder, DTColumnDefBuilder, $scope, $rootScope, $state, $stateParams, $fancyModal, $loading) {
		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		switch($state.current.name) {
			case 'app.list-rooms':
		  {	
		  	Data.get('/room/all').then(function (response) {
		  		$scope.rooms = response.data.rooms;
		  		$scope.number = response.data.number;
		  	});
		  	break;
		  }
		  case 'app.detail-room':
		  {	
		  	Data.get('/room/detail', {mpb: $stateParams.mpb})
		  	.then(function (response) {
		  		$scope.number = response.data.number;
		  		$scope.members = response.data.members;
		  		$scope.room = response.data.room;
		  	});
		  	break;
		  }
		  case 'app.chuc-danh':
		  {	
		  	$scope.chucdanh = {};
		  	$scope.dtListCD = DTOptionsBuilder.newOptions()
		  	.withOption('bLengthChange', false)
		  	.withOption('paging', false)
		  	.withOption('bFilter', false);
		  	$scope.dtColCD = [
			  	DTColumnDefBuilder.newColumnDef(1).notSortable(),
			  	DTColumnDefBuilder.newColumnDef(2).notSortable(),
		  	];
		  	Data.get('/chucdanh/all')
		  	.then(function (response) {
		  		$scope.chucdanh = response.data;
		  	});
		  	break;
		  }
		  case 'app.chuc-vu':
		  {
		  	$scope.chucvu = {};
		  	$scope.dtListCV = DTOptionsBuilder.newOptions()
		  	.withDisplayLength(10)
		  	.withOption('bLengthChange', false)
		  	.withOption('bFilter', false)
		  	.withScroller()
	      .withOption('scrollY', 350);
		  	$scope.dtColCV = [
			  	DTColumnDefBuilder.newColumnDef(1).notSortable(),
			  	DTColumnDefBuilder.newColumnDef(2).notSortable(),
		  	];
		  	Data.get('/chucvu/all')
		  	.then(function (response) {
		  		$scope.chucvu = response.data;
		  	});
		  	break;
		  }
		  case 'app.chuyen-mon':
		  {	
		  	$scope.chuyenmon = {};
		  	$scope.dtListCM = DTOptionsBuilder.newOptions()
		  	.withDisplayLength(10)
		  	.withOption('bLengthChange', false)
		  	.withOption('paging', false)
		  	.withOption('bFilter', false);
		  	$scope.dtColCM = [
			  	DTColumnDefBuilder.newColumnDef(1).notSortable(),
			  	DTColumnDefBuilder.newColumnDef(2).notSortable(),
		  	];
		  	Data.get('/chuyenmon/all')
		  	.then(function (response) {
		  		$scope.chuyenmon = response.data;
		  	});
		  	break;
		  }
		  case 'app.ds-nghi-phep':
		  {	
		  	Data.get('/user/ds_nghiphep')
		  	.then(function (response) {
		  		$scope.ds_nghi = response.data;
		  	});
		  	break;
		  }
		}

		$scope.openNewRoom = function() {
			$fancyModal.open({ 
				templateUrl: 'web/templates/management/new_room.html',
				controller: 'ManageController',
				showCloseButton: true
			});
		};
		$scope.addRoom = function() {
			if ($('#mpb').val() != '' && $('#tenpb').val() != '' && $('#truongpb').val() != '') {
				var unindexed_array = $('#newroom_form').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i){
					indexed_array[n['name']] = n['value'];
				});
				Data.post('/room/new', {'room':indexed_array})
				.then(function successCallback(response) {
					$fancyModal.close(true);
					$scope.number = parseInt($scope.number) + 1;
					var strMPB = $('#mpb').val();
					$('#num_rooms').html($scope.number);
					$state.reload();
					// $('#list-rooms').append('<div class="room-info" onclick="showRoom(\''+ strMPB.toUpperCase() +'\','+ $event +')"> <span class="mif-cancel" ng-click="removeRoom(\''+ strMPB.toUpperCase() +'\','+ $event +')"></span> <span class="mif-organization mif-4x"></span> <h4>'+ $('#tenpb').val() +'</h4> <h5>Trưởng phòng: '+ $('#truongpb').val() +'</h5> </div>');		
					Data.notify_success(response.data.message);
				}, function errorCallback(response) {
					Data.notify_error(response.data.message);
				});
			}
		};
		$scope.showRoom = function(mpb) {
			$state.go('app.detail-room', {'mpb':mpb});
		};
		$scope.updateRoom = function() {
			var unindexed_array = $('#frmUpdateRoom').serializeArray();
			var indexed_array = {};
			$.map(unindexed_array, function(n, i){
				indexed_array[n['name']] = n['value'];
			});
			indexed_array['current_val'] = $stateParams.mpb;
			Data.put('/room/update', {'room':indexed_array})
			.then(function successCallback(response) {
				Data.notify_success(response.data.message);
				$state.go('app.detail-room', {'mpb':indexed_array['mpb']});
			}, function errorCallback(response) {
				Data.notify_error(response.data.message);
				$('#frmUpdateRoom')[0].reset();
			});
		};
		$scope.removeRoom = function(roomId, $event) {
			$event.stopPropagation();
			$rootScope.holdRoomID = roomId;
			$fancyModal.open({ 
				templateUrl: 'web/templates/management/confirm_delRoom.html',
				controller: 'ManageController',
				showCloseButton: false
			});
		};
		$scope.confirmRemoveRoom = function(roomId) {
			$loading.start('waiting');
			Data.get('/room/remove', {mpb: roomId})
			.then(function (res) {
				$state.reload();
			}, function () {
				Data.notify_error('Phòng ban này hiện đang có nhân viên!');
			})
			.finally(function () {
				$loading.finish('waiting');
		  	$scope.closeModal();
			});
		};
		$scope.closeModal = function() {
			$fancyModal.close();
		};
		$scope.newChucDanh = function() {
			Data.post('/chucdanh/new', {
				'chucdanh' : {'mcd':$scope.mcd, 'tencd':$scope.tencd}
			}).then(function (response) {
				Data.notify_success(response.data.message);
				Data.get('/chucdanh/all').then(function (response) {$scope.chucdanh = response.data;});
				$scope.mcd = '';
				$scope.tencd = '';
			}, function (err) {
				Data.notify_error(err.data.message);
			});
		};
		$scope.checkMCD = function(elm) {
			Data.put('/chucdanh/update', {'chucdanh':{'mcd':$('#'+elm).val(), 'current_val':elm}})
			.then(function successCallback() {
				Data.get('/chucdanh/all')
				.then(function (response) {
					$scope.chucdanh = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			}, function errorCallback(response) {
				Data.notify_error(response.data.message);
				Data.get('/chucdanh/all').then(function (response) {$scope.chucdanh = response.data;});
			});
		};
		$scope.checkTCD = function(elm) {
			var id = elm.split('_')[1];
			Data.put('/chucdanh/update', {'chucdanh':{'mcd':id, 'tencd':$('#'+elm).val(), 'current_val':id}})
			.then(function successCallback() {
				Data.get('/chucdanh/all')
				.then(function (response) {
					$scope.chucdanh = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			});
		};
		$scope.removeChucDanh = function(maCD) {
			Data.get('/chucdanh/remove', {mcd: maCD})
			.then(function (res) {
				Data.notify_success('Đã xóa thành công');
				$state.reload();
			}, function () {
				Data.notify_error('Chức danh này đang được sử dụng!');
			});
		};
		$scope.newChucVu = function() {
			Data.post('/chucvu/new', {
				'chucvu' : {'ID':$scope.mcv, 'tenchucvu':$scope.tencv}
			}).then(function (response) {
				Data.notify_success(response.data.message);
				Data.get('/chucvu/all').then(function (response) {$scope.chucvu = response.data;});
				$scope.mcv = '';
				$scope.tencv = '';
			}, function (err) {
				Data.notify_error(err.data.message);
			});
		};
		$scope.checkMCV = function(elm) {
			Data.put('/chucvu/update', {'chucvu':{'ID':$('#'+elm).val(), 'current_val':elm}})
			.then(function successCallback() {
				Data.get('/chucvu/all')
				.then(function (response) {
					$scope.chucvu = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			}, function errorCallback(response) {
				Data.notify_error(response.data.message);
				Data.get('/chucvu/all').then(function (response) {$scope.chucvu = response.data;});
			});
		};
		$scope.checkTCV = function(elm) {
			var id = elm.split('_')[1];
			Data.put('/chucvu/update', {'chucvu':{'ID':id, 'tenchucvu':$('#'+elm).val(), 'current_val':id}})
			.then(function successCallback() {
				Data.get('/chucvu/all')
				.then(function (response) {
					$scope.chucvu = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			});
		};
		$scope.removeChucVu = function(maCV) {
			Data.get('/chucvu/remove', {mcv: maCV})
			.then(function (res) {
				Data.notify_success('Đã xóa thành công');
				$state.reload();
			}, function () {
				Data.notify_error('Chức vụ này đang được sử dụng!');
			});
		};
		$scope.newChuyenMon = function() {
			Data.post('/chuyenmon/new', {
				'chuyenmon' : {'ID':$scope.mcm, 'tenchuyenmon':$scope.tencm}
			}).then(function (response) {
				Data.notify_success(response.data.message);
				Data.get('/chuyenmon/all').then(function (response) {$scope.chuyenmon = response.data;});
				$scope.mcm = '';
				$scope.tencm = '';
			}, function (err) {
				Data.notify_error(err.data.message);
			});
		};
		$scope.checkMCM = function(elm) {
			Data.put('/chuyenmon/update', {'chuyenmon':{'ID':$('#'+elm).val(), 'current_val':elm}})
			.then(function successCallback() {
				Data.get('/chuyenmon/all')
				.then(function (response) {
					$scope.chuyenmon = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			}, function errorCallback(response) {
				Data.notify_error(response.data.message);
				Data.get('/chuyenmon/all').then(function (response) {$scope.chuyenmon = response.data;});
			});
		};
		$scope.checkTCM = function(elm) {
			var id = elm.split('_')[1];
			Data.put('/chuyenmon/update', {'chuyenmon':{'ID':id, 'tenchuyenmon':$('#'+elm).val(), 'current_val':id}})
			.then(function successCallback() {
				Data.get('/chuyenmon/all')
				.then(function (response) {
					$scope.chuyenmon = response.data;
					Data.notify_success('Cập nhật thành công');
				});
			});
		};
		$scope.removeChuyenMon = function(maCM) {
			Data.get('/chuyenmon/remove', {mcm: maCM})
			.then(function (res) {
				Data.notify_success('Đã xóa thành công');
				$state.reload();
			}, function () {
				Data.notify_error('Chuyên môn này đang được sử dụng!');
			});
		};
		$scope.duyetNghiphep = function(manv, maphep, del) {
			var confirm = window.confirm('Xác nhận quyết định!');
			if(confirm == true) {
				$loading.start('waiting');
				var trangthai = '';
				if(del == false) {
					trangthai = 'Đã duyệt';
				}
				else {
					trangthai = 'delete';
				}
				Data.put('/user/edit_nghiphep', {'trangthai':trangthai,'manv':manv,'maphep':maphep})
				.then(function (response) {
					Data.get('/user/ds_nghiphep')
					.then(function (response) {
						$scope.ds_nghi = response.data;
						$loading.finish('waiting');
						Data.notify_success('Cập nhật thành công');
					});
				});
			}
		};
		$scope.showDialog = function(id) {
			var dialog = $(id).data('dialog');
			dialog.open();
		};
	}