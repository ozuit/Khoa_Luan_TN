<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require 'BaseController.php';
/**
* 
*/
class StatisticController extends BaseController
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
		$this->load->model('info_model');
	}

	public function tkChucDanh_get() {
		$result = $this->info_model->tk_chuc_danh();
		for ($i=0; $i < count($result); $i++) { 
			$data['tencd'][$i] = $result[$i]['tencd'];
			$data['soluong'][$i] = $result[$i]['soluong'];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tkDoTuoi_get() {
		$dotuoiNam = $this->info_model->tk_do_tuoi("'Nam'");
		$dotuoiNu = $this->info_model->tk_do_tuoi("'Nữ'");
		$duoi25 = 0; $tu25den39 = 0; $tu40den60 = 0; $tren60 = 0; //Nhóm tuổi dành cho Nam
									 $tu40den55 = 0; $tren55 = 0; //Nhóm tuổi dành cho Nữ
		for ($i=0; $i < count($dotuoiNam); $i++) { 
			$tuoi = 2016-$dotuoiNam[$i]['nam'];
			if ($tuoi < 25) {
				$duoi25 += 1;
			} elseif ($tuoi < 40) {
				$tu25den39 += 1;
			} elseif ($tuoi <= 60) {
				$tu40den60 += 1;
			} else {
				$tren60 += 1;
			}
		}
		$data['nhomNam'] = array('Dưới 25', '25 đến dưới 40', '40 đến 60', 'Trên 60');
		$data['soluongNam'] = array($duoi25, $tu25den39, $tu40den60, $tren60);
		for ($i=0; $i < count($dotuoiNu); $i++) { 
			$tuoi = 2016-$dotuoiNu[$i]['nam'];
			if ($tuoi < 25) {
				$duoi25 += 1;
			} elseif ($tuoi < 40) {
				$tu25den39 += 1;
			} elseif ($tuoi <= 55) {
				$tu40den55 += 1;
			} else {
				$tren55 += 1;
			}
		}
		$data['nhomNu'] = array('Dưới 25', '25 đến dưới 40', '40 đến 55', 'Trên 55');
		$data['soluongNu'] = array($duoi25, $tu25den39, $tu40den60, $tren60);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tk_TrinhDo_get() {
		$arrYears = $this->info_model->get5years();
		$data['trenDH'] = array();
		$data['DH'] = array();
		$data['duoiDH'] = array();
		for ($i=0; $i < count($arrYears); $i++) { 
			$data['nam'][$i] = $arrYears[4-$i]['nam'];
			$arrTrinhdo = $this->info_model->tk_trinh_do($data['nam'][$i]);
			$dh = 0; $tdh = 0; $ddh = 0;
			for ($j=0; $j < count($arrTrinhdo); $j++) { 
				switch ($arrTrinhdo[$j]['trinhdo']) {
					case 'Tiến sĩ':
					case 'Thạc sĩ':
						$tdh += $arrTrinhdo[$j]['soluong'];
						break;
					case 'Đại học':
						$dh += $arrTrinhdo[$j]['soluong'];
						break;
					default:
						$ddh += $arrTrinhdo[$j]['soluong'];
						break;
				}
			}
			$data['trenDH'][$i] = $tdh;
			$data['DH'][$i] = $dh;
			$data['duoiDH'][$i] = $ddh;
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function trinhdoPie_get() {
		$result = $this->info_model->pie_trinhdo();
		for ($i=0; $i < count($result); $i++) { 
			$data['trinhdo'][$i] = $result[$i]['trinhdo'];
			$data['soluong'][$i] = $result[$i]['soluong'];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tk_GioiTinh_get() {
		$arrYears = $this->info_model->get5years();
		$data['year'] = array();
		$data['nu'] = array();
		for ($i=0; $i < count($arrYears); $i++) { 
			$data['year'][$i] = $arrYears[4-$i]['nam'];
			$arrGioitinh = $this->info_model->tk_gioi_tinh($data['year'][$i]);
			$nam = 0; $nu = 0;
			for ($j=0; $j < count($arrGioitinh); $j++) { 
				if ($arrGioitinh[$j]['gioitinh'] == 'Nam') {
					$nam += $arrGioitinh[$j]['soluong'];
				}
				else {
					$nu += $arrGioitinh[$j]['soluong'];
				}
			}
			$data['nam'][$i] = $nam;
			$data['nu'][$i] = $nu;
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tk_NgayNghi_get() {
		$this->load->model('payroll_model');
		$data['songay'] = array();
		$data['nghicophep'] = array();
		$data['nghikophep'] = array();
		$nghicophep = $this->payroll_model->tkNgayNghi(true);
		$nghikophep = $this->payroll_model->tkNgayNghi(false);
		if (count($nghicophep) > count($nghikophep)) {
			foreach ($nghicophep as $key1 => $value1) {
				foreach ($nghikophep as $key2 => $value2) {
					if ($value1['nghicophep'] == $value2['nghikophep']) {
						if (!in_array($value1['nghicophep'], $data['songay'])) {
							$data['songay'][] = 'Nghỉ ' . $value1['nghicophep'] . ' ngày';
						}
						unset($nghicophep[$key1]);
						unset($nghikophep[$key2]);
						$data['nghicophep'][] = $value1['soluong'];
						$data['nghikophep'][] = $value2['soluong'];
						break;
					}
				}
			}
		} else {
			foreach ($nghikophep as $key1 => $value1) {
				foreach ($nghicophep as $key2 => $value2) {
					if ($value1['nghikophep'] == $value2['nghicophep']) {
						if (!in_array($value1['nghikophep'], $data['songay'])) {
							$data['songay'][] = 'Nghỉ ' . $value1['nghikophep'] . ' ngày';
						}
						unset($nghikophep[$key1]);
						unset($nghicophep[$key2]);
						$data['nghikophep'][] = $value1['soluong'];
						$data['nghicophep'][] = $value2['soluong'];
						break;
					}
				}
			}
		}
		foreach ($nghicophep as $key => $value) {
			$data['songay'][] = 'Nghỉ ' . $value['nghicophep'] . ' ngày';
			$data['nghicophep'][] = $value['soluong'];
			$data['nghikophep'][] = 0;
		}
		foreach ($nghikophep as $key => $value) {
			$data['songay'][] = 'Nghỉ ' . $value['nghikophep'] . ' ngày';
			$data['nghikophep'][] = $value['soluong'];
			$data['nghicophep'][] = 0;
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tkTonGiao_get() {
		$result = $this->info_model->tk_ton_giao();
		for ($i=0; $i < count($result); $i++) { 
			$data['tongiao'][$i] = $result[$i]['tongiao'];
			$data['soluong'][$i] = $result[$i]['soluong'];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function tkDanToc_get() {
		$result = $this->info_model->tk_dan_toc();
		for ($i=0; $i < count($result); $i++) { 
			$data['dantoc'][$i] = $result[$i]['dantoc'];
			$data['soluong'][$i] = $result[$i]['soluong'];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}
}