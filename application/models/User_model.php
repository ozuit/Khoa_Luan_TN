<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/
class User_model extends CI_Model
{
	protected $_table = 'nhanvien';

	public function __construct()
	{
		parent::__construct();
	}

	public function countUserHetHD()
	{
		$this->db->where('loaihd !=', 'Hợp đồng không thời hạn');
		$this->db->where('ngayhethan <=', date('Y-m-d', time()));
		return $this->db->count_all_results('hopdong');
	}

	public function selectHD($list_field)
	{
		$this->db->select($list_field);
		$this->db->from($this->_table);
		$this->db->join('hopdong', 'hopdong.id = nhanvien.hopdong');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insertHD($data)
	{
		$this->db->where('mahd', $data['mahd']);
		$query = $this->db->get('hopdong');
		if ($query->num_rows() > 0) {
			return $query->row_array()['id'];
		}	else {
			$this->db->insert('hopdong', $data);
			return $this->db->insert_id();
		}
	}

	public function getCongtac($id)
	{
		$this->db->where('manv', $id);	
		$result = $this->db->get('congtac');
		return $result->result_array();
	}

	public function getQuyetdinh($type, $id)
	{
		if ($type == 'all') {
			$this->db->where('manv', $id);
		}
		else {
			$this->db->where(array('manv' => $id, 'loai' => $type));
		}	
		$result = $this->db->get('ktkl');
		return $result->result_array();
	}

	public function selectAllUser()
	{
		$this->db->select('hoten, mnv, nhanvien.id, online, taikhoan.id as accountID');
		$this->db->from('nhanvien');
		$this->db->join('taikhoan', 'taikhoan.id = nhanvien.taikhoan');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getAllUsers()
	{
		$this->db->select('nhanvien.*, phongban.tenpb, chucdanh.tencd, chucvu.tenchucvu, chuyenmon.tenchuyenmon, hopdong.ngayhieuluc, hopdong.ngayhethan');
		$this->db->from($this->_table);
		$this->db->join('phongban','phongban.mpb = nhanvien.phongban')
		->join('chucvu','chucvu.ID = nhanvien.chucvu')
		->join('chucdanh','chucdanh.mcd = nhanvien.macd')
		->join('hopdong','hopdong.id = nhanvien.hopdong')
		->join('chuyenmon','chuyenmon.ID = nhanvien.chuyenmon');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function filterUsers($arrWhere)
	{
		$this->db->where($arrWhere);
		$this->db->select('nhanvien.*, phongban.tenpb, chucdanh.tencd, chucvu.tenchucvu, chuyenmon.tenchuyenmon, hopdong.ngayhieuluc, hopdong.ngayhethan, hopdong.loaihd, hopdong.thoihan');
		$this->db->from($this->_table);
		$this->db->join('phongban','phongban.mpb = nhanvien.phongban')
		->join('chucvu','chucvu.ID = nhanvien.chucvu')
		->join('chucdanh','chucdanh.mcd = nhanvien.macd')
		->join('hopdong','hopdong.id = nhanvien.hopdong')
		->join('chuyenmon','chuyenmon.ID = nhanvien.chuyenmon');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getUser($total, $start){
		$this->db->limit($total, $start);
		$query=$this->db->get($this->_table);
		return $query->row_array();
	}

	public function countAll(){
		return $this->db->count_all($this->_table); 
	}

	public function getUserByAccount($id)
	{
		$this->db->where('taikhoan', $id);
		$query=$this->db->get($this->_table);
		return $query->row_array();
	}	

	public function getUserByID($mnv) 
	{
		$this->db->where('mnv', $mnv);
		$query=$this->db->get($this->_table);
		return $query->row_array();
	}

	public function addUser($data)
	{
		$this->db->where('mnv', $data['mnv']);
		$query = $this->db->get($this->_table);
		if ($query->num_rows() > 0) {
			return FALSE;
		}
		else {
        	//thêm mới một tài khoản
			$this->load->helper('string');
			$password= random_string('alnum', 6);
			$arrAccount = array(
				'id' => '',
				'username' => $data['mnv'],
				'password' => hash('sha512', $password, false),
				'email' => $data['email'],
				'level' => '5',
				'active' => '1',
				'ngaytao' => date('Y-m-d', time()),
				'ngaysua' => date('Y-m-d', time())
				);
			$this->db->insert('taikhoan', $arrAccount);
			$id = $this->db->insert_id();
			$data['taikhoan'] = $id;
			//thêm mới một hợp đồng
			switch ($data['loaihd']) {
				case 'Hợp đồng thử việc':
				$hopdong['mahd'] = 'HDTV/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng học việc':
				$hopdong['mahd'] = 'HDHV/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng có thời hạn':
				$hopdong['mahd'] = 'HDTH/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng không thời hạn':
				$hopdong['mahd'] = 'HDKH/'.date("Y").'/'.$data['mnv'];
				break;
			}
			$hopdong['loaihd'] = $data['loaihd'];
			$hopdong['thoihan'] = $data['thoihan'];
			$hopdong['ngayhieuluc'] = $data['ngayhieuluc'];
			if ($data['ngayhethan'] == '') {
				$hopdong['ngayhethan'] = NULL;
			} else {
				$hopdong['ngayhethan'] = $data['ngayhethan'];
			}
			$hopdong['nguoidd'] = $data['nguoidd'];
			$hopdong['luongcb'] = $data['luongcb'];
			$hopdong['ngaytao'] = $data['ngayhieuluc'];
			$this->db->insert('hopdong', $hopdong);
			$id_hd = $this->db->insert_id();
			unset($data['loaihd']);
			unset($data['thoihan']);
			unset($data['ngayhieuluc']);
			unset($data['ngayhethan']);
			unset($data['nguoidd']);
			$data['hopdong'] = $id_hd;
			$this->db->insert($this->_table, $data);
			$insert_id = $this->db->insert_id();
			return array('userId' => $insert_id, 'password' => $password);
		}  
	}

	public function updateUser($data) {
		if (isset($data['loaihd'])) {
			//cập nhật thông tin hợp đồng
			switch ($data['loaihd']) {
				case 'Hợp đồng thử việc':
				$hopdong['mahd'] = 'HDTV/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng học việc':
				$hopdong['mahd'] = 'HDHV/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng có thời hạn':
				$hopdong['mahd'] = 'HDTH/'.date("Y").'/'.$data['mnv'];
				break;
				case 'Hợp đồng không thời hạn':
				$hopdong['mahd'] = 'HDKH/'.date("Y").'/'.$data['mnv'];
				break;
			}
			$hopdong['loaihd'] = $data['loaihd'];
			$hopdong['thoihan'] = $data['thoihan'];
			$hopdong['ngayhieuluc'] = $data['ngayhieuluc'];
			if ($data['ngayhethan'] == '') {
				$hopdong['ngayhethan'] = NULL;
			} else {
				$hopdong['ngayhethan'] = $data['ngayhethan'];
			}
			$hopdong['nguoidd'] = $data['nguoidd'];
			$hopdong['luongcb'] = $data['luongcb'];
			$hopdong['ngaytao'] = $data['ngayhieuluc'];
			$hopdong['ngaysua'] = date('Y-m-d', time());
			$this->db->where('id', $data['idHopdong']);
			$this->db->update('hopdong', $hopdong);
			unset($data['loaihd']);
			unset($data['thoihan']);
			unset($data['ngayhieuluc']);
			unset($data['ngayhethan']);
			unset($data['nguoidd']);
			unset($data['idHopdong']);
		}

		$this->db->where('taikhoan', $data['taikhoan']);
		return $this->db->update($this->_table, $data);
	}
}