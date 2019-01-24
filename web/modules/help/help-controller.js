angular
	.module('hr_manager.help')
	.controller('HelpController', ['Data', '$scope', '$loading', HelpController]);
	
	function HelpController (Data, $scope, $loading) {
		$('.hr-content').slimScroll({
			height: $(window).height()-175
		});

		$scope.feedback = function(isValid) {
			if (isValid && $scope.email!=='' && $scope.hoten!=='' && $scope.noidung!=='') {
				console.log($scope.email);
				$loading.start('waiting');
				var unindexed_array = $('#frm_feedback').serializeArray();
				var indexed_array = {};
				$.map(unindexed_array, function(n, i) {
					indexed_array[n['name']] = n['value'];
				});
				Data.post('/info/feedback', {'feedback': indexed_array})
				.then(function (response) {
					Data.notify_success(response.data.message);
					$loading.finish('waiting');
					$('#frm_feedback')[0].reset();
					$('.require-input').removeClass('success');
					$scope.email = $scope.hoten = $scope.noidung = '';
				}, function (response) {
					Data.notify_error(response.data.message);
					$loading.finish('waiting');
				});
			}
		};
	}