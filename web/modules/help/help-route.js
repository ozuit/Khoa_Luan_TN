angular
  .module('hr_manager.help', [])
  .config(function config($stateProvider) {
    $stateProvider

    .state('app.gioi-thieu', {
	    cache: true,
	    url: '/gioi-thieu', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/help/gioithieu.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan', {
	    cache: true,
	    url: '',
	    abstract: true, 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/help/huongdan.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  /*************HUONG DAN************/
	  .state('app.huong-dan.tong-quan', {
	    cache: true,
	    url: '/huong-dan/tong-quan', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tongquan.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.dang-nhap', {
	    cache: true,
	    url: '/huong-dan/he-thong/dang-nhap', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/login.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.thong-tin-cty', {
	    cache: true,
	    url: '/huong-dan/he-thong/thong-tin-cong-ty', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/thongtincty.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.tai-khoan', {
	    cache: true,
	    url: '/huong-dan/he-thong/tai-khoan-nguoi-dung', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/taikhoan.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.doi-mk', {
	    cache: true,
	    url: '/huong-dan/he-thong/doi-mat-khau', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/doipass.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.phan-quyen', {
	    cache: true,
	    url: '/huong-dan/he-thong/phan-quyen', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/phanquyen.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.dat-lai-mk', {
	    cache: true,
	    url: '/huong-dan/he-thong/dat-lai-mat-khau', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/datlaimk.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.backup', {
	    cache: true,
	    url: '/huong-dan/he-thong/sao-luu-phuc-hoi', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/backup.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.xin-nghi', {
	    cache: true,
	    url: '/huong-dan/he-thong/xin-nghi-phep', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/xinnghi.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.tao-tb', {
	    cache: true,
	    url: '/huong-dan/he-thong/tao-thong-bao', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hethong/thongbao.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ql-phong-ban', {
	    cache: true,
	    url: '/huong-dan/quan-ly/phong-ban', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/quanly/phongban.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ql-chuc-danh', {
	    cache: true,
	    url: '/huong-dan/quan-ly/cd-cv-cm', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/quanly/chucdanh.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ql-nghi-phep', {
	    cache: true,
	    url: '/huong-dan/quan-ly/nghi-phep', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/quanly/nghiphep.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ql-tap-tin', {
	    cache: true,
	    url: '/huong-dan/quan-ly/tap-tin', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/quanly/taptin.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ds-nhan-vien', {
	    cache: true,
	    url: '/huong-dan/ho-so/danh-sach-nhan-vien', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/dsnhanvien.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.them-nhan-vien', {
	    cache: true,
	    url: '/huong-dan/ho-so/them-nhan-vien', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/themnhanvien.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.cap-nha-hs', {
	    cache: true,
	    url: '/huong-dan/ho-so/cap-nhat-ho-so', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/capnhaths.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ktkl', {
	    cache: true,
	    url: '/huong-dan/ho-so/khen-thuong-ky-luat', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/ktkl.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.cong-tac', {
	    cache: true,
	    url: '/huong-dan/ho-so/cong-tac', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/congtac.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.hd-lao-dong', {
	    cache: true,
	    url: '/huong-dan/ho-so/hop-dong-lao-dong', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/hoso/hdlaodong.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.tl-chamcong', {
	    cache: true,
	    url: '/huong-dan/cham-cong/thiet-lap', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienluong/tlchamcong.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.bang-chamcong', {
	    cache: true,
	    url: '/huong-dan/cham-cong/tong-hop', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienluong/chamcong.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.dinh-muc-luong', {
	    cache: true,
	    url: '/huong-dan/tien-luong/dinh-muc', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienluong/dinhmucluong.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.phu-cap', {
	    cache: true,
	    url: '/huong-dan/tien-luong/phu-cap', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienluong/phucap.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.bang-luong', {
	    cache: true,
	    url: '/huong-dan/tien-luong/tong-hop', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienluong/bangluong.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.thong-ke', {
	    cache: true,
	    url: '/huong-dan/thong-ke', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/thongke.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.ghi-chu', {
	    cache: true,
	    url: '/huong-dan/ghi-chu', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienich/ghichu.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.tro-chuyen', {
	    cache: true,
	    url: '/huong-dan/tro-chuyen', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienich/trochuyen.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  .state('app.huong-dan.videocall', {
	    cache: true,
	    url: '/huong-dan/video-call', 
	    views: {
	      'instruction': {
	        templateUrl: 'web/templates/help/instruction/tienich/videocall.html',
	        controller: 'HelpController'
	      }
	    }
	  })
	  /**********************************/
	  .state('app.phan-hoi', {
	    cache: true,
	    url: '/phan-hoi', 
	    views: {
	      'content': {
	        templateUrl: 'web/templates/help/phanhoi.html',
	        controller: 'HelpController'
	      }
	    }
	  })
  });