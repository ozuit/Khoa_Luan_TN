<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/

class Paycheck_model extends CI_Model
{
	public function __construct() {
		parent::__construct();
	}

	protected $table = 'bangluong';

	public function getPaycheck($month = NULL, $year = NULL) {
		if ($month != NULL && $year != NULL) {
			$this->db->select('dulieu');
			$this->db->where('thang', $month);
			$this->db->where('nam', $year);
			$query = $this->db->get('dulieuluong');
			return json_decode($query->row_array()['dulieu']);
		} else {
			$this->db->select('bangluong.*, nhanvien.hoten, nhanvien.luongcb, nhanvien.hinhanh');
			$this->db->from($this->table);
			$this->db->join('nhanvien', 'bangluong.manv = nhanvien.mnv');
			$query = $this->db->get();
			return $query->result_array();
		}
	}

	public function deletePaycheck() {
		$this->db->empty_table($this->table);
		$this->db->query('ALTER TABLE '.$this->table.' AUTO_INCREMENT 1');
	}

	private function tinh_tncn($luongchiuthue, $baohiem, $songuoi_pt) {
    $mpt = 3600000; //Mức phụ thuộc trên mỗi người
    $bac = [0,5000000,10000000,18000000,32000000,52000000,80000000]; //Bậc thu nhập chịu thuế
    $thue = [0.05,0.10,0.15,0.20,0.25,0.30,0.35]; //Mức thuế theo %

    $i_tax = $luongchiuthue - (9000000 + $baohiem + $songuoi_pt*$mpt); //Thu nhập chịu thuế
  
  	$tax = 0;
    for ($k = count($bac)-1; $k>=0; $k--) {
      if ($i_tax > $bac[$k])
      {
        $tax = ($i_tax - $bac[$k])*$thue[$k];
        break;
      }
    }

    for($j=1; $j <= $k; $j++) {
      $tax += ($bac[$j] - $bac[$j-1])*$thue[$j-1];
    }

    return $tax; //Thuế thu nhập cá nhân
  }

	public function newPaycheck($dlChamCong) {
		$this->db->select('mnv, hoten, luongcb, songuoi_pt, hinhanh, macd');
		$query = $this->db->get('nhanvien');
		$arrUers = $query->result_array();
		$query = $this->db->get('dinhmucluong');
		$setupPaycheck = $query->row_array();
		$results = array();

		foreach ($arrUers as $key => $user) {
			$dataPayroll = json_decode(json_encode($dlChamCong[$key]), true);
			$dataInsert = array();
			$dataInsert['manv'] = $user['mnv'];
			$dataInsert['luonggio'] = round($user['luongcb']/$dataPayroll['giocong_lt'], $setupPaycheck['lamtrontien']);
			//Tiền tăng ca, đi trễ
			$dataInsert['tangca'] = $dataPayroll['tangca']*$dataInsert['luonggio']*($setupPaycheck['tangca']/100);
			if (($setupPaycheck['tinhditre'] == '1') && ($dataPayroll['demuon'] >= $setupPaycheck['ditre'])) {
				$dataInsert['ditre'] = $dataPayroll['demuon']*$dataInsert['luonggio']*($setupPaycheck['truditre']/100);
			} else {
				$dataInsert['ditre'] = 0;
			}
			//Do người lao động đóng
			$dataInsert['bhxh_nv'] = $user['luongcb']*($setupPaycheck['bhxh_nv']/100);
			$dataInsert['bhyt_nv'] = $user['luongcb']*($setupPaycheck['bhyt_nv']/100);
			$dataInsert['bhtn_nv'] = $user['luongcb']*($setupPaycheck['bhtn_nv']/100);
			$dataInsert['congdoan_nv'] = $user['luongcb']*($setupPaycheck['congdoan_nv']/100);
			//Do doanh nghiệp đóng
			$dataInsert['bhxh_cty'] = $user['luongcb']*($setupPaycheck['bhxh_cty']/100);
			$dataInsert['bhyt_cty'] = $user['luongcb']*($setupPaycheck['bhyt_cty']/100);
			$dataInsert['bhtn_cty'] = $user['luongcb']*($setupPaycheck['bhtn_cty']/100);
			$dataInsert['congdoan_cty'] = $user['luongcb']*($setupPaycheck['congdoan_cty']/100);
			//Tiền công tác
			$this->db->select_sum('phucap');
			$this->db->where('manv', $user['mnv']);
			$query = $this->db->get('congtac');
			$congtac = $query->row_array()['phucap'];
			$dataInsert['congtac'] = ($congtac == NULL)? 0 : $congtac;
			//Tiền khen thưởng
			$this->db->select_sum('tienktkl');
			$this->db->where('manv', $user['mnv']);
			$this->db->where('loai', 'Khen thưởng');
			$this->db->where('hinhthuc', 'Bằng tiền');
			$query = $this->db->get('ktkl');
			$khenthuong = $query->row_array()['tienktkl'];
			$dataInsert['khenthuong'] = ($khenthuong == NULL)? 0 : $khenthuong;
			//Tiền phạt
			$this->db->select_sum('tienktkl');
			$this->db->where('manv', $user['mnv']);
			$this->db->where('loai', 'Kỷ luật');
			$query = $this->db->get('ktkl');
			$phat = $query->row_array()['tienktkl'];
			$dataInsert['phat'] = ($phat == NULL)? 0 : $phat;
			//Tiền phụ cấp
			$this->db->select_sum('sotien');
			$this->db->where('macd', $user['macd']);
			$query = $this->db->get('phucap');
			$phucap = $query->row_array()['sotien'];
			$dataInsert['phucap'] = ($phucap == NULL)? 0 : $phucap;
			$thunhapchiuthue = ($dataPayroll['giocong']*$user['luongcb'])/$dataPayroll['giocong_lt'] + $dataInsert['khenthuong'] + $dataInsert['phucap'];
			$tienbh = $dataInsert['bhxh_nv'] + $dataInsert['bhyt_nv'] + $dataInsert['bhtn_nv'];
			$dataInsert['tongluong'] = round($thunhapchiuthue + $dataInsert['tangca'] - $tienbh -$dataInsert['congdoan_nv'] - $dataInsert['phat'], $setupPaycheck['lamtrontien']);
			if ($dataInsert['tongluong'] > $setupPaycheck['thunhapcn']) {
				$dataInsert['thuetncn'] = $this->tinh_tncn($thunhapchiuthue, $tienbh, $user['songuoi_pt']);
			} else {
				$dataInsert['thuetncn'] = 0;
			}
			$dataInsert['thuclanh'] = round($dataInsert['tongluong'] - $dataInsert['thuetncn'], $setupPaycheck['lamtrontien']);
			$this->db->insert($this->table, $dataInsert);
			$dataInsert['hoten'] = $user['hoten'];
			$dataInsert['luongcb'] = $user['luongcb'];
			$dataInsert['hinhanh'] = $user['hinhanh'];
			$results[] = $dataInsert;
		}
		return $results;
	}

	public function checkFinish($currMonth, $currYear) {
		$this->db->where('thang', $currMonth);
		$this->db->where('nam', $currYear);
		$query = $this->db->get('dulieuluong');
		if ($query->num_rows() > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	public function dsPhuCap() {
		$this->db->select('id, macd, tencd, mota, sotien, ghichu');
		$this->db->from('phucap');
		$this->db->join('chucdanh', 'chucdanh.mcd = phucap.macd');
		$query = $this->db->get();
		return $query->result_array();
	}
}