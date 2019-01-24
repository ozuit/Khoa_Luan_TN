<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/

class Info_model extends CI_Model
{
	public function __construct() {
		parent::__construct();
	}

	public function a_fGetAnnounces($perpage, $offset, $userID, $keyword=null) { 
		if ($keyword == null) $keyword = "";
		$sqlQuery = "SELECT `thongbao`.`id`, `tieude`, `ngaytao`, `mota`, `nguoitao`, `isRead` FROM `thongbao` JOIN `user_announce` ON `thongbao`.`id` = `user_announce`.`announce_id` WHERE `user_id` = ".$userID." AND (`thongbao`.`tieude` LIKE '%".$keyword."%' ESCAPE '!' OR `thongbao`.`noidung` LIKE '%".htmlentities($keyword)."%' ESCAPE '!') ORDER BY `ngaytao` DESC LIMIT ".$offset.", ".$perpage;
		$result = $this->db->query($sqlQuery);
		return $result->result_array();
	} 

	public function i_fGetTotalAnnouces($userID, $keyword=null) {
		if ($keyword == null) $keyword = "";
		$sqlQuery = "SELECT `thongbao`.`id` FROM `thongbao` JOIN `user_announce` ON `thongbao`.`id` = `user_announce`.`announce_id` WHERE  (`tieude` LIKE '%".$keyword."%' ESCAPE '!' OR `noidung` LIKE '%".htmlentities($keyword)."%' ESCAPE '!') AND `user_id` = " . $userID;
		$result = $this->db->query($sqlQuery);
		return $result->num_rows();
	}

	public function loadThongBao($userID) {
		$data = $this->db->select('thongbao.id, tieude, ngaytao, mota, nguoitao, isRead')
						->from('thongbao')
						->join('user_announce', 'thongbao.id = user_announce.announce_id')
						->where('user_id', $userID)
						->limit(5)
						->order_by('ngaytao', 'desc')
						->get()
						->result_array();
		return $data;
	}

	public function setReadNotify($userID, $notifyID) {
		$this->db->where('user_id', $userID);
		$this->db->where('announce_id', $notifyID);
		$this->db->update('user_announce', array('isRead' => 1));
	}

	public function getReadNotify($userID, $notifyID) {
		$this->db->where('user_id', $userID);
		$this->db->where('announce_id', $notifyID);
		$this->db->where('isRead', 1);
		$query = $this->db->get('user_announce');
		if($query->num_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	public function tk_chuc_danh() {
		$this->db->select('macd, COUNT(macd) as soluong, tencd');
		$this->db->from('nhanvien');
		$this->db->group_by('macd');
		$this->db->join('chucdanh', 'nhanvien.macd = chucdanh.mcd');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function tk_ton_giao() {
		$this->db->select('tongiao, COUNT(tongiao) as soluong');
		$this->db->from('nhanvien');
		$this->db->group_by('tongiao');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function tk_dan_toc() {
		$this->db->select('dantoc, COUNT(dantoc) as soluong');
		$this->db->from('nhanvien');
		$this->db->group_by('dantoc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get5years() {
		$sql = 'SELECT EXTRACT(YEAR from ngayvaolam) AS nam ';
		$sql .='FROM `nhanvien` ';
		$sql .='GROUP BY EXTRACT(YEAR from ngayvaolam) ';
		$sql .='ORDER BY nam DESC ';
		$sql .='LIMIT 5';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function tk_trinh_do($year) {
		$sql = 'SELECT trinhdo, COUNT(trinhdo) as soluong ';
		$sql .='FROM `nhanvien` ';
		$sql .='WHERE EXTRACT(YEAR from ngayvaolam) = '.$year;
		$sql .=' GROUP BY trinhdo';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function pie_trinhdo() {
		$this->db->select('trinhdo, COUNT(trinhdo) as soluong');
		$this->db->from('nhanvien');
		$this->db->group_by('trinhdo');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function tk_gioi_tinh($year) {
		$sql = 'SELECT gioitinh, COUNT(gioitinh) as soluong ';
		$sql .='FROM `nhanvien` ';
		$sql .='WHERE EXTRACT(YEAR from ngayvaolam) = '.$year;
		$sql .=' GROUP BY gioitinh';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function tk_do_tuoi($gioitinh) {
		$sql = 'SELECT EXTRACT(YEAR from ngaysinh) AS nam ';
		$sql .='FROM `nhanvien` ';
		$sql .='WHERE gioitinh = '.$gioitinh;
		$sql .=' GROUP BY EXTRACT(YEAR from ngaysinh)';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}