angular
	.module('hr_manager.statistic')
	.controller('StatisticController', ['Data', '$scope', '$state', '$http', '$loading', StatisticController]);
	
	function StatisticController (Data, $scope, $state, $http, $loading) {
		var vm = this;
		vm.tkChucdanh = tkChucdanh;
		vm.tkTrinhdo = tkTrinhdo;
		vm.tkDotuoi = tkDotuoi;
		vm.tkGioitinh = tkGioitinh;
		vm.tkTongiao = tkTongiao;
		vm.tkDantoc = tkDantoc;	

		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		switch($state.current.name) {
			case 'app.tk-chuc-danh':
		  {	
		  	vm.tkChucdanh('chartBar');
		  	break;
		  }
		  case 'app.tk-do-tuoi':
		  {	
		  	Data.get('/dotuoi/statistic')
		  	.then(function (response) {
		  		$scope.labels_nam = response.data.nhomNam;
		  		$scope.data_nam = response.data.soluongNam;
		  		$scope.labels_nu = response.data.nhomNu;
		  		$scope.data_nu = response.data.soluongNu;
		  	});
		  	vm.tkDotuoi('chartMan');
		  	break;
		  }
		  case 'app.tk-ton-giao':
		  {	
		  	vm.tkTongiao('chartPie');
		  	break;
		  }
		  case 'app.tk-dan-toc':
		  {
		  	vm.tkDantoc('chartPie');
		  	break;
		  }
		  case 'app.tk-trinh-do':
		  {	
		  	vm.tkTrinhdo('chartLine');
		  	break;
		  }
		  case 'app.tk-gioi-tinh':
		  {	
		  	Data.get('/gioitinh/statistic')
		  	.then(function (response) {
		  		$scope.colours = ['#00ADF9', '#949FB1'];
		  		$scope.labels = response.data.year;
		  		$scope.series = ['Nam', 'Nữ'];
		  		$scope.data = [
			  		response.data.nam,
			  		response.data.nu
		  		];
		  	});
		  	vm.tkGioitinh('chartBar');
		  	break;
		  }
		  case 'app.tk-ngay-nghi':
		  {	
		  	Data.get('/ngaynghi/statistic')
		  	.then(function (response) {
		  		$scope.soNgayNghi = response.data.songay;
		  		$scope.loaiNghiPhep = ['Nghỉ có phép', 'Nghỉ không phép'];
		  		$scope.dlNgayNghi = [
			  		response.data.nghicophep,
			  		response.data.nghikophep
		  		];
		  	});
		  	break;
		  }
		}

		function tkTrinhdo (type_chart) {
			if (type_chart=='chartLine') {
				$scope.chartLine = true;
				$scope.chartPie = false;
				Data.get('/trinhdo/statistic')
		  	.then(function (response) {
		  		$scope.labels = response.data.nam;
		  		$scope.series = ['Trên Đại Học', 'Đại học', 'Dưới Đại Học'];
		  		$scope.data = [
			  		response.data.trenDH,
			  		response.data.DH,
			  		response.data.duoiDH
		  		];
		  	});
			} else{
				$scope.chartLine = false;
				$scope.chartPie = true;
				Data.get('/trinhdo_pie/statistic')
		  	.then(function (response) {
		  		$scope.labels = response.data.trinhdo;
		  		$scope.data = response.data.soluong;
		  	});
			}
		};
		function tkGioitinh (type_chart) {
			if (type_chart=='chartLine') {
				$scope.chartLine = true;
				$scope.chartBar = false;
			} else{
				$scope.chartLine = false;
				$scope.chartBar = true;
			}
		};
		function tkDotuoi (type_chart) {
			if (type_chart=='chartMan') {
				$scope.chartMan = true;
				$scope.chartWoman = false;
			} else{
				$scope.chartMan = false;
				$scope.chartWoman = true;
			}
		};
		function tkChucdanh(type_chart) {
			if (type_chart=='chartPie') {
				Data.get('/chucdanh/statistic')
		  	.then(function (response) {
		  		$scope.labels_cd = response.data.tencd;
		  		$scope.data_cd = response.data.soluong;
		  	});
				$scope.chartPie = true;
				$scope.chartBar = false;
			} else{
				Data.get('/chucdanh/statistic')
		  	.then(function (response) {
		  		$scope.labels_cd = response.data.tencd;
		  		$scope.data_cd = [response.data.soluong];
		  	});
				$scope.chartPie = false;
				$scope.chartBar = true;
			}
		};
		function tkDantoc (type_chart) {
			if (type_chart=='chartPie') {
				Data.get('/dantoc/statistic')
		  	.then(function (response) {
		  		$scope.labels_dt = response.data.dantoc;
		  		$scope.data_dt = response.data.soluong;
		  	});
				$scope.chartPie = true;
				$scope.chartBar = false;
			} else{
				Data.get('/dantoc/statistic')
		  	.then(function (response) {
		  		$scope.labels_dt = response.data.dantoc;
		  		$scope.data_dt = [response.data.soluong];
		  	});
				$scope.chartPie = false;
				$scope.chartBar = true;
			}
		};
		function tkTongiao (type_chart) {
			if (type_chart=='chartPie') {
				Data.get('/tongiao/statistic')
		  	.then(function (response) {
		  		for (var i = 0; i < response.data.tongiao.length; i++) {
		  			if (response.data.tongiao[i] == '') {response.data.tongiao[i] = 'Tôn giáo khác';};
		  			if (response.data.tongiao[i] == 'Không') {response.data.tongiao[i] = 'Không tôn giáo';};
		  		};
		  		$scope.labels_tg = response.data.tongiao;
		  		$scope.data_tg = response.data.soluong;
		  	});
				$scope.chartPie = true;
				$scope.chartBar = false;
			} else{
				Data.get('/tongiao/statistic')
		  	.then(function (response) {
		  		for (var i = 0; i < response.data.tongiao.length; i++) {
		  			if (response.data.tongiao[i] == '') {response.data.tongiao[i] = 'Tôn giáo khác';};
		  			if (response.data.tongiao[i] == 'Không') {response.data.tongiao[i] = 'Không tôn giáo';};
		  		};
		  		$scope.labels_tg = response.data.tongiao;
		  		$scope.data_tg = [response.data.soluong];
		  	});
				$scope.chartPie = false;
				$scope.chartBar = true;
			}
		};

		$scope.dsNVNghi = function (evt) {
			var numberDay = (evt[0].label).split(' ')[1];
			Data.get('/chamcong/dsnghi', {'songay':numberDay}).then(
				function (res) {
					$scope.listAbsentUsers = res.data;
				});
			$scope.numDayAbsent = numberDay;
	    $scope.showListAbsent = true;
	  };
	  $scope.exportListAbsent = function(absentStatus, numDayAbsent) {
	  	$loading.start('waiting');
	  	$http({
				url: '/api/chamcong/xuatdsnghi',
				method: "post",
				headers: {
					'Content-type': 'application/json',
					'Authorization': 'Bearer ' + $scope.token
				},
				data: {filter:absentStatus, dayNumber:numDayAbsent},
				responseType: 'arraybuffer'
			}).success(function (data, status, headers, config) {
				var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
				Data.saveAs(blob, 'DSNV_Nghi.xlsx');
			}).error(function (data, status, headers, config) {
				console.log('Failed to download Excel')
			}).finally(function (){
				$loading.finish('waiting');
			});
	  };
	}