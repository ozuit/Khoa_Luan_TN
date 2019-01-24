angular
	.module('hr_manager.salary')
	.controller('SalaryController', ['Data', 'DTOptionsBuilder', 'Entities', 'Utilities', '$scope', '$state', '$http', '$fancyModal', '$loading', SalaryController]);
	
	function SalaryController (Data, DTOptionsBuilder, Entities, Utilities, $scope, $state, $http, $fancyModal, $loading) {
		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		switch($state.current.name) {
			case 'app.kh-cham-cong':
		  {
		  	Data.get('/chamcong/thietlap/get')
		  	.then(function (res) {
		  		$scope.setupCC = res.data;
		  	});
		  	Data.get('/chamcong/kyhieu/all')
		  	.then(function (res) {
		  		$scope.khOrigin = [];
		  		angular.forEach(res.data, function (obj, i){
		  			if (obj.kyhieu !== 'OT+(số giờ)' && obj.kyhieu !== 'DT-(số phút)')
		  				$scope.khOrigin.push(obj);
		  		});
		  		$scope.khCustom = Utilities.formatKHCC(res.data);
		  	});
		    break;
		  }
		  case 'app.bang-cham-cong':
		  {
		  	$loading.start('waiting');
		  	$scope.dtOptionCC = DTOptionsBuilder.newOptions()
		  		.withOption('scrollX', 2120)
		  		.withOption('rowCallback', selectRowPayroll); 
		  	Data.get('/chamcong/kyhieu/all', {filter:true})
		  	.then(function (res) {
		  		$scope.arrSymbols = angular.copy(res.data);
		  		$scope.khChamcong = Utilities.formatKHCC(res.data);
		  	});
		  	Data.get('/chamcong/bangchamcong')
		  	.then(function (res) {
		  		$scope.khmacdinh = res.data.khmacdinh;
		  		var response = res.data;
		  		if (!response.error) {
		  			$scope.dataChamCong = response.data;
		  			$scope.dateNumber = response.day_numbers;
		  			$scope.arrYearPayroll = response.arrYear;
		  			$scope.yearPayroll = response.currYear;
		  			$scope.arrMonthPayroll = response.arrMonth;
		  			$scope.monthPayroll = response.currMonth;
		  			$scope.isFinishPayroll = (response.finish === '0') ? false : true;
		  			$scope.dayOfMonth = [];
			  		for (var i = 0; i < response.day_numbers; i++) {
			  			$scope.dayOfMonth.push(i+1);
			  		};
			  		$loading.finish('waiting');
			  	}
		  	});
		  	$scope.chamcong = Entities.fastPayroll();
		  	break;
		  }
		  case 'app.tl-luong':
		  {
		  	Data.get('/bangluong/dinhmuc').then (
		  		function (res) {
		  			$scope.dinhmuc = res.data;
		  			$scope.dinhmuc.tinhditre = (res.data.tinhditre == '1')? true : false;
		  		});
		  	break;
		  }
		  case 'app.phu-cap':
		  {
		  	$scope.isCreating = true;
		  	$scope.isEditing = false;
		  	$scope.phucap = {
		  		mota: '',
		  		sotien: '',
		  		ghichu: ''
		  	}
		  	Data.get('/phucap/danhsach')
		  	.then(function (res) {
		  		$scope.listPhucap = res.data.phucap;
		  		$scope.listChucdanh = res.data.chucdanh;
		  	});
		  }
		  case 'app.bang-luong':
		  {
		  	$loading.start('waiting');
		  	$scope.paycheckUsers = [];
		  	Data.get('/bangluong/laydulieu').then(
		  		function (res) {
		  			var response = res.data;
		  			if (!response.error) {
			  			$scope.isFinishPaycheck = (response.finish == '1') ? true : false;
			  			$scope.arrYearPaycheck = response.arrYear;
			  			$scope.arrMonthPaycheck = response.arrMonth;
			  			$scope.yearPaycheck = response.currYear;
			  			$scope.monthPaycheck = response.currMonth;
			  			$scope.paycheckUsers = response.dataPaycheck;
			  			$scope.selectedUser = response.dataPaycheck[0];
		  			}
		  		}).finally(function (){
						$loading.finish('waiting');
					});
		  	$scope.dtPaycheck = DTOptionsBuilder.newOptions()
				.withDisplayLength(8)
		    .withOption('rowCallback', selectRowPaycheck)
		    .withOption('scrollY', ($scope.paycheckUsers.length === 0)? 'auto' : 350)
		    .withOption('scrollX', 2200);
		  	break;
		  }
		}

		function selectRowPayroll(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
			$('td', nRow).unbind('click');
	      $('td', nRow).bind('click', function() {
	          $scope.$apply(function() {
	            $('.data-payroll').removeClass('active');
	            angular.element('#rowPayroll_'+nRow._DT_RowIndex).addClass('active');
	            angular.element('.rowPayroll_'+nRow._DT_RowIndex+'_').addClass('active');
	          });
	      });
	    return nRow;
		};
		$scope.themKHCC = function() {
			$scope.chamcong = {
				title: 'Thêm mới ký hiệu',
				data: Entities.saveSymbol()
			};
			$fancyModal.open({ 
				templateUrl: 'web/templates/finance/khcc_template.html',
				controller: 'SystemController',
				showCloseButton: true,
				scope: $scope
			});
		};
		$scope.editKHCC = function(item) {
			$scope.chamcong = {
				title: 'Chỉnh sửa ký hiệu',
				data: Entities.saveSymbol(item)
			}
			$fancyModal.open({ 
				templateUrl: 'web/templates/finance/khcc_template.html',
				controller: 'SystemController',
				showCloseButton: true,
				scope: $scope
			});
		};
		$scope.deleteKHCC = function(idKyHieu) {
			$loading.start('waiting');
			Data.get('/chamcong/kyhieu/delete', {id: idKyHieu})
			.then(function (res) {
				$scope.khOrigin = angular.copy(res.data);
	  		var temp = [], i = 0;
	  		for (i; i < res.data.length/4; i++) {
	  			temp.push(res.data.splice(0, 4));
	  		};
	  		temp.push(res.data.splice(0, res.data.length));
	  		$scope.khCustom = temp;
	  		$loading.finish('waiting');
	  		$fancyModal.close();
			});
		};
		$scope.closeModal = function() {
			$fancyModal.close();
		};
		$scope.saveKHCC = function(data) {
			$loading.start('waiting');
			Data.get('/chamcong/kyhieu/save', {params: data})
			.then(function (res) {
	  		$loading.finish('waiting');
	  		$fancyModal.close();
	  		$state.reload();
			});
		};
		$scope.updateSetupCC = function(dataCC) {
			dataCC.lamt7 = (dataCC.lamt7 == 'true')? 1 : 0;
			Data.post('/chamcong/thietlap/post', {dataCC: dataCC})
			.then(function (res) {
				$scope.setupCC = res.data;
				Data.notify_success('Cập nhật thiết lập thành công');
			});
		};
		$scope.parJson = function(json) {
			return JSON.parse(json);
		};
		$scope.newPayroll = function() {
			$loading.start('waiting');
			Data.get('/chamcong/bangchamcong', {new_create:true})
			.then(function (res) {
				var response = res.data;
	  		if (!response.error) {
	  			$scope.isFinishPayroll = response.finish;
	  			$scope.dataChamCong = response.data;
	  			$scope.dateNumber = response.day_numbers;
	  			$scope.arrYearPayroll = response.arrYear;
	  			$scope.yearPayroll = response.currYear;
	  			$scope.arrMonthPayroll = response.arrMonth;
	  			$scope.monthPayroll = response.currMonth;
	  			$scope.dayOfMonth = [];
		  		for (var i = 0; i < response.day_numbers; i++) {
		  			$scope.dayOfMonth.push(i+1);
		  		};
		  	}
			})
			.finally(function (){
				$loading.finish('waiting');
			});
		};
		$scope.cfmClosePayroll = function() {
			$fancyModal.open({ 
				templateUrl: 'web/templates/finance/confirm_popup.html',
				controller: 'SystemController',
				showCloseButton: true,
				scope: $scope
			});
		};
		$scope.fastPayroll = function() {
			$fancyModal.open({ 
				templateUrl: 'web/templates/finance/cc_nhanh.html',
				controller: 'SystemController',
				showCloseButton: true,
				scope: $scope
			});
		};
		$scope.finishPayroll = function() {
			$loading.start('waiting');
			Data.get('/chamcong/hoanthanh')
			.then(function (res) {
				var response = res.data;
	  		if (!response.error) {
	  			$fancyModal.close();
	  			$scope.isFinishPayroll = true;
	  			$state.reload();
		  	}
			})
			.finally(function (){
				$loading.finish('waiting');
			});
		};
		$scope.updateTablePayroll = function(dataPayroll) {
			$('.tangca_'+dataPayroll.manv).text(dataPayroll.tangca);
			$('.denmuon_'+dataPayroll.manv).text(dataPayroll.denmuon);
			$('.cophep_'+dataPayroll.manv).text(dataPayroll.nghicophep);
			$('.kophep_'+dataPayroll.manv).text(dataPayroll.nghikophep);
			$('.tongcong_'+dataPayroll.manv).text(dataPayroll.tongngaycong);
		};
		$scope.updatePayroll = function($event, manv, dayNumber) {
			var data = {manv: manv,dayNumber: dayNumber,kyhieu: event.currentTarget.value}
			Data.post('/chamcong/capnhat', data)
			.then(function (res) {
				var response = res.data;
	  		if (!response.error) {
					$scope.updateTablePayroll(response.data);
					Data.notify_success('Cập nhật thành công!');
				} else {
					if (response.message !== '') {
						Data.notify_error(response.message);
					}
				}
			});
		};
		$scope.fastUpdatePayroll = function(dataChamCong) {
			$loading.start('waiting');
			if (dataChamCong.allDate) {
				dataChamCong.startDate = 1;
				dataChamCong.endDate = $scope.dateNumber;
			}
			if (dataChamCong.allLine) {
				dataChamCong.startLine = 1;
				dataChamCong.endLine = $scope.dataChamCong.length;
			}
			var requestData = Entities.fastPayroll(dataChamCong);
			requestData.manv = [];
			var userIndex = requestData.startLine - 1;
			for (var i = 0; i < requestData.endLine - requestData.startLine + 1; i++) {
				requestData.manv.push($scope.dataChamCong[userIndex++].manv);
			};
			var row = requestData.startLine - 1, col = requestData.startDate - 1;
			for (var i = row; i < requestData.endLine; i++) {
				for (var j = col; j < requestData.endDate; j++) {
					$('#ttcc_'+i+'_'+j).val(requestData.kyhieu);
				};
			};
			delete requestData.startLine;
			delete requestData.endLine;
			Data.post('/chamcong/chamcongnhanh', requestData)
			.then(function (res) {
				var response = res.data;
				angular.forEach(response, function (obj, i) {
					if (!obj.error) {
						$scope.updateTablePayroll(obj.data);
					}
				});
				$loading.finish('waiting');
	  		$fancyModal.close();
				Data.notify_success('Cập nhật thành công!');
			});
		};
		$scope.getBackupPayroll = function(month, year) {
			$loading.start('waiting');
			Data.get('/chamcong/bangchamcong', {'month': month, 'year': year})
	  	.then(function (res) {
	  		$scope.khmacdinh = res.data.khmacdinh;
	  		var response = res.data;
	  		if (!response.error) {
	  			$scope.dataChamCong = response.data;
	  			$scope.dateNumber = response.day_numbers;
	  			$scope.arrYearPayroll = response.arrYear;
	  			$scope.yearPayroll = response.currYear;
	  			$scope.arrMonthPayroll = response.arrMonth;
	  			$scope.monthPayroll = response.currMonth;
	  			$scope.isFinishPayroll = (response.finish == '0') ? false : true;
	  			$scope.dayOfMonth = [];
		  		for (var i = 0; i < response.day_numbers; i++) {
		  			$scope.dayOfMonth.push(i+1);
		  		};
		  		$loading.finish('waiting');
		  	}
	  	});
		};
		$scope.exportPayroll = function(month, year) {
			$loading.start('waiting');
			$http({
				url: '/api/chamcong/xuatexcel',
				method: "post",
				headers: {
					'Content-type': 'application/json',
					'Authorization': 'Bearer ' + $scope.token
				},
				data: {'month': month, 'year': year},
				responseType: 'arraybuffer'
			}).success(function (data, status, headers, config) {
				var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
				Data.saveAs(blob, 'Bang_cham_cong.xlsx');
			}).error(function (data, status, headers, config) {
				console.log('Failed to download Excel')
			}).finally(function (){
				$loading.finish('waiting');
			});
		};
		
	  $scope.resetSetupSalary = function() {
	  	$scope.dinhmuc = Entities.defaultSetupSalary();
	  	Data.post('/bangluong/update_dinhmuc', {setupPaycheck: Entities.defaultSetupSalary()}).then (
	  		function (res) {
	  			console.log(res.data.message);
	  		});
	  };
	  $scope.updateSetupSalary = function(dinhmuc) {
	  	$loading.start('waiting');
	  	Data.post('/bangluong/update_dinhmuc', {setupPaycheck: dinhmuc}).then (
	  		function (res) {
	  			Data.notify_success(res.data.message);
	  		}).finally(function (){
					$loading.finish('waiting');
				});
	  };
	  $scope.getBackupPaycheck = function(month, year) {
	  	if (month != 0 && year != 0) {
		  	$loading.start('waiting');
		  	Data.get('/bangluong/laydulieu', {month: month, year: year}).then(
		  		function (res) {
		  			var response = res.data;
		  			if (!response.error) {
			  			$scope.isFinishPaycheck = (response.finish == '1') ? true : false;
			  			$scope.arrYearPaycheck = response.arrYear;
			  			$scope.arrMonthPaycheck = response.arrMonth;
			  			$scope.yearPaycheck = response.currYear;
			  			$scope.monthPaycheck = response.currMonth;
			  			$scope.paycheckUsers = response.dataPaycheck;
		  			}
		  		}).finally(function (){
						$loading.finish('waiting');
					});
			}
	  };
	  $scope.newPaycheck = function (month, year) {
	  	if (month != 0 && year != 0) {
		  	$loading.start('waiting');
		  	Data.post('/bangluong/taomoi', {month: month, year: year}).then(
		  		function (res) {
		  			var response = res.data;
		  			if (!response.error) {
		  				$scope.isFinishPaycheck = (response.finish == '1') ? true : false;
			  			$scope.paycheckUsers = response.dataPaycheck;
		  			}
		  		}).finally(function (){
						$loading.finish('waiting');
					});
			}
	  };
	  function selectRowPaycheck(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
	  	$('td', nRow).unbind('click');
      $('td', nRow).bind('click', function() {
        $scope.$apply(function() {
          $scope.selectedUser = Entities.formatDataPaycheck(aData);
        });
      });
	    return nRow;
	  };
	  $scope.cfmClosePaycheck = function () {
	  	$fancyModal.open({ 
				templateUrl: 'web/templates/finance/confirm_paycheck.html',
				controller: 'SystemController',
				showCloseButton: true,
				scope: $scope
			});
	  };
	  $scope.finishPaycheck = function () {
	  	$loading.start('waiting');
	  	Data.get('/bangluong/khoaso')
	  	.then(function (res) {
	  		var response = res.data;
	  		if (!response.error) {
	  			$fancyModal.close();
	  			$scope.isFinishPaycheck = true;
	  			$state.reload();
		  	}
	  	}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	  $scope.exportPaycheck = function (month, year) {
	  	$loading.start('waiting');
	  	$http({
				url: '/api/bangluong/xuatexcel',
				method: "post",
				headers: {
					'Content-type': 'application/json',
					'Authorization': 'Bearer ' + $scope.token
				},
				data: {'month': month, 'year': year},
				responseType: 'arraybuffer'
			}).success(function (data, status, headers, config) {
				var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
				Data.saveAs(blob, 'Bang_tong_hop_luong.xlsx');
			}).error(function (data, status, headers, config) {
				console.log('Failed to download Excel')
			}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	  $scope.sendMail = function (month, year, dataPaycheck) {
	  	$loading.start('waiting');
	  	Data.post('/bangluong/guimail', {'month': month, 'year': year, data: dataPaycheck})
	  	.then(function (res) {
	  		if (!res.data.error) {
	  			Data.notify_success('Đã gửi thành công');
	  		} else {
	  			Data.notify_error('Đã xảy ra lỗi!');
	  		}
	  	}).finally(function (){
				$loading.finish('waiting');
			});
	  }
	  $scope.printPDF = function (month, year, dataPaycheck) {
	  	$loading.start('waiting');
	  	$http({
				url: 'api/bangluong/inpdf',
				method: "post",
				headers: {
					'Content-type': 'application/json',
					'Authorization': 'Bearer ' + $scope.token
				},
				data: {'month': month, 'year': year, data: dataPaycheck},
				responseType: 'arraybuffer'
			}).success(function (data, status, headers, config) {
				var blob = new Blob([data], {type: "application/pdf"});
				Data.saveAs(blob, 'Phieu_luong_ca_nhan.pdf');
			}).finally(function (){
				$loading.finish('waiting');
			});
	  }
	  $scope.createPhucap = function (dataPhucap) {
	  	$loading.start('waiting');
	  	Data.post('/phucap/taomoi', {data: dataPhucap})
	  	.then(function (res) {
	  		$scope.listPhucap = res.data.phucap;
	  		$scope.listChucdanh = res.data.chucdanh;
	  		Data.notify_success('Đã thêm phụ cấp mới thành công');
	  	}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	  $scope.resetPhucap = function () {
	  	$scope.phucap = {
	  		mota: '',
	  		sotien: '',
	  		ghichu: ''
	  	}
	  };
	  $scope.editPhucap = function (dataPhucap) {
	  	$scope.phucap = angular.copy(dataPhucap);
	  	$scope.isCreating = false;
	  	$scope.isEditing = true;
	  }
	  $scope.updatePhucap = function (dataPhucap) {
	  	$loading.start('waiting');
	  	Data.post('/phucap/capnhat', {data: dataPhucap})
	  	.then(function (res) {
	  		$scope.listPhucap = res.data.phucap;
	  		$scope.listChucdanh = res.data.chucdanh;
	  		Data.notify_success('Cập nhật thành công');
	  	}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	  $scope.cancelUpdatePhucap = function () {
	  	$scope.isCreating = true;
	  	$scope.isEditing = false;
	  	$scope.phucap = {
	  		mota: '',
	  		sotien: '',
	  		ghichu: ''
	  	}
	  };
	  $scope.removePhucap = function (id) {
	  	$loading.start('waiting');
	  	Data.get('/phucap/xoa', {id: id})
	  	.then(function (res) {
	  		$scope.listPhucap = res.data.phucap;
	  		$scope.listChucdanh = res.data.chucdanh;
	  		Data.notify_success('Đã xóa thành công');
	  	}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	}