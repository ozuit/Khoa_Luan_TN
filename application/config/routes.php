<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'HomeController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
| -------------------------------------------------------------------------
| REST API Routes
| -------------------------------------------------------------------------
*/

$route['api/auth/login'] = 'api/AuthController/login/';
$route['api/auth/logout'] = 'api/AuthController/logout/';
$route['api/auth/check_token'] = 'api/AuthController/checkToken/';
$route['api/user/changepass'] = 'api/UserController/changepassword/';
$route['api/user/fogetpass'] = 'api/UserController/doforget/';
$route['api/account/all'] = 'api/AccountController/showAccount';
$route['api/account/active'] = 'api/AccountController/changeActive';
$route['api/account/level'] = 'api/AccountController/changeLevel';
$route['api/schedule/all'] = 'api/AccountController/readSchedule';
$route['api/schedule/create'] = 'api/AccountController/createSchedule';
$route['api/schedule/update'] = 'api/AccountController/updateSchedule';
$route['api/schedule/delete'] = 'api/AccountController/deleteSchedule';
$route['api/schedule/future'] = 'api/AccountController/countSchedule';

$route['api/user/all'] = 'api/UserController/showUsers/';
$route['api/user/select'] = 'api/UserController/selectUsers/';
$route['api/user/filters'] = 'api/UserController/filtersData/';
$route['api/user/import'] = 'api/UserController/importExl/';
$route['api/user/export'] = 'api/UserController/exportExl/';
$route['api/user/new'] = 'api/UserController/addUser/';
$route['api/user/remove'] = 'api/UserController/removeUser';
$route['api/user/info'] = 'api/UserController/showUser/';
$route['api/user/count_by_cv'] = 'api/UserController/countUserByCV/';
$route['api/user/update'] = 'api/UserController/updateUser/';
$route['api/user/online'] = 'api/UserController/userOnline';
$route['api/user/count_nghiphep'] = 'api/UserController/countNghiPhep';
$route['api/user/hethd'] = 'api/UserController/userHetHD';
$route['api/user/sinhnhat'] = 'api/UserController/userCoSinhNhat';
$route['api/user/ds_sinhnhat'] = 'api/UserController/listUserCoSN';

$route['api/user/dk_nghiphep'] = 'api/UserController/themNghiPhep/';
$route['api/user/ds_nghiphep'] = 'api/UserController/loadNghiPhep/';
$route['api/user/edit_nghiphep'] = 'api/UserController/changeNghiPhep/';
$route['api/user/show_ktkl'] = 'api/UserController/showQuyetdinh/';
$route['api/user/export_ktkl'] = 'api/UserController/xuatQuyetdinh/';
$route['api/user/export_dsnv'] = 'api/UserController/xuatDSNV/';
$route['api/user/addQD'] = 'api/UserController/themQuyetdinh/';
$route['api/user/getQD'] = 'api/UserController/getQuyetdinh/';
$route['api/user/updateQD'] = 'api/UserController/capnhatQD/';
$route['api/user/show_congtac'] = 'api/UserController/showCongtac/';
$route['api/user/export_congtac'] = 'api/UserController/xuatCongtac/';
$route['api/user/addHD'] = 'api/UserController/themHoatdong/';
$route['api/user/getHD'] = 'api/UserController/getHoatdong/';
$route['api/user/updateHD'] = 'api/UserController/capnhatHD/';
$route['api/user/hopdong'] = 'api/UserController/loadContract/';
$route['api/user/filterHD'] = 'api/UserController/filterContract/';
$route['api/user/exportHD'] = 'api/UserController/exportHD/';
$route['api/user/showHD'] = 'api/UserController/showContract/';
$route['api/user/updateContract'] = 'api/UserController/updateContract/';
$route['api/user/export_contract'] = 'api/UserController/xuatContract/';

$route['api/info/all'] = 'api/InfoController/showInfo';
$route['api/info/update'] = 'api/InfoController/updateInfo';
$route['api/info/footer'] = 'api/InfoController/showForfooter';
$route['api/info/image'] = 'api/InfoController/imageUpload';
$route['api/info/feedback'] = 'api/InfoController/feedback';
$route['api/info/announce'] = 'api/InfoController/addAnnounce';
$route['api/announce/update'] = 'api/InfoController/updateAnnounce';
$route['api/announce/delete'] = 'api/InfoController/deleteAnnounce';
$route['api/announce/load'] = 'api/InfoController/loadThongbao';
$route['api/announce/get'] = 'api/InfoController/getThongbao';
$route['api/announce/pagination/(:num)'] = 'api/PaginationController/announce/$1';
$route['api/announce/pagination'] = 'api/PaginationController/announce/1';
$route['api/announce/search/(:any)/(:num)'] = 'api/PaginationController/searchRequest/$1/$2';
$route['api/announce/search/(:any)'] = 'api/PaginationController/searchRequest/$1';

