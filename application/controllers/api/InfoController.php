<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require 'BaseController.php';
/**
* 
*/
class InfoController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
		$this->load->model('base_model');
	}

	public function addAnnounce_post() {
		$this->load->helper('utf8');
		$data = $this->post('data');
		$config['upload_path'] = './assets/kcfinder/upload/other/';
        $config['allowed_types'] = '*';
        $config['overwrite'] = TRUE;
		$this->load->library('upload', $config);
		if (count($_FILES) > 0) {
			$files = $_FILES;
      $cpt = count($_FILES['files']['name']);
      if ($cpt > 0) {
      	for($i=0; $i<$cpt; $i++)
        {
            $filename= explode('.', $files['files']['name'][$i]);
            $exten= $filename[count($filename)-1];
            $name= trim($files['files']['name'][$i], '.'.$exten);
            $_FILES['tmp']['name']= remove_utf8($name).'.'.$exten;
            $_FILES['tmp']['type']= $files['files']['type'][$i];
            $_FILES['tmp']['tmp_name']= $files['files']['tmp_name'][$i];
            $_FILES['tmp']['error']= $files['files']['error'][$i];
            $_FILES['tmp']['size']= $files['files']['size'][$i];
            $this->upload->do_upload('tmp');
            $filesData[] = $this->upload->data()['file_name'];
				}
				$data['files'] = implode(',', $filesData);
      }
		}
		$data['ngaytao'] = date('Y-m-d H:i:s');
    $id = $this->base_model->insertTable('thongbao', $data);
    $arrUser = $this->post('listUser');
    if (count($arrUser) == 0) {
    	$arrUser = array();
    	$arrTemp = $this->base_model->getTable('taikhoan', 'id');
    	foreach ($arrTemp as $key => $value) {
    		$arrUser[] = $value['id'];
    	}
    }
    if(array_search($this->user_authed->user_id, $arrUser) === false) {
	    $arrUser[] = $this->user_authed->user_id;
		}
    $sqlQuery = "INSERT INTO `user_announce` (user_id, announce_id, isRead) VALUES";
    foreach ($arrUser as $key => $value) {
    	$sqlQuery .= "(".$value.",".$id.",0),";
    }
    $sqlQuery = rtrim($sqlQuery, ",");
    if($this->db->query($sqlQuery))
		{
			$this->set_response([
	    	'status' => TRUE,
	    	'message' => 'Thông báo đã được thêm thành công',
	    	'id' => $id
			], REST_Controller::HTTP_OK);
		}
		else {
			$this->set_response([
				'status'=> FALSE,
				'message'=> 'Thêm thông báo thất bại'
			], REST_Controller::HTTP_OK);
		}
	}

	public function updateAnnounce_post() {
		$this->load->helper('utf8');
		$data = $this->post('data');
		unset($data['files']);
		$config['upload_path'] = './assets/kcfinder/upload/other/';
        $config['allowed_types'] = '*';
        $config['overwrite'] = TRUE;
		$this->load->library('upload', $config);
		if (count($_FILES) > 0) {
			$files = $_FILES;
    	$cpt = count($_FILES['files']['name']);
      if ($cpt > 0) {
      	for($i=0; $i<$cpt; $i++)
        {
          $filename= explode('.', $files['files']['name'][$i]);
          $exten= $filename[count($filename)-1];
          $name= trim($files['files']['name'][$i], '.'.$exten);
          $_FILES['tmp']['name']= remove_utf8($name).'.'.$exten;
          $_FILES['tmp']['type']= $files['files']['type'][$i];
          $_FILES['tmp']['tmp_name']= $files['files']['tmp_name'][$i];
          $_FILES['tmp']['error']= $files['files']['error'][$i];
          $_FILES['tmp']['size']= $files['files']['size'][$i];
          $this->upload->do_upload('tmp');
          $filesData[] = $this->upload->data()['file_name'];
				}
				$data['files'] = implode(',', $filesData);
      }
		}
		$idNotification = $data['id'];
		unset($data['hoten']);
		unset($data['id']);
		unset($data['ngaytao']);
		unset($data['nguoitao']);
		unset($data['user_id']);
    $this->base_model->updateTable('thongbao', 'id', $idNotification, $data);
    $this->set_response([
    	'status' => TRUE,
    	'message' => 'Cập nhật thông báo thành công',
		], REST_Controller::HTTP_OK);
	}

	public function deleteAnnounce_get() {
		$idNotification = $this->get('id');
		$this->base_model->deleteTable('thongbao', 'id', $idNotification);
		$this->set_response([
    	'status' => TRUE,
    	'message' => 'Thông báo đã được xóa thành công',
		], REST_Controller::HTTP_OK);
	}

	public function loadThongbao_get() {
		$this->load->model('info_model');
		$userID = $this->user_authed->user_id;
		$data = $this->info_model->loadThongBao($userID);
		foreach ($data as $key => $value) {
			$date=date_create($value['ngaytao']);
			$data[$key]['ngaytao'] = date_format($date,"d/m/Y - H:i");
			$userInfo = $this->base_model->getOneByField('nhanvien', 'taikhoan', $value['nguoitao']);
			$data[$key]['hoten'] = $userInfo['hoten'];
			$data[$key]['user_id'] = $userInfo['taikhoan'];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function getThongbao_get() {
		$id = $this->get('id');
		$user_id = $this->user_authed->user_id;
		$this->load->model('info_model');
		//Kiểm tra trạng thái read thông báo
		if (!$this->info_model->getReadNotify($user_id, $id)) {
			$this->info_model->setReadNotify($user_id, $id);
		}
		//Lấy dữ liệu thông báo
		$data = $this->base_model->getOneByField('thongbao', 'id', $id);
		if($data) {
			$date=date_create($data['ngaytao']);
			$data['ngaytao'] = date_format($date,"d/m/Y - H:i");
			if ($data['files'] == '') {
				unset($data['files']);
			}
			else {
				$data['files'] = explode(',', $data['files']);
			}
			$userInfo = $this->base_model->getOneByField('nhanvien', 'taikhoan', $data['nguoitao']);
			$data['hoten'] = $userInfo['hoten'];
			$data['user_id'] = $userInfo['taikhoan'];
			$this->set_response($data, REST_Controller::HTTP_OK);
		}
	}

	//Các chức năng trợ giúp
	public function feedback_post() {
		$data = $this->post('feedback');
		$email = "viettops.net@gmail.com";
		if ($data['subject'] == '') {
			$subject = 'Góp ý - Hệ thống quản lý nhân sự';
		}
		else {
			$subject = $data['subject'];
		}
		$content = $data['noidung'];
		$this->sendMail($data['email'], $data['hoten'], $email, $subject, $content);
		$this->set_response([
			'status' => TRUE,
			'message' => 'Phản hồi đã được ghi nhận'
		], REST_Controller::HTTP_OK); 
	}


	//Các chức năng quản lý
	public function loadRooms_get() {
		$data['rooms'] = $this->base_model->getTable('phongban');
		$data['number'] = count($data['rooms']);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function loadRoom_get() {
		$mpb = $this->get('mpb');
		$data['room'] = $this->base_model->getOneByField('phongban', 'mpb', $mpb);
		$data['members'] = $this->base_model->getAllByField('nhanvien', 'phongban', $mpb);
		$data['number'] = count($data['members']);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function newRoom_post() {
		$data = $this->post('room');
		$data['mpb'] = strtoupper($data['mpb']);
		$data['tenpb'] = ucfirst($data['tenpb']);
		$data['truongpb'] = ucwords($data['truongpb']);
		$data['ngaytao'] = date('Y-m-d', time());
		$data['ngaysua'] = date('Y-m-d', time());
		if($this->base_model->insertTable('phongban', $data, 'mpb') === FALSE) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã phòng ban đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm một phòng ban mới'
			], REST_Controller::HTTP_OK);
		}
	}

	public function updateRoom_put() {
		$data = $this->put('room');
		$data['truongpb'] = ucwords($data['truongpb']);
		$ID_current = $data['current_val'];
		if($this->base_model->checkExist('phongban', 'mpb', $data['mpb']) && $ID_current != $data['mpb']) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã phòng ban đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {		
			unset($data['current_val']);
			$data['ngaysua'] = date('Y-m-d', time());
			$this->base_model->updateTable('phongban', 'mpb', $ID_current, $data);
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Cập nhật phòng ban thành công'
			], REST_Controller::HTTP_OK);
		}
	}

	public function removeRoom_get() {
		$this->base_model->deleteTable('phongban', 'mpb', $this->get('mpb'));
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã xóa thành công'
		], REST_Controller::HTTP_OK);
	}

	public function loadChucDanh_get() {
		$this->set_response($this->base_model->getTable('chucdanh'), REST_Controller::HTTP_OK);
	}

	public function newChucDanh_post() {
		$data = $this->post('chucdanh');
		$data['ngaytao'] = date('Y-m-d', time());
		$data['ngaysua'] = date('Y-m-d', time());
		if($this->base_model->insertTable('chucdanh', $data, 'mcd') === FALSE) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã chức danh đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm một chức danh mới'
			], REST_Controller::HTTP_OK);
		}
	}

	public function updateChucDanh_put() {
		$data = $this->put('chucdanh');
		$ID_current = $data['current_val'];
		if($this->base_model->checkExist('chucdanh', 'mcd', $data['mcd']) && $ID_current != $data['mcd']) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã chức danh đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {	
			unset($data['current_val']);
			$data['ngaysua'] = date('Y-m-d', time());
			$this->base_model->updateTable('chucdanh', 'mcd', $ID_current, $data);
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Cập nhật chức danh thành công'
			], REST_Controller::HTTP_OK);
		}
	}

	public function removeChucDanh_get() {
		$this->base_model->deleteTable('chucdanh', 'mcd', $this->get('mcd'));
		$this->set_response(REST_Controller::HTTP_OK);
	}

	public function loadChucVu_get() {
		$this->set_response($this->base_model->getTable('chucvu'), REST_Controller::HTTP_OK);
	}

	public function newChucVu_post() {
		$data = $this->post('chucvu');
		$data['ngaytao'] = date('Y-m-d', time());
		$data['ngaysua'] = date('Y-m-d', time());
		if($this->base_model->checkExist('chucvu', 'ID', $data['ID'])) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Chức vụ này đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {
			$this->base_model->insertTable('chucvu', $data, 'tenchucvu');
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm một chức vụ mới'
			], REST_Controller::HTTP_OK);
		}
	}

	public function updateChucVu_put() {
		$data = $this->put('chucvu');
		$ID_current = $data['current_val'];
		if($this->base_model->checkExist('chucvu', 'ID', $data['ID']) && $ID_current != $data['ID']) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã chức vụ đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {	
			unset($data['current_val']);
			$data['ngaysua'] = date('Y-m-d', time());
			$this->base_model->updateTable('chucvu', 'ID', $ID_current, $data);
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Cập nhật chức vụ thành công'
			], REST_Controller::HTTP_OK);
		}
	}

	public function removeChucVu_get() {
		$this->base_model->deleteTable('chucvu', 'ID', $this->get('mcv'));
		$this->set_response(REST_Controller::HTTP_OK);
	}

	public function loadChuyenMon_get() {
		$this->set_response($this->base_model->getTable('chuyenmon'), REST_Controller::HTTP_OK);
	}

	public function newChuyenMon_post() {
		$data = $this->post('chuyenmon');
		$data['ngaytao'] = date('Y-m-d', time());
		$data['ngaysua'] = date('Y-m-d', time());

		if($this->base_model->insertTable('chuyenmon', $data, 'ID') === FALSE) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Chuyên môn này đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm mới một chuyên môn'
			], REST_Controller::HTTP_OK);
		}
	}

	public function updateChuyenMon_put() {
		$data = $this->put('chuyenmon');
		$ID_current = $data['current_val'];
		if($this->base_model->checkExist('chuyenmon', 'ID', $data['ID']) && $ID_current != $data['ID']) {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã chuyên môn đã tồn tại!'
			], REST_Controller::HTTP_CONFLICT);
		}
		else {	
			unset($data['current_val']);
			$data['ngaysua'] = date('Y-m-d', time());
			$this->base_model->updateTable('chuyenmon', 'ID', $ID_current, $data);
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Cập nhật chuyên môn thành công'
			], REST_Controller::HTTP_OK);
		}
	}

	public function removeChuyenMon_get() {
		$this->base_model->deleteTable('chuyenmon', 'ID', $this->get('mcm'));
		$this->set_response(REST_Controller::HTTP_OK);
	}

	public function infoNewMember_get() {
		$data = array();
		//Lấy danh sách phòng ban
		$this->db->select('mpb, tenpb');
		$query = $this->db->get('phongban');
		$data['phongban'] = $query->result_array();
		//Lấy danh sách chức danh
		$this->db->select('mcd, tencd');
		$query = $this->db->get('chucdanh');
		$data['chucdanh'] = $query->result_array();
		//Lấy danh sách chức vụ
		$this->db->select('ID, chucvu');
		$query = $this->db->get('chucvu');
		$data['chucvu'] = $query->result_array();
		//Lấy danh sách chuyên môn
		$this->db->select('ID, tenchuyenmon');
		$query = $this->db->get('chuyenmon');
		$data['chuyenmon'] = $query->result_array();
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function showInfo_get() {
		$query = $this->db->get('congty');
		$this->set_response($query->row_array(), REST_Controller::HTTP_OK);
	}

	public function showForfooter_get() {
		$this->db->select('tendonvi, website');
		$query = $this->db->get('congty');
		$this->set_response($query->row_array(), REST_Controller::HTTP_OK);
	}

	public function updateInfo_put() {
		$data = $this->put('data');
	
		if ($this->db->update('congty', $data) == FALSE) {
			$this->set_response([
            	'status' => FALSE,
            	'message' => 'Cập nhật không thành công!'
			], REST_Controller::HTTP_NOT_MODIFIED);
		}
		else {
			$this->set_response([
            	'status' => TRUE,
            	'message' => 'Cập nhật thông tin thành công!'
			], REST_Controller::HTTP_OK);
		}
	}

	function imageUpload_post()
	{
		$config['upload_path'] = './assets/kcfinder/upload/files/images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['overwrite'] = TRUE;

		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('file'))
		{
			$this->set_response([
            	'status' => FALSE,
            	'error' => $this->upload->display_errors()
			], REST_Controller::HTTP_NOT_FOUND);
		}
		else
		{
			$this->set_response([
				'status' => TRUE,
				'image_name' => $this->upload->data()['file_name']
			], REST_Controller::HTTP_OK);
		}
	}

	public function backupDB_post() {
		$fileName = $this->post('fileName');
		$this->load->dbutil();

		try {
			// $backup = $this->do_backup(array('chucdanh', 'chucvu', 'chuyenmon', 'congtac', 'congty', 'hopdong', 'ktkl', 'luong', 'nghile', 'nghiphep', 'nhanvien', 'phongban', 'phucap', 'taikhoan', 'thongbao'));
			$tables = $this->db->list_tables();
			$backup = $this->do_backup($tables);
		}
		catch(Exception $e) {echo "";}
		
		$this->load->helper('file');

		if(write_file('./assets/kcfinder/upload/files/backups/'. $fileName .'.sql', $backup)) {
			$this->set_response([
	        	'status' => TRUE,
	        	'message' => 'Sao lưu dữ liệu thành công!',
	        	'fileURL' => '/assets/kcfinder/upload/files/backups/'. $fileName .'.sql'
			], REST_Controller::HTTP_OK);
		}
		else {
			$this->set_response([
	        	'status' => FALSE,
	        	'message' => 'Sao lưu dữ liệu thất bại!'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	function do_backup($tables = array(), $add_drop = TRUE, $add_insert = TRUE)
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
		$newline = PHP_EOL;

	    if (count($tables) == 0)
	    {
	        return FALSE;
	    }

	    // Extract the prefs for simplicity
	    extract($tables);

	    // Build the output
	    $output = 'SET FOREIGN_KEY_CHECKS=0;endLine'.$newline.$newline;
	    foreach ((array)$tables as $table)
	    {

	        // Get the table schema
	        $query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.'.$table);
	        
	        // No result means the table name was invalid
	        if ($query === FALSE)
	        {
	            continue;
	        }
	        
	        // Write out the table schema
	        // $output .= '--'.$newline.'-- TABLE STRUCTURE FOR: '.$table.$newline.'--'.$newline.$newline;

	        if ($add_drop == TRUE)
	        {
	            $output .= 'DROP TABLE IF EXISTS '.$table.';endLine'.$newline;
	        }
	        
	        $i = 0;
	        $result = $query->result_array();
	        foreach ($result[0] as $val)
	        {
	            if ($i++ % 2)
	            {                     
	                $output .= $val.';endLine'.$newline.$newline;
	            }
	        }
	        
	        // If inserts are not needed we're done...
	        if ($add_insert == FALSE)
	        {
	            continue;
	        }

	        // Grab all the data from the current table
	        $query = $this->db->query("SELECT * FROM $table");
	        
	        if ($query->num_rows() == 0)
	        {
	            continue;
	        }
	    
	        // Fetch the field names and determine if the field is an
	        // integer type.  We use this info to decide whether to
	        // surround the data with quotes or not
	        
	        $i = 0;
	        $field_str = '';
	        $is_int = array();
	        while ($field = mysql_fetch_field($query->result_id))
	        {
	            // Most versions of MySQL store timestamp as a string
	            $is_int[$i] = (in_array(
	                                    strtolower(mysql_field_type($query->result_id, $i)),
	                                    array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'), 
	                                    TRUE)
	                                    ) ? TRUE : FALSE;
	                                    
	            // Create a string of field names
	            $field_str .= '`'.$field->name.'`, ';
	            $i++;
	        }
	        
	        // Trim off the end comma
	        $field_str = preg_replace( "/, $/" , "" , $field_str);
	        
	        
	        // Build the insert string
	        $x = 0;
	        $output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES'.$newline;
	        foreach ($query->result_array() as $row)
	        {
	            $val_str = '';
	        
	            $i = 0;
	            foreach ($row as $v)
	            {
	                // Is the value NULL?
	                if ($v === NULL)
	                {
	                    $val_str .= 'NULL';
	                }
	                else
	                {
	                    // Escape the data if it's not an integer
	                    if ($is_int[$i] == FALSE)
	                    {
	                        $val_str .= $this->db->escape($v);
	                    }
	                    else
	                    {
	                        $val_str .= $v;
	                    }                    
	                }                    
	                
	                // Append a comma
	                $val_str .= ', ';
	                $i++;
	            }
	            
	            // Remove the comma at the end of the string
	            $val_str = preg_replace( "/, $/" , "" , $val_str);
	                            
	            // Build the INSERT string
	            if($x != $query->num_rows() - 1)
	            {
	                $output .= '('.$val_str.'),'.$newline;
	            }
	            else
	            {
	                $output .= '('.$val_str.')';
	            }
	            $x++;
	        }
	        
	        $output .= ';endLine'.$newline.$newline;
	    }

	    return $output;
	}

	public function restoreDB_post() {
		$this->cache->delete('contract');
		$this->cache->delete('user');
		$config['upload_path'] = './assets/kcfinder/upload/files/backups/';
		$config['allowed_types'] = '*';
		$config['overwrite'] = TRUE;

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('file'))
		{
			$this->set_response([
            	'status' => FALSE,
            	'error' => $this->upload->display_errors()
			], REST_Controller::HTTP_BAD_REQUEST);
		}
		else
		{
			$fileURL = './assets/kcfinder/upload/files/backups/'.$this->upload->data()['file_name'];
			$data = file_get_contents($fileURL);
			$sqls = explode(';endLine', $data);
			array_pop($sqls);
			foreach($sqls as $statement){
			    $statment = $statement . ";";
			    $this->db->query($statement);   
			}
			$this->set_response([
				'status' => TRUE,
				'message' => 'Dữ liệu đã được phục hồi thành công!'
			], REST_Controller::HTTP_OK);
		}
	}
}