<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
require 'BaseController.php';
class UserController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
	}

	public function xuatContract_post() {
		$manv = $this->post('manv');
		$data = $this->getContract($manv);
		$this->load->library('pdf');
		$this->pdf->load_view('template_pdf/hopdong.html', $data);
		$this->pdf->set_paper('a4', 'portrait');
		$this->pdf->render();
		// $this->pdf->stream("hopdong.pdf");
		$data = $this->pdf->output();
		echo ($data);
	}

	public function updateContract_post() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->delete('contract');
		$infoContract = $this->post('info_contract');
		$this->db->select('id');
		$this->db->where('mnv', $infoContract['nguoidd']);
		$query = $this->db->get('nhanvien');
		$infoContract['nguoidd'] = $query->row_array()['id'];
		foreach ($infoContract as $key => $value) {
			if ($value == '') {
				$infoContract[$key] = NULL;
			}
		}
		$infoContract['ngaysua'] = date('Y-m-d', time());
 		$this->load->model('base_model');
		$this->base_model->updateTable('hopdong', 'id', $infoContract['id'], $infoContract);
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã cập nhật thành công'
		], REST_Controller::HTTP_OK);
	}

	public function showContract_get() {
		$manv = $this->get('manv');
		$data = $this->getContract($manv);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	private function getContract($manv) {
		$data = array();
		$this->load->model('user_model');
		$this->load->model('base_model');
		//Thông tin bên B
		$benB = $this->user_model->getUserByID($manv);
		$data['mnv'] = $benB['mnv'];
		$data['hotenB'] = $benB['hoten'];
		$data['quoctichB'] = $benB['quoctich'];
		$data['ngaysinhB'] = $benB['ngaysinh'];
		$data['noisinhB'] = $benB['noisinh'];
		$data['thuongtruB'] = $benB['thuongtru'];
		$data['chucvuB'] = $this->base_model->getOneByField('chucvu', 'ID', $benB['chucvu'])['tenchucvu'];
		$data['cmndB'] = $benB['cmnd'];
		$data['ngaycapB'] = $benB['ngaycap'];
		$data['noicapB'] = $benB['noicap'];
		//Lấy thông tin hợp đồng
		$hopdong = $this->base_model->getOneByField('hopdong', 'id', $benB['hopdong']);
		$data['id'] = $hopdong['id'];
		$data['mahd'] = $hopdong['mahd'];
		$data['loaihd'] = $hopdong['loaihd'];
		$data['thoihan'] = $hopdong['thoihan'];
		$data['giolamviec'] = $hopdong['giolamviec'];
		$data['ngayhieuluc'] = $hopdong['ngayhieuluc'];
		$data['ngayhethan'] = $hopdong['ngayhethan'];
		$data['luongcb'] = $hopdong['luongcb'];
		$data['htTraluong'] = $hopdong['htTraluong'];
		$data['ngaytraluong'] = $hopdong['ngaytraluong'];
		$data['phucap'] = $hopdong['phucap'];
		$data['baohiem'] = $hopdong['baohiem'];
		$data['noitao'] = $hopdong['noitao'];
		$data['ngaytao'] = $hopdong['ngaytao'];
		$data['ghichu'] = $hopdong['ghichu'];	
		//Thông tin bên A
		$benA = $this->base_model->getOneByField('nhanvien', 'id', $hopdong['nguoidd']);
		$data['mnvA'] = $benA['mnv'];
		$data['hotenA'] = $benA['hoten'];
		$data['quoctichA'] = $benA['quoctich'];
		$data['chucvuA'] = $this->base_model->getOneByField('chucvu', 'ID', $benA['chucvu'])['tenchucvu'];
		//Lấy thông tin công ty
		$congty = $this->base_model->getTable('congty', 'tendonvi, dienthoai, diachi')[0];
		$data['tenCty'] = $congty['tendonvi'];
		$data['sdtCty'] = $congty['dienthoai'];
		$data['diachiCty'] = $congty['diachi'];
		return $data;
	}

	public function exportHD_post() {
		$state = $this->post('state');
		$this->load->model('user_model');
		$data = $this->user_model->selectHD('nhanvien.hoten, hopdong.mahd, hopdong.loaihd, hopdong.thoihan, hopdong.ngayhieuluc, hopdong.ngayhethan, hopdong.nguoidd, hopdong.luongcb');
		foreach ($data as $key => $value) {
			$this->db->where('id', $value['nguoidd']);
    	$query=$this->db->get('nhanvien');
			$data[$key]['nguoidd'] = $query->row_array()['hoten'];
			if(strtotime($value['ngayhethan']) - strtotime(date('Y-m-d', time())) > 0 || $value['loaihd'] == 'Hợp đồng không thời hạn') {
				$data[$key]['trangthai'] = 'Còn hiệu lực';
				if ($state == 'Hết hiệu lực') {
					unset($data[$key]);
				}
			} else {
				$data[$key]['trangthai'] = 'Hết hiệu lực';
				if ($state == 'Còn hiệu lực') {
					unset($data[$key]);
				}
			}
		}
		//load our new PHPExcel library
    $this->load->library('PHPExcel');
    // Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
 		// Add header
 		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0, 1, "BẢNG DANH SÁCH HỢP ĐỒNG LAO ĐỘNG");
		$sheet->mergeCells('A1:J1');
		$sheet->getStyle('A1')->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
		$sheet->getStyle("A1:J1")->getFont()->setSize(16);
		$sheet->getStyle("A1:J1")->getFont()->setBold(true);
    //activate worksheet number 1
    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A3', 'STT')
		->setCellValue('B3', 'Họ và tên')
		->setCellValue('C3', 'Mã hợp đồng')
		->setCellValue('D3', 'Loại hợp đồng')
		->setCellValue('E3', 'Thời hạn hợp đồng')
		->setCellValue('F3', 'Ngày có hiệu lực')
		->setCellValue('G3', 'Ngày hết hiệu lực')
		->setCellValue('H3', 'Người đại diện')
		->setCellValue('I3', 'Lương căn bản')
		->setCellValue('J3', 'Trạng thái');

		$i = 4;
		foreach ($data as $row)
		{
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $i-3)
			->setCellValue('B'.$i, $row['hoten'])
			->setCellValue('C'.$i, $row['mahd'])
			->setCellValue('D'.$i, $row['loaihd'])
			->setCellValue('E'.$i, $row['thoihan'])
			->setCellValue('F'.$i, DateTime::createFromFormat('Y-m-d', $row['ngayhieuluc'])->format('d/m/Y'))
			->setCellValue('H'.$i, $row['nguoidd'])
			->setCellValue('I'.$i, $row['luongcb'])
			->setCellValue('J'.$i, $row['trangthai']);
			if ($row['ngayhethan']!='') {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, DateTime::createFromFormat('Y-m-d', $row['ngayhethan'])->format('d/m/Y'));
			} else {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, '');
			}
			$i++;
		}		
		//Set width column
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(25);
		$sheet->getColumnDimension('C')->setWidth(25);	
		$sheet->getColumnDimension('D')->setWidth(20);	
		$sheet->getColumnDimension('E')->setWidth(15);	
		$sheet->getColumnDimension('F')->setWidth(15);	
		$sheet->getColumnDimension('G')->setWidth(15);	
		$sheet->getColumnDimension('H')->setWidth(25);	
		$sheet->getColumnDimension('I')->setWidth(15);	
		$sheet->getColumnDimension('J')->setWidth(15);	
		//Create border
		$styleArray = array(
		    'borders' => array(
		        'allborders' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN
		        )
	      	)
	  	);
		$objPHPExcel->getActiveSheet()->getStyle('A3:J'.($i-1))->applyFromArray($styleArray);
		//Set background for header
		$objPHPExcel->getActiveSheet()->getStyle('A3:J3')->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFE8E5E5');
		unset($styleArray); 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
    header('Cache-Control: max-age=0'); //no cache                
    //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
    //if you want to save it as .XLSX Excel 2007 format 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
    //force user to download the Excel file without writing it to server's HD
    $objWriter->save('php://output');
	}

	public function filterContract_post() {
		$state = $this->post('state');
		$this->load->model('user_model');
		$data = $this->user_model->selectHD('nhanvien.hoten, nhanvien.mnv, hopdong.id, hopdong.mahd, hopdong.loaihd, hopdong.thoihan, hopdong.ngayhieuluc, hopdong.ngayhethan, hopdong.nguoidd, hopdong.luongcb');
		foreach ($data as $key => $value) {
			$this->db->where('id', $value['nguoidd']);
        	$query=$this->db->get('nhanvien');
			$data[$key]['nguoidd'] = $query->row_array()['hoten'];
			if(strtotime($value['ngayhethan']) - strtotime(date('Y-m-d', time())) > 0 || $value['loaihd'] == 'Hợp đồng không thời hạn') {
				$data[$key]['trangthai'] = 'Còn hiệu lực';
				if ($state == 'Hết hiệu lực') {
					unset($data[$key]);
				}
			} else {
				$data[$key]['trangthai'] = 'Hết hiệu lực';
				if ($state == 'Còn hiệu lực') {
					unset($data[$key]);
				}
			}
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function userHetHD_get()
	{
		$this->load->model('user_model');
		$userHetHD = $this->user_model->countUserHetHD();
		$this->set_response($userHetHD, REST_Controller::HTTP_OK);
	}

	public function userCoSinhNhat_get()
	{
		$this->load->model('base_model');
		$snTrongThang = $this->base_model->countValue('nhanvien', 'month(ngaysinh)', date('m'));
		$this->set_response($snTrongThang, REST_Controller::HTTP_OK);
	}

	public function listUserCoSN_get()
	{
		$this->load->model('base_model');
		$arrUsers = $this->base_model->getAllByField('nhanvien', 'month(ngaysinh)', date('m'));
		$this->set_response($arrUsers, REST_Controller::HTTP_OK);
	}

	public function loadContract_get() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		if (!$contract_data = $this->cache->get('contract')) {
			$this->load->model('user_model');
			$contract_data = $this->user_model->selectHD('nhanvien.hoten, nhanvien.mnv, hopdong.id, hopdong.mahd, hopdong.loaihd, hopdong.thoihan, hopdong.ngayhieuluc, hopdong.ngayhethan, hopdong.nguoidd, hopdong.luongcb');
			foreach ($contract_data as $key => $value) {
				$this->db->where('id', $value['nguoidd']);
	        	$query=$this->db->get('nhanvien');
				$contract_data[$key]['nguoidd'] = $query->row_array()['hoten'];
				if(strtotime($value['ngayhethan']) - strtotime(date('Y-m-d', time())) > 0 || $value['loaihd'] == 'Hợp đồng không thời hạn') {
					$contract_data[$key]['trangthai'] = 'Còn hiệu lực';
				} else {
					$contract_data[$key]['trangthai'] = 'Hết hiệu lực';
				}
			}
			$this->cache->save('contract', $contract_data, 600);
		}
		$this->set_response($contract_data, REST_Controller::HTTP_OK);
	}

	public function capnhatHD_post() {
		$postData = $this->post('postData');
		$this->load->model('base_model');
		$this->base_model->updateTable('congtac', 'id', $postData['id'], $postData);
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã cập nhật thành công'
		], REST_Controller::HTTP_OK);
	}

	public function getHoatdong_get() {
		$id = $this->get('id');
		$this->load->model('base_model');
		$data = $this->base_model->getOneByField('congtac', 'id', $id);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function themHoatdong_post() {
		$postData = $this->post('postData');
		$this->load->model('base_model');
		$this->base_model->insertTable('congtac', $postData);
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã thêm thành công'
		], REST_Controller::HTTP_OK);
	}

	public function xuatCongtac_post() {
		$manv = $this->post('manv');
		$hoten = $this->post('hoten');
		$data['title'] = $hoten;
		$data['manv'] = $manv;
		$this->load->model('base_model');
		$user = $this->base_model->getOneByField('nhanvien', 'mnv', $data['manv']);
		$chucvu = $this->base_model->getOneByField('chucvu', 'ID', $user['chucvu'])['tenchucvu'];
		$data['chucvu'] = $chucvu;
		$data['hinhanh'] = $user['hinhanh'];
		$this->load->model('user_model');
		$data['content'] = $this->user_model->getCongtac($manv);
		$this->load->library('pdf');
		$this->pdf->load_view('template_pdf/congtac.html', $data);
		$this->pdf->set_paper('a4', 'portrait');
		$this->pdf->render();
		// $this->pdf->stream("congtac.pdf");
		$data = $this->pdf->output();
		echo ($data);
	}

	public function showCongtac_get() {
		$id = $this->get('manv');
		$this->load->model('user_model');
		$data = $this->user_model->getCongtac($id);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function capnhatQD_post() {
		$postData = $this->post('postData');
		$this->load->model('base_model');
		$this->base_model->updateTable('ktkl', 'maqd', $postData['maqd'], $postData);
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã cập nhật thành công'
		], REST_Controller::HTTP_OK);
	}

	public function themQuyetdinh_post() {
		$postData = $this->post('postData');
		$this->load->model('base_model');
		if($this->base_model->insertTable('ktkl', $postData, 'maqd')) {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm thành công'
			], REST_Controller::HTTP_OK);
		} else {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Mã quyết định đã tồn tại'
			], REST_Controller::HTTP_CONFLICT);
		}
	}

	public function xuatQuyetdinh_post() {
		$postData = $this->post('postData');
		if ($postData['type'] == 'all') {
			$data['subject'] = 'BẢNG THEO DÕI KHEN THƯỞNG - KỶ LUẬT';
		} else if ($postData['type'] == 'Khen thưởng') {
			$data['subject'] = 'BẢNG THEO DÕI KHEN THƯỞNG';
		} else {
			$data['subject'] = 'BẢNG THEO DÕI KỶ LUẬT';
		}
		$data['title'] = $postData['hoten'];
		$data['manv'] = $postData['manv'];
		$this->load->model('base_model');
		$user = $this->base_model->getOneByField('nhanvien', 'mnv', $data['manv']);
		$chucvu = $this->base_model->getOneByField('chucvu', 'ID', $user['chucvu'])['tenchucvu'];
		$data['chucvu'] = $chucvu;
		$data['hinhanh'] = $user['hinhanh'];
		$this->load->model('user_model');
		$data['content'] = $this->user_model->getQuyetdinh($postData['type'], $postData['manv']);
		$this->load->library('pdf');
		$this->pdf->load_view('template_pdf/ktkl.html', $data);
		$this->pdf->set_paper('a4', 'portrait');
		$this->pdf->render();
		// $this->pdf->stream("quyetdinh.pdf");
		$data = $this->pdf->output();
		echo ($data);
	}

	public function getQuyetdinh_get() {
		$id = $this->get('id');
		$this->load->model('base_model');
		$data = $this->base_model->getOneByField('ktkl', 'id', $id);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function showQuyetdinh_get() {
		$type = $this->get('type');
		$id = $this->get('manv');
		$this->load->model('user_model');
		$data = $this->user_model->getQuyetdinh($type, $id);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function selectUsers_get() {
		$this->load->model('user_model');
		$data = $this->user_model->selectAllUser();
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function filtersData_post() {
		$arrWhere = $this->post('whereClauses');
		$this->load->model('user_model');
		$data = $this->user_model->filterUsers($arrWhere);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function countNghiPhep_get()
	{
		$this->load->model('base_model');
		$countNghiPhep = $this->base_model->countValue('nghiphep', 'trangthai', 'Chờ xét duyệt');
		$this->set_response($countNghiPhep, REST_Controller::HTTP_OK);
	}

	public function themNghiPhep_post() {
		$data = $this->post('info');
		$data['trangthai'] = 'Chờ xét duyệt';
		$data['ngaytao'] = date('Y-m-d', time());
		$this->load->model('base_model');
		if ($this->base_model->insertTable('nghiphep', $data)) {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã đăng ký thành công'
			], REST_Controller::HTTP_OK);
		}
		else {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Đăng ký thất bại'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function loadNghiPhep_get() {
		$this->db->where('trangthai', 'Chờ xét duyệt');
		$this->db->order_by('ngaytao', 'asc');
		$query = $this->db->get('nghiphep');
		$this->set_response($query->result_array(), REST_Controller::HTTP_OK);
	}

	public function changeNghiPhep_put() {
		$data['trangthai'] = $this->put('trangthai');
		$maphep = $this->put('maphep');
		$manv = $this->put('manv');
		$data['ngaysua'] = date('Y-m-d', time());
		if ($data['trangthai'] == 'delete') {
			$data['trangthai'] = 'Đã bị hủy';
			$this->db->where('maphep', $maphep);
			$this->db->delete('nghiphep');
		}
		else {	
			$this->load->model('base_model');
			$this->base_model->updateTable('nghiphep', 'maphep', $maphep, $data);
		}
		
		//Gửi mail thông báo cho NV
		$this->db->where('username', $manv);
		$this->db->select('email');
		$query = $this->db->get('taikhoan');
		$email = $query->row_array()['email'];
		$subject = 'Thông báo về việc xin nghỉ phép';
		$content = "<p>--------ĐƠN XIN NGHỈ PHÉP--------</p><p>Mã nhân viên: " . $manv . "</p><p>Tình trạng: " . $data['trangthai'] . "</p>";
		$this->sendMail( NULL, NULL, $email, $subject, $content);
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã cập nhật thành công'
		], REST_Controller::HTTP_OK);
	}

	public function countUserByCV_get() {
		$macv = $this->get('macv');
		$this->db->where('chucvu', $macv);
		$this->db->order_by('mnv', 'desc');
		$this->db->limit(1);
		$this->db->select('mnv');
		$query = $this->db->get('nhanvien');
		$result = $query->row_array();
		$tmp = intval(substr($result['mnv'], 4)) + 1;
		switch ($tmp) {
			case ($tmp < 10):
				$tmp = '000'.$tmp;
				break;
			case ($tmp < 100):
				$tmp = '00'.$tmp;
				break;
			case ($tmp < 1000):
				$tmp = '0'.$tmp;
				break;
		}
		$data['number'] = $tmp;
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function userOnline_get() {
		$this->load->model('base_model');
		$userOnline = $this->base_model->countValue('taikhoan', 'online', 1);
		$this->set_response($userOnline, REST_Controller::HTTP_OK);
	}

	public function showUsers_get() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		if (!$user_data = $this->cache->get('user')) {
			$this->load->model('user_model');
			$user_data =  $this->user_model->getAllUsers();
			$this->cache->save('user', $user_data, 600);
		}
		$this->set_response($user_data, REST_Controller::HTTP_OK);
	}

	public function showUser_get() {
		$this->load->model('user_model');
		$this->load->model('base_model');
		$user_id = $this->get('user_id');
		if (isset($user_id)) {
			$id = $user_id;
			$data = $this->user_model->getUserById($id);
			$phucap = $this->base_model->getAllByField('phucap', 'macd', $data['macd']);
			$listPhucap = '';
			foreach ($phucap as $key => $value) {
				$listPhucap .= $value['mota'] . '; ';
			}
			$data['phucap'] = rtrim($listPhucap, "; ");
			$this->db->where('id', $data['hopdong']);
			$this->db->select('id, loaihd, thoihan, ngayhieuluc, ngayhethan, nguoidd, luongcb');
			$query = $this->db->get('hopdong');
			$data['data_hd'] = $query->row_array();
		}
		else {
			$id = $this->user_authed->user_id;
			$data = $this->user_model->getUserByAccount($id);
		}		

		$data['chuyenmon'] = $this->base_model->getOneByField('chuyenmon', 'ID', $data['chuyenmon'])['tenchuyenmon'];
		$data['tencv'] = $this->base_model->getOneByField('chucvu', 'ID', $data['chucvu'])['tenchucvu'];
		$data['tenpb'] = $this->base_model->getOneByField('phongban', 'mpb', $data['phongban'])['tenpb'];
		$data['tencd'] = $this->base_model->getOneByField('chucdanh', 'mcd', $data['macd'])['tencd'];
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function updateUser_post() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->delete('user');
		$this->cache->delete('contract');
		$data = $this->post('info');
		//upload avatar
		$config['upload_path'] = './assets/kcfinder/upload/files/avatars/';
		$config['allowed_types'] = 'jpg|png|bmp';
		$config['overwrite'] = TRUE;
		$this->load->library('upload', $config);
		if ( $this->upload->do_upload('image'))
		{
			$data['hinhanh'] = $this->upload->data()['file_name'];
		}
		$data['ngaysua'] = date('Y-m-d', time());
		$this->load->model('account_model');
		$this->account_model->updateUser('id', $data['taikhoan'], array('email'=>$data['email'], 'username'=>$data['mnv']));

		$this->load->model('user_model');		
		if ($this->user_model->updateUser($data)) {
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã cập nhật thành công'
			], REST_Controller::HTTP_OK);
		}
		else {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Cập nhật thất bại'
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function addUser_post() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->delete('user');
		$this->cache->delete('contract');
		$data = $this->post('info');
		$data['hoten'] = ucwords($data['hoten']);
		$data['noisinh'] = ucwords($data['noisinh']);
		$data['quoctich'] = ucwords($data['quoctich']);
		$data['dantoc'] = ucwords($data['dantoc']);
		$data['tongiao'] = ucwords($data['tongiao']);
		$data['noicap'] = ucwords($data['noicap']);
		$this->load->model('user_model');
		//upload avatar
		$config['upload_path'] = './assets/kcfinder/upload/files/avatars/';
		$config['allowed_types'] = 'jpg|png|bmp';
		$config['overwrite'] = TRUE;
		$this->load->library('upload', $config);
		if ( !$this->upload->do_upload('image'))
		{
			$data['hinhanh'] = 'avatar.png';
		}
		else {
			$data['hinhanh'] = $this->upload->data()['file_name'];
		}
		$data['ngaytao'] = date('Y-m-d', time());
		$data['ngaysua'] = date('Y-m-d', time());
		if (($result = $this->user_model->addUser($data)) != FALSE) {
			//Gửi mail thông báo
			$this->send_account($data['email'], $data['mnv'], $result['password']);
			$this->set_response([
				'status'=>TRUE,
				'message'=>'Đã thêm thành công'
			], REST_Controller::HTTP_OK);
		}
		else {
			$this->set_response([
				'status'=>FALSE,
				'message'=>'Nhân viên đã tồn tại'
			], REST_Controller::HTTP_CONFLICT);
		}
	}

	public function removeUser_get() {
		$manv = $this->get('manv');
		$this->load->model('base_model');
		$user = $this->base_model->getOneByField('nhanvien', 'mnv', $manv);
		$this->base_model->deleteTable('nhanvien', 'mnv', $manv);
		$this->base_model->deleteTable('taikhoan', 'id', $user['taikhoan']);
		$this->base_model->deleteTable('hopdong', 'id', $user['hopdong']);
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->delete('user');
		$this->cache->delete('contract');
		$this->cache->delete('account');
		$this->set_response([
			'status'=>TRUE,
			'message'=>'Đã xóa thành công'
		], REST_Controller::HTTP_OK);
	}

	public function changepassword_put() {
		$oldPass = $this->put('old_password');
		$newPass = $this->put('new_password');

		$this->load->model('account_model');
		if (($user_info = $this->account_model->checkUser($this->user_authed->user_id)) != FALSE) {
			if ($user_info['password'] != $oldPass) {
				$this->set_response([
	            	'status' => FALSE,
	            	'message' => 'Mật khẩu hiện tại không chính xác!'
				], REST_Controller::HTTP_NOT_FOUND);
			}
			else {
				$this->account_model->updateUser('id', $this->user_authed->user_id, array('password'=>$newPass));
				$this->set_response([
	            	'status' => TRUE,
	            	'message' => 'Mật khẩu đã được thay đổi thành công!'
				], REST_Controller::HTTP_OK);
			}
		}
	}

	public function doforget_post()
	{
		$username = $this->post('username');
		$email = $this->post('email');
   		$q = $this->db->query("select * from taikhoan where username='" . $username . "'");
        if ($q->num_rows() > 0) {
            $user = $q->row_array();
            if($user['email'] == $email) {
				$this->resetpassword($user);
				$this->set_response([
	            	'status' => TRUE,
	            	'message' => "Một mật khẩu mới đã được gửi tới địa chỉ email: ". $email
				], REST_Controller::HTTP_OK);
			}
			else {
				$this->set_response([
		        	'status' => FALSE,
		        	'message' => 'Địa chỉ '.$email.' không trùng khớp'
				], REST_Controller::HTTP_NOT_FOUND);
			}
        }
        else {
			$this->set_response([
	        	'status' => FALSE,
	        	'message' => 'Không tìm thấy tên truy cập '.$username 
			], REST_Controller::HTTP_NOT_FOUND);
		}
	} 

	private function resetpassword($user)
	{
		$this->load->helper('string');
		$data['password']= random_string('alnum', 6);
		$this->db->where('id', $user['id']);
		$this->db->update('taikhoan',array('password'=>hash('sha512', $data['password'], false)));
		$data['username'] = $user['username'];
		$body = $this->load->view('emails/resetpassword.php', $data, TRUE);
		$this->sendMail('tantd@qlns.890m.com', 'Administrator', $user['email'], 'Thông tin reset password', $body);
	}

	public function importExl_post() {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$this->cache->delete('user');
		$this->cache->delete('contract');
		$config['upload_path'] = './assets/kcfinder/upload/files/documents/';
		$config['allowed_types'] = "xls|xlsx";
		$config['overwrite'] = TRUE;

		$this->load->library('upload', $config);
		if(!$this->upload->do_upload('file')) {
			$this->set_response([
				'status'=> FALSE,
				'message'=> $this->upload->display_errors()
			], REST_Controller::HTTP_BAD_REQUEST);
		}
		else {
			//load the excel library
			$this->load->library('PHPExcel');
			//read file from path
			$objPHPExcel = PHPExcel_IOFactory::load('./assets/kcfinder/upload/files/documents/'.$this->upload->data()['file_name']);
			//get only the Cell Collection
			$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
			//extract to a PHP readable array format
			foreach ($cell_collection as $cell) {
			    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
			    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
			    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
			    //header will/should be in row 1 only. of course this can be modified to suit your need.
			   	if (is_float($data_value) && $column != 28) {
			   		$data_value = date('m/d/Y',PHPExcel_Shared_Date::ExcelToPHP($data_value));
			   	}
			   	if ($row == 1) {
			        $header[$row][$column] = $data_value;
			    } else {
			        $arr_data[$row][$column] = $data_value;
			    }


			}

			//import data to table	
			$fields = $this->db->list_fields('nhanvien');
			unset($fields[0]);
			$phongban = $this->db->select('mpb, tenpb')->get('phongban')->result_array();
			$chucvu = $this->db->select('ID, tenchucvu')->get('chucvu')->result_array();
			$chucdanh = $this->db->select('mcd, tencd')->get('chucdanh')->result_array();
			$chuyenmon = $this->db->select('ID, tenchuyenmon')->get('chuyenmon')->result_array();
			$this->load->model('account_model');
			$this->load->model('user_model');
			$this->load->model('base_model');

			$sql = "INSERT IGNORE INTO `nhanvien` (";
			foreach ($fields as $field) {
				$sql .= $field . ",";
			}
			$sql = rtrim($sql, ",") . ") VALUES";
			foreach ($arr_data as $key => $rows) {
				//add new account
				$this->load->helper('string');
				$password= random_string('alnum', 6);
				$arrAccount = array(
					'id' => '',
					'username' => $rows['A'],
					'password' => hash('sha512', $password, false),
					'email' => $rows['H'],
					'level' => '5',
					'active' => '1',
					'ngaytao' => date('Y-m-d', time()),
					'ngaysua' => date('Y-m-d', time())
				);
		
				$id = $this->account_model->addUser($arrAccount);
				//Send new password to member by email
				if ($arrAccount['email'] && $arrAccount['email'] !== '') {
					// $this->send_account($arrAccount['email'], $arrAccount['username'], $password);
				}

				//add new contract
				switch ($rows['P']) {
					case 'Hợp đồng thử việc':
						$hopdong['mahd'] = 'HDTV/'.date("Y").'/'.$rows['A'];
						break;
					case 'Hợp đồng học việc':
						$hopdong['mahd'] = 'HDHV/'.date("Y").'/'.$rows['A'];
						break;
					case 'Hợp đồng có thời hạn':
						$hopdong['mahd'] = 'HDTH/'.date("Y").'/'.$rows['A'];
						break;
					case 'Hợp đồng không thời hạn':
						$hopdong['mahd'] = 'HDKH/'.date("Y").'/'.$rows['A'];
						break;
				}
				$hopdong['loaihd'] = $rows['P'];
				$hopdong['thoihan'] = $rows['Q'];
				$hopdong['luongcb'] = $rows['AF'];
				if ($rows['R']!='') {
					$hopdong['ngayhieuluc'] = DateTime::createFromFormat('d/m/Y', $rows['R'])->format('Y-m-d');
				} else {
					$hopdong['ngayhieuluc'] = NULL;
				}
				if ($rows['S']!='') {
					$hopdong['ngayhethan'] = DateTime::createFromFormat('d/m/Y', $rows['S'])->format('Y-m-d');
				} else {
					$hopdong['ngayhethan'] = NULL;
				}
				$hopdong['ngaytao'] = $hopdong['ngayhieuluc'];
				/*$this->db->insert('hopdong', $hopdong);
				$id_hd = $this->db->insert_id();*/
				$id_hd = $this->user_model->insertHD($hopdong);
				unset($rows['P']);
				unset($rows['Q']);
				unset($rows['R']);
				unset($rows['S']);

				//add new member
				$sql .= "(";
				foreach ($rows as $k => $v) {
					if ($k == "D" || $k == "O" || $k == "AC") {
						$sql .= "'" . DateTime::createFromFormat('d/m/Y', $v)->format('Y-m-d') . "',";
					}
					else if ($k == "K") {
						$i=0;
						for ($i; $i < count($chucdanh); $i++) { 
							if ($v == $chucdanh[$i]['tencd']) {
								$sql .= "'" . $chucdanh[$i]['mcd'] . "',";
								break;
							}
						}
						if($i == count($chucdanh)) {
							do {
								$data = array (
									'mcd'		=> strtoupper(random_string('alnum', 2)),
									'tencd'		=> $v,
									'ngaytao' 	=> date('Y-m-d', time()),
									'ngaysua' 	=> date('Y-m-d', time())
								);
								$check = $this->base_model->insertTable('chucdanh', $data, 'mcd');
							} while($check == FALSE);
							$sql .= "'" . $v . "',";
							$chucdanh = $this->db->select('mcd, tencd')->get('chucdanh')->result_array();
						}
					}
					else if ($k == "L") {
						$i=0;
						for ($i; $i < count($chucvu); $i++) { 
							if ($v == $chucvu[$i]['tenchucvu']) {
								$sql .= "'" . $chucvu[$i]['ID'] . "',";
								break;
							}	
						}
						if($i == count($chucvu)) {
							do {
								$data = array (
									'ID'		=> strtoupper(random_string('alnum', 4)),
									'tenchucvu'		=> $v,
									'ngaytao' 	=> date('Y-m-d', time()),
									'ngaysua' 	=> date('Y-m-d', time())
								);
								$check = $this->base_model->insertTable('chucvu', $data, 'ID');
							} while($check == FALSE);
							$sql .= "'" . $v . "',";
							$chucvu = $this->db->select('ID, tenchucvu')->get('chucvu')->result_array();
						}
					}
					else if ($k == "M") {
						$i=0;
						for ($i; $i < count($phongban); $i++) { 
							if ($v == $phongban[$i]['tenpb']) {
								$sql .= "'" . $phongban[$i]['mpb'] . "',";
								break;
							}
						}
						if($i == count($phongban)) {
							do {
								$data = array (
									'mpb'		=> strtoupper(random_string('alnum', 2)),
									'tenpb'		=> $v,
									'truongpb'	=> '',
									'ngaytao' 	=> date('Y-m-d', time()),
									'ngaysua' 	=> date('Y-m-d', time())
								);
								$check = $this->base_model->insertTable('phongban', $data, 'mpb');
							} while($check == FALSE);
							$sql .= "'" . $v . "',";
							$phongban = $this->db->select('mpb, tenpb')->get('phongban')->result_array();
						}
					}
					else if ($k == "Z") {
						$i=0;
						for ($i; $i < count($chuyenmon); $i++) { 
							if ($v == $chuyenmon[$i]['tenchuyenmon']) {
								$sql .= "'" . $chuyenmon[$i]['ID'] . "',";
								break;
							}
						}
						if($i == count($chuyenmon)) {
							do {
								$data = array (
									'ID'		=> strtoupper(random_string('alnum', 4)),
									'tenchuyenmon'		=> $v,
									'ngaytao' 	=> date('Y-m-d', time()),
									'ngaysua' 	=> date('Y-m-d', time())
								);
								$check = $this->base_model->insertTable('chuyenmon', $data, 'ID');
							} while($check == FALSE);
							$sql .= "'" . $v . "',";
							$chuyenmon = $this->db->select('ID, tenchuyenmon')->get('chuyenmon')->result_array();
						}
					}
					else {
						$sql .= "'" . $v . "',";
					}
				}
				$sql .= "DEFAULT,".$id.",".$id_hd.",'".date('Y-m-d', time())."','".date('Y-m-d', time())."'),";
			}
			$sql = rtrim($sql, ",");

			if($this->db->query($sql))
			{
				$this->set_response([
	            	'status' => TRUE,
	            	'message' => 'Đã thêm thành công!'
				], REST_Controller::HTTP_OK);
			}
			else {
				$this->set_response([
					'status'=> FALSE,
					'message'=> 'Import không thành công'
				], REST_Controller::HTTP_OK);
			}
		}
	}

	public function send_account($email, $username, $password)
	{
		$fromEmail = 'tantd@qlns.890m.com';
		$subject = 'Thông tin tài khoản cá nhân';
		$content = '<p>Đây là thông tin tài khoản cá nhân của bạn để truy cập vào hệ thống.</p><p>Username: '. $username .'</p><p>Password: '. $password .'</p>';
		$this->sendMail($fromEmail,NULL,$email,$subject,$content);
	}

	public function exportExl_post() {
		// load model
    $this->load->model('user_model');
    // get users in array formate
    $arrWhere = $this->post('whereClauses');
		$users = $this->user_model->filterUsers($arrWhere);
		//load our new PHPExcel library
    $this->load->library('PHPExcel');
    // Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
 		// Add header
 		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0, 1, "BẢNG DANH SÁCH THÔNG TIN NHÂN VIÊN");
		$sheet->mergeCells('A1:AE1');
		$sheet->getStyle('A1')->getAlignment()->applyFromArray(
		    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
		$sheet->getStyle("A1:AE1")->getFont()->setSize(16);
		$sheet->getStyle("A1:AE1")->getFont()->setBold(true);
        //activate worksheet number 1
        $objPHPExcel->setActiveSheetIndex(0)
        //tao du lieu cho tieu de
		->setCellValue('A3', 'STT')
		->setCellValue('B3', 'Mã nhân viên')
		->setCellValue('C3', 'Họ và tên')
		->setCellValue('D3', 'Giới tính')
		->setCellValue('E3', 'Ngày sinh')
		->setCellValue('F3', 'Nơi sinh')
		->setCellValue('G3', 'DT di động')
		->setCellValue('H3', 'DT cố định')
		->setCellValue('I3', 'Email')
		->setCellValue('J3', 'Địa chỉ')
		->setCellValue('K3', 'Tạm trú')
		->setCellValue('L3', 'Chức danh')
		->setCellValue('M3', 'Chức vụ')
		->setCellValue('N3', 'Phòng ban')
		->setCellValue('O3', 'Trạng thái')
		->setCellValue('P3', 'Ngày vào làm')
		->setCellValue('Q3', 'Loại hợp đồng')
		->setCellValue('R3', 'Thời hạn HĐ')
		->setCellValue('S3', 'Lương căn bản')
		->setCellValue('T3', 'Số người PT')
		->setCellValue('U3', 'Quốc tịch')
		->setCellValue('V3', 'Dân tộc')
		->setCellValue('W3', 'Tôn giáo')
		->setCellValue('X3', 'Trình độ')
		->setCellValue('Y3', 'Ngoại ngữ')
		->setCellValue('Z3', 'Tin học')
		->setCellValue('AA3', 'Chuyên môn')
		->setCellValue('AB3', 'Tình trạng hôn nhân')
		->setCellValue('AC3', 'CMND')
		->setCellValue('AD3', 'Ngày cấp')
		->setCellValue('AE3', 'Nơi cấp')
		->setCellValue('AF3', 'Ghi chú');
		$i = 4;
		foreach ($users as $row)
		{
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $i-3)
			->setCellValue('B'.$i, $row['mnv'])
			->setCellValue('C'.$i, $row['hoten'])
			->setCellValue('D'.$i, $row['gioitinh'])
			->setCellValue('E'.$i, DateTime::createFromFormat('Y-m-d', $row['ngaysinh'])->format('d/m/Y'))
			->setCellValue('F'.$i, $row['noisinh'])
			->setCellValue('G'.$i, $row['dtdd'])
			->setCellValue('H'.$i, $row['dtcd'])
			->setCellValue('I'.$i, $row['email'])
			->setCellValue('J'.$i, $row['thuongtru'])
			->setCellValue('K'.$i, $row['tamtru'])
			->setCellValue('L'.$i, $row['tencd'])
			->setCellValue('M'.$i, $row['tenchucvu'])
			->setCellValue('N'.$i, $row['tenpb'])
			->setCellValue('O'.$i, $row['trangthai'])
			->setCellValue('P'.$i, DateTime::createFromFormat('Y-m-d', $row['ngayvaolam'])->format('d/m/Y'))
			->setCellValue('Q'.$i, $row['loaihd'])
			->setCellValue('R'.$i, $row['thoihan'])
			->setCellValue('S'.$i, $row['luongcb'])
			->setCellValue('T'.$i, $row['songuoi_pt'])
			->setCellValue('U'.$i, $row['quoctich'])
			->setCellValue('V'.$i, $row['dantoc'])
			->setCellValue('W'.$i, $row['tongiao'])
			->setCellValue('X'.$i, $row['trinhdo'])
			->setCellValue('Y'.$i, $row['ngoaingu'])
			->setCellValue('Z'.$i, $row['tinhoc'])
			->setCellValue('AA'.$i, $row['tenchuyenmon'])
			->setCellValue('AB'.$i, $row['tthonnhan'])
			->setCellValue('AC'.$i, $row['cmnd'])
			->setCellValue('AD'.$i, DateTime::createFromFormat('Y-m-d', $row['ngaycap'])->format('d/m/Y'))
			->setCellValue('AE'.$i, $row['noicap'])
			->setCellValue('AF'.$i, $row['ghichu']);
			/*if ($row['ngayhethan']!='') {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$i, DateTime::createFromFormat('Y-m-d', $row['ngayhethan'])->format('d/m/Y'));
			} else {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$i, '');
			}*/
			$i++;
		}		
		//Set width column
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(5);	
		$sheet->getColumnDimension('E')->setWidth(10);	
		$sheet->getColumnDimension('F')->setWidth(25);	
		$sheet->getColumnDimension('G')->setWidth(15);	
		$sheet->getColumnDimension('H')->setWidth(15);	
		$sheet->getColumnDimension('I')->setWidth(25);	
		$sheet->getColumnDimension('J')->setWidth(25);	
		$sheet->getColumnDimension('K')->setWidth(25);
		$sheet->getColumnDimension('L')->setWidth(10);
		$sheet->getColumnDimension('M')->setWidth(15);
		$sheet->getColumnDimension('N')->setWidth(10);
		$sheet->getColumnDimension('O')->setWidth(12);
		$sheet->getColumnDimension('P')->setWidth(12);
		$sheet->getColumnDimension('Q')->setWidth(18);
		$sheet->getColumnDimension('R')->setWidth(10);
		$sheet->getColumnDimension('S')->setWidth(10);
		$sheet->getColumnDimension('T')->setWidth(10);
		$sheet->getColumnDimension('U')->setWidth(10);
		$sheet->getColumnDimension('V')->setWidth(6);
		$sheet->getColumnDimension('W')->setWidth(10);
		$sheet->getColumnDimension('X')->setWidth(10);
		$sheet->getColumnDimension('Y')->setWidth(10);
		$sheet->getColumnDimension('Z')->setWidth(10);
		$sheet->getColumnDimension('AA')->setWidth(15);
		$sheet->getColumnDimension('AB')->setWidth(15);
		$sheet->getColumnDimension('AC')->setWidth(15);
		$sheet->getColumnDimension('AD')->setWidth(15);
		$sheet->getColumnDimension('AE')->setWidth(15);
		$sheet->getColumnDimension('AF')->setWidth(20);
		//Create border
		$styleArray = array(
		    'borders' => array(
		        'allborders' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN
		        )
	      	)
	  	);
		$objPHPExcel->getActiveSheet()->getStyle('A3:AF'.($i-1))->applyFromArray($styleArray);
		//Set background for header
		$objPHPExcel->getActiveSheet()->getStyle('A3:AF3')->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFE8E5E5');
		unset($styleArray); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Cache-Control: max-age=0'); //no cache                    
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');

	}
}