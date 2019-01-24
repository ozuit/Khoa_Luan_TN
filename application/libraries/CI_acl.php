<?php
 
//figure out the path to libraries. (your path may be different)
$libpath = rtrim(BASEPATH,'system/');
$libpath = $libpath.'/application/libraries';
 
// Set the include path.
// A more permanent solution would be to add this to your php ini file
set_include_path(get_include_path() . PATH_SEPARATOR . $libpath);
require_once 'Zend/Acl.php';
class CI_acl extends Zend_acl
{
    private $CI;
    function __construct()
    {
        $this->CI =& get_instance();


        //Create roles
        $this->addRole(new Zend_Acl_Role('member'));
        $this->addRole(new Zend_Acl_Role('admin'),'member');
        $this->addRole(new Zend_Acl_Role('hr_manager'),'member');
        $this->addRole(new Zend_Acl_Role('accountant'),'member');
        $this->addRole(new Zend_Acl_Role('director'), null);


        //create resources
        $this->add(new Zend_Acl_Resource('AccountController'));
        $this->add(new Zend_Acl_Resource('AccountController:showAccount'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:changeActive'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:changeLevel'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:readSchedule'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:createSchedule'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:updateSchedule'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:deleteSchedule'), 'AccountController');
        $this->add(new Zend_Acl_Resource('AccountController:countSchedule'), 'AccountController');

        $this->add(new Zend_Acl_Resource('UserController'));
        $this->add(new Zend_Acl_Resource('UserController:listUserCoSN'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:userCoSinhNhat'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:userHetHD'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:countNghiPhep'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:userOnline'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:showUsers'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:filtersData'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:changepassword'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:doforget'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:importExl'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:exportExl'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:addUser'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:removeUser'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:showUser'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:countUserByCV'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:updateUser'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:themNghiPhep'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:loadNghiPhep'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:changeNghiPhep'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:selectUsers'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:showQuyetdinh'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:xuatQuyetdinh'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:xuatDSNV'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:themQuyetdinh'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:getQuyetdinh'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:capnhatQD'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:showCongtac'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:xuatCongtac'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:themHoatdong'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:getHoatdong'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:capnhatHD'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:loadContract'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:filterContract'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:exportHD'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:showContract'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:updateContract'), 'UserController');
        $this->add(new Zend_Acl_Resource('UserController:xuatContract'), 'UserController');

        $this->add(new Zend_Acl_Resource('InfoController'));
        $this->add(new Zend_Acl_Resource('InfoController:showInfo'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateInfo'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:backupDB'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:restoreDB'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:imageUpload'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:feedback'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:addAnnounce'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateAnnounce'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:deleteAnnounce'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadThongbao'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:getThongbao'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadRooms'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadRoom'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:newRoom'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateRoom'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:removeRoom'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadChucDanh'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:newChucDanh'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateChucDanh'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:removeChucDanh'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadChucVu'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:newChucVu'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateChucVu'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:removeChucVu'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:loadChuyenMon'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:newChuyenMon'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:updateChuyenMon'), 'InfoController');
        $this->add(new Zend_Acl_Resource('InfoController:removeChuyenMon'), 'InfoController');

        $this->add(new Zend_Acl_Resource('PaginationController'));
        $this->add(new Zend_Acl_Resource('PaginationController:announce'), 'PaginationController');
        $this->add(new Zend_Acl_Resource('PaginationController:searchRequest'), 'PaginationController');

        $this->add(new Zend_Acl_Resource('StatisticController'));
        $this->add(new Zend_Acl_Resource('StatisticController:tkChucDanh'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tk_TrinhDo'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:trinhdoPie'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tk_GioiTinh'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tkTonGiao'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tkDanToc'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tkDoTuoi'), 'StatisticController');
        $this->add(new Zend_Acl_Resource('StatisticController:tk_NgayNghi'), 'StatisticController');
     
        $this->add(new Zend_Acl_Resource('PayrollController'));
        $this->add(new Zend_Acl_Resource('PayrollController:getKHCC'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:saveKHCC'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:deleteKHCC'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:getSetupCC'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:updateSetupCC'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:getPayroll'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:updatePayroll'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:finishPayroll'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:fastPayroll'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:exportExcel'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:listAbsentUsers'), 'PayrollController');
        $this->add(new Zend_Acl_Resource('PayrollController:exportAbsentUsers'), 'PayrollController');

        $this->add(new Zend_Acl_Resource('PaycheckController'));
        $this->add(new Zend_Acl_Resource('PaycheckController:loadSetupPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:updateSetupPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:loadPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:newPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:finishPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:exportPaycheck'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:sendMail'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:printPDF'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:dsPhuCap'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:themPhuCap'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:suaPhuCap'), 'PaycheckController');
        $this->add(new Zend_Acl_Resource('PaycheckController:xoaPhuCap'), 'PaycheckController');
           
        //Set up access rights   
        $this->allow('member', array('AccountController', 'UserController', 'InfoController'), array('showForfooter', 'feedback', 'updateUser', 'showUser', 'selectUsers', 'changepassword', 'loadThongbao', 'getThongbao', 'themNghiPhep', 'showInfo', 'readSchedule', 'createSchedule', 'updateSchedule', 'deleteSchedule'));
        $this->allow('member', array('PaginationController'), null);
        $this->allow('admin', array('AccountController', 'UserController', 'InfoController'), array('deleteAnnounce', 'updateAnnounce', 'backupDB', 'restoreDB', 'updateInfo', 'imageUpload', 'showAccount', 'changeLevel', 'doforget'));
        $this->allow('hr_manager', array('UserController', 'InfoController', 'AccountController'), array('userHetHD', 'listUserCoSN', 'countSchedule', 'userCoSinhNhat', 'countNghiPhep', 'userOnline', 'deleteAnnounce', 'updateAnnounce', 'showUsers', 'xuatContract', 'updateContract', 'showContract', 'exportHD', 'filterContract', 'loadContract', 'capnhatHD', 'getHoatdong', 'themHoatdong', 'xuatCongtac', 'showCongtac', 'capnhatQD', 'getQuyetdinh', 'themQuyetdinh', 'xuatDSNV', 'xuatQuyetdinh', 'showQuyetdinh', 'importExl', 'exportExl', 'filtersData', 'selectUsers', 'changeNghiPhep', 'loadNghiPhep', 'removeChuyenMon', 'updateChuyenMon', 'newChuyenMon', 'loadChuyenMon', 'removeChucVu', 'loadChucVu', 'newChucVu' ,'updateChucVu' , 'removeChucDanh', 'updateChucDanh', 'newChucDanh' ,'loadChucDanh' ,'updateRoom', 'newRoom', 'removeRoom', 'loadRoom','loadRooms' , 'addUser', 'removeUser', 'addAnnounce', 'updateInfo', 'imageUpload'));
        $this->allow('hr_manager', array('StatisticController'), null);
        $this->allow('accountant', array('InfoController'), array('deleteAnnounce', 'updateAnnounce', 'updateInfo', 'imageUpload', 'addAnnounce'));
        $this->allow('accountant', array('PayrollController', 'PaycheckController', 'StatisticController'), null);
        $this->allow('director', null);

        $controller = $this->CI->router->fetch_class();
        $action = $this->CI->router->fetch_method();
        $level = $this->CI->session->userdata('level');

        switch($level){ 
            case '1': 
                $role = 'director'; 
                break; 
            case '2': 
                $role = 'hr_manager'; 
                break;
            case '3': 
                $role = 'accountant'; 
                break;
            case '4': 
                $role = 'admin'; 
                break;
            case '5': 
                $role = 'member'; 
                break;
        } 
        if(!$this->isAllowed($role, $controller, $action)){ 
            redirect(base_url('restricted.html'));
        } 
        else {
            $this->CI->session->unset_userdata('level');
        }
    }
}