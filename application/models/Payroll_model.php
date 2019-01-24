<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/

class Payroll_model extends CI_Model
{
	public function __construct() {
		parent::__construct();
	}

	protected $table = 'chamcong';

	public function deletePayroll() {
		$this->db->empty_table($this->table);
		$this->db->query('ALTER TABLE '.$this->table.' AUTO_INCREMENT 1');
	}

	public function newPayroll($dayNumber, $saturday, $ttchamcong) {
		$this->db->select('mnv');
		$query = $this->db->get('nhanvien');
		$arrMaNV = $query->result_array();

		$this->db->select('congngay, lamt7');
		$query = $this->db->get('tlchamcong');
		$setupPayroll = $query->row_array();
		if ($setupPayroll['lamt7'] == 0) {
			foreach ($saturday as $key => $value) {
				$ttchamcong[$key] = $value;
			}
			$dayNumber = $dayNumber - count($saturday);
		}
		$giocong = $dayNumber*$setupPayroll['congngay'];

		$dataInsert = array();
		$dataInsert['giocong'] = $giocong; // Tổng số giờ công 1 tháng thực tế
		$dataInsert['giocong_lt'] = $giocong; // Tổng số giờ công 1 tháng lý thuyết
		$dataInsert['nghicophep'] = 0;
		$dataInsert['nghikophep'] = 0;
		$dataInsert['tongngaycong'] = $dayNumber;
		$dataInsert['ttchamcong'] = json_encode($ttchamcong);
		$dataInsert['tangca'] = 0; // Số giờ làm thêm
		$dataInsert['denmuon'] = 0; // Số giờ đi trễ
		$dataInsert['ngaytao'] = date('Y-m-d', time());
		$dataInsert['ngaysua'] = date('Y-m-d', time());
		foreach ($arrMaNV as $key => $manv) {
			$dataInsert['manv'] = $manv['mnv'];
			$this->db->insert($this->table, $dataInsert);
		}
	}

	public function getPayroll($month = NULL, $year = NULL) {
		if ($month != NULL && $year != NULL) {
			$this->db->select('dulieu');
			$this->db->where('thang', $month);
			$this->db->where('nam', $year);
			$query = $this->db->get('dlchamcong');
			return json_decode($query->row_array()['dulieu']);
		} else {
			$this->db->select('chamcong.giocong, chamcong.giocong_lt, chamcong.manv, chamcong.tangca, chamcong.denmuon, chamcong.nghicophep, chamcong.nghikophep, chamcong.tongngaycong, chamcong.ttchamcong, nhanvien.hoten');
			$this->db->from($this->table);
			$this->db->join('nhanvien', 'chamcong.manv = nhanvien.mnv');
			$query = $this->db->get();
			return $query->result_array();
		}
	}

	public function getByUserId($arrUserId) {
		$results = array();
		foreach ($arrUserId as $key => $value) {
			$this->db->select('manv, nghikophep, nghicophep, tongngaycong');
			$this->db->where('manv', $value);
			$query = $this->db->get($this->table);
			$results[] = $query->row_array();
		}
		return $results;
	}

	public function tkNgayNghi($cophep = false) {
		if ($cophep) {
			$this->db->select('nghicophep, COUNT(nghicophep) as soluong');
			$this->db->where('nghicophep >', '0');
			$this->db->from($this->table);
			$this->db->group_by('nghicophep');
			$query = $this->db->get();
		} else {
			$this->db->select('nghikophep, COUNT(nghikophep) as soluong');
			$this->db->where('nghikophep >', '0');
			$this->db->from($this->table);
			$this->db->group_by('nghikophep');
			$query = $this->db->get();
		}
		return $query->result_array();
	}

	public function getUsersAbsent($dayNumber, $cophep = false) {
		$this->db->select('nhanvien.hoten, chamcong.manv, phongban.tenpb');
		if ($cophep) {
			$this->db->where('nghicophep', $dayNumber);
		} else {
			$this->db->where('nghikophep', $dayNumber);
		}
		$this->db->from($this->table);
		$this->db->join('nhanvien', 'nhanvien.mnv = chamcong.manv');
		$this->db->join('phongban', 'nhanvien.phongban = phongban.mpb');
		$query = $this->db->get();
		return $query->result_array();
	}
}