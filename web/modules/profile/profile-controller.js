angular
	.module('hr_manager.profile')
	.controller('ProfileController', ['Data', 'DTOptionsBuilder', '$scope', '$state', '$stateParams', '$http', 'Upload', '$loading', '$fancyModal', ProfileController]);
	
	function ProfileController (Data, DTOptionsBuilder, $scope, $state, $stateParams, $http, Upload, $loading, $fancyModal) {
		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		switch($state.current.name) {
			case 'app.members':
			{
				$loading.start('waiting');
				$scope.data_users = {}; $scope.initGT=$scope.initHN=$scope.initCM=$scope.initCD=$scope.initPB=$scope.initTT=$scope.initTD=$scope.initNN='all';
				Data.get('/user/all').then(function (response) {
					$scope.data_users = response.data;
					$loading.finish('waiting');
				});
				Data.get('/room/all').then(function (response) {
					$scope.phongban = response.data.rooms;
				});
				Data.get('/chucdanh/all').then(function (response) {
					$scope.chucdanh = response.data;
				});
				Data.get('/chuyenmon/all').then(function (response) {
					$scope.chuyenmon = response.data;
				});
				$scope.dtOptions = DTOptionsBuilder.newOptions()
				.withDisplayLength(15)
		    .withOption('rowCallback', rowCallback)
		    .withOption('scrollY', 350)
		    .withOption('scrollX', 3979);
		    break;
		  }
		  case 'app.new-member':
		  {	
		  	$scope.avatar_default = '/assets/images/icon-user.png';
		  	$scope.users = [];
		  	Data.get('/user/select')
		  	.then(function (response) {
		  		$scope.users = response.data;
		  	});
		  	Data.get('/user/count_by_cv', {macv:'GDDH'})
		  	.then(function (response) {
		  		$scope.mnv = 'GDDH' + response.data.number;
		  	});
		  	Data.get('/room/all').then(function (response) {
		  		$scope.phongban = response.data.rooms;
		  	});
		  	Data.get('/chucdanh/all').then(function (response) {
		  		$scope.chucdanh = response.data;
		  		$scope.macd = response.data[0].mcd;
		  	});
		  	Data.get('/chucvu/all').then(function (response) {
		  		$scope.chucvu = response.data;
		  		$scope.macv = response.data[0].ID;
		  	});
		  	Data.get('/chuyenmon/all').then(function (response) {
		  		$scope.chuyenmon = response.data;
		  	});
		  	break;
		  }
		  case 'app.edit-member':
		  {	
		  	Data.get('/user/info').then(function (response) {
		  		$scope.user = response.data;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	break;
		  }
		  case 'app.update-member':
		  case 'app.view-member':
		  {	
		  	$scope.users = [];
		  	Data.get('/user/select')
		  	.then(function (response){
		  		$scope.users = response.data;
		  		$scope.users.unshift({
		  			hoten:"",
		  			id:"",
		  			mnv:""
		  		})
		  	});
		  	Data.get('/user/info', {user_id: $stateParams.mnv})
		  	.then(function (response) {
		  		$scope.user = response.data;
		  		if(response.data.data_hd != null) {
		  			$scope.nguoidd = {'id': response.data.data_hd.nguoidd};
		  			$scope.thoihan = response.data.data_hd.thoihan;
		  			$scope.loaihd = response.data.data_hd.loaihd;
		  		}
		  		$scope.noisinh = response.data.noisinh;
		  		$scope.tongiao = response.data.tongiao;
		  		$scope.dantoc = response.data.dantoc;
		  		$scope.currChucvu = response.data.chucvu;
		  		$scope.currManv = response.data.mnv;
		  		$scope.manv = response.data.mnv;
		  		$scope.macv = response.data.chucvu;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	Data.get('/room/all').then(function (response) {
		  		$scope.phongban = response.data.rooms;
		  	});
		  	Data.get('/chucdanh/all').then(function (response) {
		  		$scope.chucdanh = response.data;
		  	});
		  	Data.get('/chucvu/all').then(function (response) {
		  		$scope.chucvu = response.data;
		  	});
		  	Data.get('/chuyenmon/all').then(function (response) {
		  		$scope.chuyenmon = response.data;
		  	});
		  	break;
		  }
		  case 'app.sinhnhat':
		  {
		  	Data.get('/user/ds_sinhnhat').then (
		  		function (res) {
		  			$scope.listSinhNhat = res.data;
		  		});
		  }
		  case 'app.ktkl':
		  {	
		  	$scope.dtKTKL = DTOptionsBuilder.newOptions()
		  	.withDisplayLength(5)
		  	.withPaginationType('full_numbers')
		  	.withOption('bLengthChange', false)
		  	.withOption('bFilter', false)
		  	.withOption('bInfo', false)
		  	.withOption('scrollCollapse', false);
		  	$scope.initType = 'all'; $scope.disabled = true;
		  	$scope.users = [];
		  	Data.get('/user/select')
		  	.then(function (response){
		  		$scope.users = response.data;
		  	});
		  	break;
		  }
		  case 'app.new-ktkl':
		  case 'app.new-hoatdong':
		  {	
		  	Data.get('/user/info', {user_id: $stateParams.manv}).then(function (response) {
		  		$scope.user = response.data;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	break;
		  }
		  case 'app.update-ktkl':
		  {	
		  	Data.get('/user/info', {user_id: $stateParams.manv}).then(function (response) {
		  		$scope.user = response.data;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	Data.get('/user/getQD', {id: $stateParams.id}).then(function (response) {
		  		$scope.quyetdinh = response.data;
		  		$scope.checkMoney = (response.data.hinhthuc == 'Bằng tiền')? true : false;
		  		$scope.applyMoney = (response.data.hinhthuc == 'Bằng tiền')? 'Bằng tiền' : response.data.hinhthuc;
		  	});
		  	break;
		  }
		  case 'app.update-hoatdong':
		  {	
		  	Data.get('/user/info', {user_id: $stateParams.manv}).then(function (response) {
		  		$scope.user = response.data;
		  		$scope.avatar_default = '/assets/kcfinder/upload/files/avatars/' + response.data.hinhanh;
		  	});
		  	Data.get('/user/getHD', {id: $stateParams.id}).then(function (response) {
		  		$scope.hoatdong = response.data;
		  	});
		  	break;
		  }
		  case 'app.cong-tac':
		  {	
		  	$scope.dtKTKL = DTOptionsBuilder.newOptions()
		  	.withDisplayLength(5)
		  	.withPaginationType('full_numbers')
		  	.withOption('bLengthChange', false)
		  	.withOption('bInfo', false)
		  	.withOption('scrollCollapse', false);
		  	$scope.users = []; $scope.disabled = true;
		  	Data.get('/user/select')
		  	.then(function (response){
		  		$scope.users = response.data;
		  	});
		  	break;
		  }
		  case 'app.ds-hop-dong':
		  {	
		  	$loading.start('waiting');
		  	$scope.stateHD = 'all';
		  	Data.get('/user/hopdong')
		  	.then(function (response){
		  		$scope.data_contracts = response.data;
		  		$loading.finish('waiting');
		  	});
		  	$scope.dtOptionsHD = DTOptionsBuilder.newOptions()
		  	.withDisplayLength(20)
		  	.withPaginationType('full_numbers')
		  	.withOption('scrollY', 350);  	
		  	break;
		  }
		  case 'app.show-hop-dong':
		  {	
		  	$scope.users = [];
		  	Data.get('/user/select')
		  	.then(function (response){
		  		$scope.users = response.data;
		  	});
		  	var manv = $stateParams.maNV;
		  	Data.get('/user/showHD', {manv})
		  	.then(function (response){
		  		$scope.contract = response.data;
		  		$scope.loaihd = response.data.loaihd;
		  		$scope.thoihan = response.data.thoihan;
		  		$scope.quoctichA = response.data.quoctichA;
		  		$scope.chucvuA = response.data.chucvuA;
		  		$scope.nguoidd = {'mnv':response.data.mnvA};
		  	});
		  	break;
		  }
		}

		$scope.importExl = function(file, errFiles){
				$loading.start('waiting');
				$scope.f = file;
				$scope.errFile = errFiles && errFiles[0];
				if (file) {
					file.upload = Upload.upload({
						url: '/api/user/import',
						data: {file: file},
						method: 'POST',
						headers:{
							'Authorization': 'Bearer ' + $scope.token
						}
					});

					file.upload.then(function (response) {
						if (response.data.status) {
							Data.notify_success(response.data.message);
							Data.get('/user/all').then(function (response) {$scope.data_users = response.data;});
						}
					}, function (err) {
						Data.notify_error('Import thất bại!');
					})
					.finally(function () {
						$loading.finish('waiting');
					});
				}
			};	
			$scope.exportExl = function() {
				$loading.start('waiting');
				var unindexed_array = $('#frm_user_filters').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					if (n['value'] != 'all') {
						indexed_array[n['name']] = n['value'];
					}
				});
				$http({
					url: 'api/user/export',
					method: "post",
					headers: {
						'Content-type': 'application/json',
						'Authorization': 'Bearer ' + $scope.token
					},
					data: {'whereClauses': indexed_array},
					responseType: 'arraybuffer'
				}).success(function (data, status, headers, config) {
					var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
					Data.saveAs(blob, 'DS_Nhan_Vien.xlsx');
					$loading.finish('waiting');
				}).error(function (data, status, headers, config) {
					console.log('Failed to download Excel')
				});
			};
			$scope.exportHD = function() {
				$http({
					url: 'api/user/exportHD',
					method: "post",
					headers: {
						'Content-type': 'application/json',
						'Authorization': 'Bearer ' + $scope.token
					},
					data: {'state': $scope.stateHD},
					responseType: 'arraybuffer'
				}).success(function (data, status, headers, config) {
					var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
					Data.saveAs(blob, 'hopdong.xlsx');
					$loading.finish('waiting');
				}).error(function (data, status, headers, config) {
					console.log('Failed to download Excel')
				});
			};
			$scope.addMember = function(avatar, isValid) {
				if (isValid) {
					$loading.start('waiting'); 
					var unindexed_array = $('#frm_add_member').serializeArray();
					var indexed_array = {};
					$.map(unindexed_array, function(n, i){
						indexed_array[n['name']] = n['value'];
					});
					indexed_array.luongcb = indexed_array.luongcb.replace(/,/g ,'');
					if (typeof avatar==='undefined') {
						avatar = '/assets/images/icon-user.png';
					};
					file.upload = Upload.upload({
						url: '/api/user/new',
						data: {image: avatar, info: indexed_array},
						method: 'POST',
						headers:{
							'Authorization': 'Bearer ' + $scope.token
						}
					});

					file.upload.then(function (response) {
						$loading.finish('waiting');
						Data.notify_success(response.data.message);
						$scope.resetAddUser();	
						$('#frm_add_member .input-control').removeClass('success');		      	
					}, function (response) {
						$loading.finish('waiting');
						Data.notify_error(response.data.message);
					});
				}
			};
			$scope.resetAddUser = function() {
				$scope.mnv = '';
				$('#frm_add_member')[0].reset();
				$('#select-noisinh1').val('Hồ Chí Minh');
				$('#select-noicap1').val('Hồ Chí Minh');
				$('#select-dantoc1').val('Kinh');
				$('#select-tongiao1').val('Không');
				$('#select-loaihd1').val('Hợp đồng thử việc');
				$('#select-thoihan1').val('2 tháng');
				$scope.avatar = null;
				Data.get('/user/count_by_cv', {macv:'GDDH'}).then(function (response) {$scope.mnv = 'GDDH' + response.data.number;});		
			};
			$scope.viewMap = function(address) {
				window.open('http://maps.google.com/maps?f=q&hl=en&geocode=&q='+address+'&ie=UTF8&s=AARTsJowriHHOt7le7PzJka7tTgSleZODQ&view=map','googwin')
			};
			$scope.updateMembers = function(manv) {
				$state.go('app.update-member', {mnv: manv});
			};
			function viewMembers(manv) {
				$state.go('app.view-member', {mnv: manv});
			};
			function rowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$('td', nRow).unbind('click');
	      $('td', nRow).bind('click', function() {
	          $scope.$apply(function() {
	            viewMembers(aData[1]);
	          });
	      });
		    return nRow;
			};
			$scope.updateMember = function(avatar) {
				$loading.start('waiting'); 
				var unindexed_array = $('#frm_update_member').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i){
					indexed_array[n['name']] = n['value'];
				});
				if (typeof avatar==='undefined') {
					avatar = '/assets/images/icon-user.png';
				};
				file.upload = Upload.upload({
					url: '/api/user/update',
					data: {image: avatar, info: indexed_array},
					method: 'POST',
					headers:{
						'Authorization': 'Bearer ' + $scope.token
					}
				});

				file.upload.then(function (response) {
					$loading.finish('waiting');
					Data.notify_success(response.data.message);
				}, function (response) {
					$loading.finish('waiting');
					Data.notify_error(response.data.message);
				});
			};
			$scope.editMember = function(avatar) {
				$loading.start('waiting'); 
				var unindexed_array = $('#frm_edit_member').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i){
					indexed_array[n['name']] = n['value'];
				});
				indexed_array.luongcb = indexed_array.luongcb.replace(/,/g ,'');
				if (typeof avatar==='undefined') {
					avatar = '/assets/images/icon-user.png';
				};
				file.upload = Upload.upload({
					url: '/api/user/update',
					data: {image: avatar, info: indexed_array},
					method: 'POST',
					headers:{
						'Authorization': 'Bearer ' + $scope.token
					}
				});

				file.upload.then(function (response) {
					$loading.finish('waiting');
					Data.notify_success(response.data.message);
					window.location.href = '#/update-member/' + $scope.manv;
				}, function (response) {
					$loading.finish('waiting');
					Data.notify_error(response.data.message);
				});
			};
			$scope.printMember = function() {
				Data.printElement('.view-member');
			};
			$scope.printProfile = function() {
				Data.printElement('.profile-member');
			};
			$scope.removeMember = function(manv) {
				$fancyModal.open({ 
					templateUrl: 'web/templates/files/confirm_delMember.html',
					controller: 'ProfileController',
					showCloseButton: true
				});
			};
			$scope.confirmRemoveMember = function() {
				$loading.start('waiting'); 
				Data.get('/user/remove', {manv: $stateParams.mnv})
				.then(function(res) {
					$scope.closeModal();
					$loading.finish('waiting');
					$state.go('app.members');
				});
			};
			$scope.closeModal = function() {
				$fancyModal.close();
			};
			$scope.userFilters = function() {
				var unindexed_array = $('#frm_user_filters').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					if (n['value'] != 'all') {
						indexed_array[n['name']] = n['value'];
					}
				});
				Data.post('/user/filters', {'whereClauses':indexed_array})
				.then(function (response){
					$scope.data_users = response.data;
				});
			};
			$scope.removeFilterUser = function() {
				$scope.initGT=$scope.initHN=$scope.initCM=$scope.initCD=$scope.initPB=$scope.initTT=$scope.initTD=$scope.initNN='all';

				Data.post('/user/filters', {'whereClauses':{}})
				.then(function (response){
					$scope.data_users = response.data;
				});
			};
			$scope.contractFilter = function() {
				Data.post('/user/filterHD', {'state':$scope.stateHD})
				.then(function (response){
					$scope.data_contracts = response.data;
				});
			};
			$scope.changeCV = function() {
				Data.get('/user/count_by_cv', {macv:$scope.macv})
				.then(function (response) {
					$scope.mnv = $scope.macv + response.data.number;
				});
			};
			$scope.changeCD = function() {
				var cd = $scope.macd;
				var ds_chucvu = [];
				Data.get('/chucvu/all').then(function (response) {
		  		var chucvu = response.data;
		  		for (var i = 0; i < chucvu.length; i++) {
		  			var macv = chucvu[i].ID;
		  			var temp = macv.substr(0, 2);
		  			if (cd === temp) {
		  				ds_chucvu.push(chucvu[i]);
		  			}
		  		}
		  		if (ds_chucvu.length > 0) {
		  			$scope.chucvu = ds_chucvu;
		  			$scope.macv = ds_chucvu[0].ID;
		  		} else {
		  			$scope.chucvu = chucvu;
		  			$scope.macv = chucvu[0].ID;
		  		}
		  	});
			};
			$scope.updateChucvu = function() {
				if ($scope.currChucvu == $scope.macv) {
					$scope.manv = $scope.currManv;
				}
				else {
					Data.get('/user/count_by_cv', {macv:$scope.macv})
					.then(function (response) {
						$scope.manv = $scope.macv + response.data.number;
					});
				}
			};
			$scope.printTable = function(id) {
				Data.printElement(id);	
			};
			$scope.selectUserForQD = function(user) {
				$scope.disabled = false;
				Data.get('/user/show_ktkl', {'type': $scope.initType, 'manv': user.mnv})
				.then(function (response) {
					$scope.ds_quyetdinh = response.data;
				});
			};
			$scope.selectUserForCT = function(user) {
				$scope.disabled = false;
				Data.get('/user/show_congtac', {'manv': user.mnv})
				.then(function (response) {
					$scope.ds_congtac = response.data;
				});
			};
			$scope.decideFilters = function() {
				var unindexed_array = $('#frm_decide_filters').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					indexed_array[n['name']] = n['value'];
				});
				Data.get('/user/show_ktkl', indexed_array)
				.then(function (response) {
					$scope.ds_quyetdinh = response.data;
				});
			};
			$scope.exportDecidePDF = function() {
				$loading.start('waiting');
				var unindexed_array = $('#frm_decide_filters').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					indexed_array[n['name']] = n['value'];
				});
				$http({
					url: 'api/user/export_ktkl',
					method: "post",
					headers: {
						'Content-type': 'application/json',
						'Authorization': 'Bearer ' + $scope.token
					},
					data: {'postData': indexed_array},
					responseType: 'arraybuffer'
				}).success(function (data, status, headers, config) {
					var blob = new Blob([data], {type: "application/pdf"});
					Data.saveAs(blob, 'quyetdinh.pdf');
					$loading.finish('waiting');
				});
			};
			$scope.exportCongtacPDF = function() {
				$loading.start('waiting');
				$http({
					url: 'api/user/export_congtac',
					method: "post",
					headers: {
						'Content-type': 'application/json',
						'Authorization': 'Bearer ' + $scope.token
					},
					data: {'manv': $scope.ctrl.user.selected.mnv, 'hoten': $scope.ctrl.user.selected.hoten},
					responseType: 'arraybuffer'
				}).success(function (data, status, headers, config) {
					var blob = new Blob([data], {type: "application/pdf"});
					Data.saveAs(blob, 'congtac.pdf');
					$loading.finish('waiting');
				});
			};
			$scope.addQD = function(manv) {
				$state.go('app.new-ktkl', {'manv': manv});
			};
			$scope.addHD = function(manv) {
				$state.go('app.new-hoatdong', {'manv': manv});
			};
			$scope.addKTKL = function(isValid) {
				if (isValid) {
					var unindexed_array = $('#frm_add_ktkl').serializeArray();
					var indexed_array = {};
					$.map(unindexed_array, function(n, i) {
						indexed_array[n['name']] = n['value'];
					});
					if (indexed_array.tienktkl) {
						indexed_array.tienktkl = (indexed_array.tienktkl).replace(/,/g ,'');
					}
					Data.post('/user/addQD', {'postData':indexed_array})
					.then(function successCallback(response) {
						Data.notify_success(response.data.message);
					}, function errorCallback(response) {
						Data.notify_error(response.data.message);
					});
				}
			};
			$scope.addHoatdong = function(isValid) {
				if (isValid) {
					var unindexed_array = $('#frm_add_hoatdong').serializeArray();
					var indexed_array = {};
					$.map(unindexed_array, function(n, i) {
						indexed_array[n['name']] = n['value'];
					});
					Data.post('/user/addHD', {'postData':indexed_array})
					.then(function successCallback(response) {
						Data.notify_success(response.data.message);
					});
				}
			};
			$scope.updateQD = function(manv, id) {
				$state.go('app.update-ktkl', {'manv': manv, 'id': id});
			};
			$scope.updateHD = function(manv, id) {
				$state.go('app.update-hoatdong', {'manv': manv, 'id': id});
			};
			$scope.updateKTKL = function(isValid) {
				if (isValid) {
					var unindexed_array = $('#frm_update_ktkl').serializeArray();
					var indexed_array = {};
					$.map(unindexed_array, function(n, i) {
						indexed_array[n['name']] = n['value'];
					});
					Data.post('/user/updateQD', {'postData':indexed_array})
					.then(function successCallback(response) {
						Data.notify_success(response.data.message);
					});
				}
			};
			$scope.updateHoatdong = function(isValid) {
				if (isValid) {
					var unindexed_array = $('#frm_update_hoatdong').serializeArray();
					var indexed_array = {};
					$.map(unindexed_array, function(n, i) {
						indexed_array[n['name']] = n['value'];
					});
					Data.post('/user/updateHD', {'postData':indexed_array})
					.then(function successCallback(response) {
						Data.notify_success(response.data.message);
					});
				}
			};
			$scope.showHD = function(maNV) {
				$state.go('app.show-hop-dong', {'maNV': maNV});
			};
			$scope.changeNguoiDD = function(maNguoidd) {
				Data.get('/user/info', {user_id:maNguoidd})
				.then(function (response){
					$scope.chucvuA = response.data.tencv;
					$scope.quoctichA = response.data.quoctich;
				});
			};
			$scope.updateContract = function() {
				$loading.start('waiting');
				var unindexed_array = $('#frm_update_contract').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					indexed_array[n['name']] = n['value'];
				});
				Data.post('/user/updateContract', {'info_contract':indexed_array})
				.then(function (response) {
					$loading.finish('waiting');
					Data.notify_success(response.data.message);
				});
			};
			$scope.export_contract = function(manv) {
				$loading.start('waiting');
				$http({
					url: 'api/user/export_contract',
					method: "post",
					headers: {
						'Content-type': 'application/json',
						'Authorization': 'Bearer ' + $scope.token
					},
					data: {'manv':manv},
					responseType: 'arraybuffer'
				}).success(function (data, status, headers, config) {
					var blob = new Blob([data], {type: "application/pdf"});
					Data.saveAs(blob, 'DS_Hop_Dong.pdf');
					$loading.finish('waiting');
				});
			};
	}