$route['api/database/backup'] = 'api/InfoController/backupDB';
$route['api/database/restore'] = 'api/InfoController/restoreDB';
$route['api/room/all'] = 'api/InfoController/loadRooms';
$route['api/room/detail'] = 'api/InfoController/loadRoom';
$route['api/room/new'] = 'api/InfoController/newRoom';
$route['api/room/update'] = 'api/InfoController/updateRoom';
$route['api/room/remove'] = 'api/InfoController/removeRoom';
$route['api/chucdanh/all'] = 'api/InfoController/loadChucDanh';
$route['api/chucdanh/new'] = 'api/InfoController/newChucDanh';
$route['api/chucdanh/update'] = 'api/InfoController/updateChucDanh';
$route['api/chucdanh/remove'] = 'api/InfoController/removeChucDanh';
$route['api/chucvu/all'] = 'api/InfoController/loadChucVu';
$route['api/chucvu/new'] = 'api/InfoController/newChucVu';
$route['api/chucvu/update'] = 'api/InfoController/updateChucVu';
$route['api/chucvu/remove'] = 'api/InfoController/removeChucVu';
$route['api/chuyenmon/all'] = 'api/InfoController/loadChuyenMon';
$route['api/chuyenmon/new'] = 'api/InfoController/newChuyenMon';
$route['api/chuyenmon/update'] = 'api/InfoController/updateChuyenMon';
$route['api/chuyenmon/remove'] = 'api/InfoController/removeChuyenMon';

$route['api/chucdanh/statistic'] = 'api/StatisticController/tkChucDanh';
$route['api/trinhdo/statistic'] = 'api/StatisticController/tk_TrinhDo';
$route['api/trinhdo_pie/statistic'] = 'api/StatisticController/trinhdoPie';
$route['api/gioitinh/statistic'] = 'api/StatisticController/tk_GioiTinh';
$route['api/tongiao/statistic'] = 'api/StatisticController/tkTonGiao';
$route['api/dantoc/statistic'] = 'api/StatisticController/tkDanToc';
$route['api/dotuoi/statistic'] = 'api/StatisticController/tkDoTuoi';
$route['api/ngaynghi/statistic'] = 'api/StatisticController/tk_NgayNghi';

$route['api/chamcong/kyhieu/all'] = 'api/PayrollController/getKHCC';
$route['api/chamcong/kyhieu/save'] = 'api/PayrollController/saveKHCC';
$route['api/chamcong/kyhieu/delete'] = 'api/PayrollController/deleteKHCC';
$route['api/chamcong/thietlap/get'] = 'api/PayrollController/getSetupCC';
$route['api/chamcong/thietlap/post'] = 'api/PayrollController/updateSetupCC';
$route['api/chamcong/bangchamcong'] = 'api/PayrollController/getPayroll';
$route['api/chamcong/capnhat'] = 'api/PayrollController/updatePayroll';
$route['api/chamcong/hoanthanh'] = 'api/PayrollController/finishPayroll';
$route['api/chamcong/chamcongnhanh'] = 'api/PayrollController/fastPayroll';
$route['api/chamcong/xuatexcel'] = 'api/PayrollController/exportExcel';
$route['api/chamcong/dsnghi'] = 'api/PayrollController/listAbsentUsers';
$route['api/chamcong/xuatdsnghi'] = 'api/PayrollController/exportAbsentUsers';

$route['api/bangluong/dinhmuc'] = 'api/PaycheckController/loadSetupPaycheck';
$route['api/bangluong/update_dinhmuc'] = 'api/PaycheckController/updateSetupPaycheck';
$route['api/bangluong/laydulieu'] = 'api/PaycheckController/loadPaycheck';
$route['api/bangluong/taomoi'] = 'api/PaycheckController/newPaycheck';
$route['api/bangluong/khoaso'] = 'api/PaycheckController/finishPaycheck';
$route['api/bangluong/xuatexcel'] = 'api/PaycheckController/exportPaycheck';
$route['api/bangluong/guimail'] = 'api/PaycheckController/sendMail';
$route['api/bangluong/inpdf'] = 'api/PaycheckController/printPDF';
$route['api/phucap/danhsach'] = 'api/PaycheckController/dsPhuCap';
$route['api/phucap/taomoi'] = 'api/PaycheckController/themPhuCap';
$route['api/phucap/capnhat'] = 'api/PaycheckController/suaPhuCap';
$route['api/phucap/xoa'] = 'api/PaycheckController/xoaPhuCap';