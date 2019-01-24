<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
require 'BaseController.php';
class PayrollController extends BaseController
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
	}

	public function getKHCC_get() {
		$this->load->model('base_model');
		if (isset($_GET['filter'])) {
			$data = $this->base_model->getAllByField('khchamcong','hienthi','1');
		} else {
			$data = $this->base_model->getTable('khchamcong');
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function deleteKHCC_get() {
		$idKyhieu = $this->get('id');
		$this->load->model('base_model');
		$this->base_model->deleteTable('khchamcong','id',$idKyhieu);
		$data = $this->base_model->getTable('khchamcong');
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function saveKHCC_get() {
		$params = json_decode($this->get('params'), true);
		$params['thoigian'] = ($params['xuly'] == '-1') ? 0 : $params['thoigian'];
		$params['hienthi'] = ($params['hienthi'] == 'true') ? 1 : $params['hienthi'];
		$params['kyhieu'] = strtoupper($params['kyhieu']);
		$idKyhieu = $params['id'];
		$this->load->model('base_model');
		if ($idKyhieu == null) {
			$this->base_model->insertTable('khchamcong', $params);
		} else {
			$this->base_model->updateTable('khchamcong','id',$idKyhieu,$params);
		}
		$data = $this->base_model->getTable('khchamcong');
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function getSetupCC_get() {
		$this->load->model('base_model');
		$data = $this->base_model->getOneByField('tlchamcong','id',1);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function updateSetupCC_post() {
		$postData = $this->post('dataCC');
		$this->load->model('base_model');
		$this->base_model->updateTable('tlchamcong','id',1,$postData);
		$data = $this->base_model->getOneByField('tlchamcong','id',1);
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function getPayroll_get() {
		$this->load->model('base_model');
		$this->load->model('payroll_model');
		$data = array();
		
		$setupCC = $this->base_model->getTable('tlchamcong')[0];
		$data['khmacdinh'] = $setupCC['khmacdinh'];
		$data['finish'] = $setupCC['hoanthanh'];
		try {
			if (isset($_GET['new_create'])) {
				$month = Date('n', time());
				$year = Date('Y', time());
				// Tạo mới bảng chấm công
				$dayOfMonth = $this->datesOfMonth($month, $year);
				$data['day_numbers'] = $dayOfMonth['dayNumber'];
				$this->payroll_model->deletePayroll();
				$arrChamCong = array();
				// Khởi tạo bảng chấm công với dữ liệu mặc định
				$arrChamCong = $dayOfMonth['sunday'];
				$this->payroll_model->newPayroll($dayOfMonth['dayNumber'] - count($dayOfMonth['sunday']), $dayOfMonth['saturday'], $arrChamCong);
				$data['data'] = $this->payroll_model->getPayroll();
				$this->base_model->updateTable('tlchamcong', 'id', '1', array('hoanthanh'=>0,"currMonth"=>$month,"currYear"=>$year));
				$data['finish'] = 0;
			} else {
				$month = (isset($_GET['month'])) ? $this->get('month') : $setupCC['currMonth'];
				$year = (isset($_GET['year'])) ? $this->get('year') : $setupCC['currYear'];
				// Lấy dữ liệu từ bảng chấm công
				if ($setupCC['hoanthanh'] == '0' && $month == $setupCC['currMonth'] && $year == $setupCC['currYear']) {
					$data['data'] = $this->payroll_model->getPayroll();
				} else {
					$data['data'] = $this->payroll_model->getPayroll($month, $year);
					$data['finish'] = 1;
				}
				$dayOfMonth = $this->datesOfMonth($month, $year);
				$data['day_numbers'] = $dayOfMonth['dayNumber'];
			}
			$data['currYear'] = $year;
			$data['currMonth'] = $month;
			$arrBackupPayroll = $this->base_model->getTable('dlchamcong', 'thang, nam');
			$arrYear = array();
			$arrMonth = array();
			foreach ($arrBackupPayroll as $key => $value) {
				if (!in_array($value['thang'], $arrMonth)) {
					$arrMonth[] = $value['thang'];
				}
				if (!in_array($value['nam'], $arrYear)) {
					$arrYear[] = $value['nam'];
				}
			}
			if (!in_array($month, $arrMonth)) {
				$arrMonth[] = $month;
			}
			if (!in_array($year, $arrYear)) {
				$arrYear[] = $year;
			}
			if (!in_array($setupCC['currMonth'], $arrMonth)) {
				$arrMonth[] = $setupCC['currMonth'];
			}
			if (!in_array($setupCC['currYear'], $arrYear)) {
				$arrYear[] = $setupCC['currYear'];
			}
			$data['arrYear'] = $arrYear;
			$data['arrMonth'] = $arrMonth;
		}
		catch(Exception $e) {
			$data['error'] = true;
		}
		$data['error'] = false;

		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function updateDataPayroll($manv, $dayNumber, $kyhieu) {
		$this->load->model('base_model');
		$respone = array();
		$kyhieu = strtoupper($kyhieu);
		if (strpos($kyhieu, 'TC+') !== false) {
			$newSymbol = 'TC+(số giờ)';
		} else if (strpos($kyhieu, 'DT-') !== false) {
			$newSymbol = 'DT-(số phút)';
		} else {
			$newSymbol = $kyhieu;
		}
		$arrSymbol = $this->base_model->getTable('khchamcong', 'kyhieu');
		foreach ($arrSymbol as $row) {
      $Symbols[] = $row['kyhieu'];
    }
		if (in_array($newSymbol, $Symbols)) {
			try {
				$dataPayroll = $this->base_model->getOneByField('chamcong', 'manv', $manv);
				$setupCC = $this->base_model->getOneByField('tlchamcong', 'id', '1');
				$ttChamCong = json_decode($dataPayroll['ttchamcong'], true);
				$isMatch = false;
				if (array_key_exists($dayNumber, $ttChamCong)) {
					$isMatch = ($kyhieu == $ttChamCong[$dayNumber]) ? true : false;
					if (strpos($ttChamCong[$dayNumber], 'TC+') !== false) {
						$currSymbol = 'TC+(số giờ)';
					} else if (strpos($ttChamCong[$dayNumber], 'DT-') !== false) {
						$currSymbol = 'DT-(số phút)';
					} else {
						$currSymbol = $ttChamCong[$dayNumber];
					}
					$currDataSymbol = $this->base_model->getOneByField('khchamcong', 'kyhieu', $currSymbol);
				} else {
					$currDataSymbol = $this->base_model->getOneByField('khchamcong', 'kyhieu', $setupCC['khmacdinh']);
					$isMatch = ($kyhieu == $currDataSymbol['kyhieu']) ? true : false;
				}

				if ($isMatch) {
					$respone['error'] = true;
					$respone['message'] = '';
				} else {
					$respone['error'] = false;
					$newDataSymbol = $this->base_model->getOneByField('khchamcong', 'kyhieu', $newSymbol);
					// Xử lý phần bù
					if ($currDataSymbol['xuly'] == '1' && $newDataSymbol['xuly'] == '1') {
						$currWorkTime = (float)$currDataSymbol['thoigian'];
						$dataPayroll['giocong'] = (float)$dataPayroll['giocong'] - $currWorkTime;
					}
					else if ($currDataSymbol['xuly'] == '0' && $newDataSymbol['xuly'] == '0') {
						$currWorkTime = (float)$currDataSymbol['thoigian'];
						$dataPayroll['giocong'] = (float)$dataPayroll['giocong'] + $currWorkTime;
					}
					else {
						if (strpos($currDataSymbol['kyhieu'], 'TC') !== false) {
							$currHours = floatval(explode('+', $ttChamCong[$dayNumber])[1]);
							$dataPayroll['tangca'] -= $currHours;
						} 
						else if (strpos($currDataSymbol['kyhieu'], 'DT') !== false) {
							$currHours = floatval(explode('-', $ttChamCong[$dayNumber])[1]);
							$dataPayroll['denmuon'] -= $currHours;
						}
						if ($newSymbol == 'TC+(số giờ)' || $newSymbol == 'DT-(số phút)') {
							$newHours = ($newSymbol == 'TC+(số giờ)') ? floatval(explode('+', $kyhieu)[1]) : floatval(explode('-', $kyhieu)[1]);
							if ($currDataSymbol['xuly'] == '1') {
								$dataPayroll['giocong'] -= (float)$currDataSymbol['thoigian'];
							} else {
								$dataPayroll['giocong'] += (float)$currDataSymbol['thoigian'];
							}
						}
					}
					// Xử lý phần thêm mới
					if ($newDataSymbol['xuly'] == '1') {
						$dataPayroll['giocong'] += $newDataSymbol['thoigian'];	
					} 
					else if ($newDataSymbol['xuly'] == '0') {
						$dataPayroll['giocong'] -= $newDataSymbol['thoigian'];
					}
					/*New code*/
					else {
						if ($newSymbol == 'DT-(số phút)') {
							$dataPayroll['denmuon'] += $newHours;
						} 
						else if ($newSymbol == 'TC+(số giờ)') {
							$dataPayroll['tangca'] += $newHours;
						}
					}

					// Xử lý các trường hợp ngoại lệ
					if (strpos($currDataSymbol['kyhieu'], 'TC') === false && strpos($currDataSymbol['kyhieu'], 'DT') === false && $currDataSymbol['xuly'] == '1') {
						if ($newSymbol == 'TC+(số giờ)' || $newSymbol == 'DT-(số phút)') {
							$dataPayroll['giocong'] += 8;
						}
					}
					else if (strpos($currDataSymbol['kyhieu'], 'TC') !== false || strpos($currDataSymbol['kyhieu'], 'DT') !== false) {
						if ($newSymbol != 'TC+(số giờ)' && $newSymbol != 'DT-(số phút)' && $newDataSymbol['xuly'] == '1') {
							$dataPayroll['giocong'] -= 8;
						}
					}

					// Xử lý nghỉ có phép, ko phép, tổng ngày công
					if ($newSymbol == 'V' && $currDataSymbol['kyhieu'] != 'V') {
						$dataPayroll['nghikophep'] += 1;
						$dataPayroll['tongngaycong'] -= 1;
					} 
					if ($newSymbol != 'V' && $currDataSymbol['kyhieu'] == 'V') {
						$dataPayroll['nghikophep'] -= 1;
						$dataPayroll['tongngaycong'] += 1;
					}
					if ($newSymbol == 'P' && $currDataSymbol['kyhieu'] != 'P') {
						$dataPayroll['nghicophep'] += 1;
						if ($dataPayroll['nghicophep'] > $setupCC['phepthang']) {
							$dataPayroll['tongngaycong'] -= 1;
						}
					}
					if ($newSymbol != 'P' && $currDataSymbol['kyhieu'] == 'P') {
						$dataPayroll['nghicophep'] -= 1;
						if ($dataPayroll['nghicophep'] >= $setupCC['phepthang']) {
							$dataPayroll['tongngaycong'] += 1;
						}
					}
					$ttChamCong[$dayNumber] = $kyhieu; // Cập nhật lại thông tin chấm công
					$dataPayroll['ttChamCong'] = json_encode($ttChamCong);
				}
			}
			catch(Exception $e) {
				$respone['error'] = true;
				$respone['message'] = '';
			}
			// Update database
			$dataPayroll['ngaysua'] = date('Y-m-d', time());
			$this->base_model->updateTable('chamcong', 'manv', $manv, $dataPayroll);
			$respone['data'] = $this->base_model->getOneByField('chamcong', 'manv', $manv);
		} else {
			$respone['error'] = true;
			$respone['message'] = 'Ký hiệu không tìm thấy!';
		}

		return $respone;
	}

	public function updatePayroll_post() {
		$manv = $this->post('manv');
		$dayNumber = $this->post('dayNumber');
		$kyhieu = $this->post('kyhieu');
		$respone = $this->updateDataPayroll($manv, $dayNumber, $kyhieu);
		$this->set_response($respone, REST_Controller::HTTP_OK);
	}

	public function fastPayroll_post() {
		$arrUserId = $this->post('manv');
		$startDate = $this->post('startDate');
		$endDate = $this->post('endDate');
		$kyhieu = $this->post('kyhieu');
		$respone = array();
		foreach ($arrUserId as $key => $value) {
			$dateIndex = $startDate;
			for ($i=0; $i < $endDate - $startDate +1; $i++) { 
				$respone[] = $this->updateDataPayroll($value, $dateIndex++, $kyhieu);
			}
		}
		$this->set_response($respone, REST_Controller::HTTP_OK);
	}

	public function finishPayroll_get() {
		$this->load->model('payroll_model');
		$this->load->model('base_model');
		// Lưu dữ liệu chấm công lại
		$this->db->from('dlchamcong');
		$numRows = $this->db->count_all_results();
		if ($numRows == 12) {
			$arrPayrollId = $this->base_model->getTable('dlchamcong', 'id');
			$firstRecord = $arrPayrollId[0]['id'];
			$this->base_model->deleteTable('dlchamcong', 'id', $firstRecord);
		}
		$setupCC = $this->base_model->getTable('tlchamcong')[0];
		$data['dulieu'] = json_encode($this->payroll_model->getPayroll());
		$data['thang'] = $setupCC['currMonth'];
		$data['nam'] = $setupCC['currYear'];
		$this->base_model->insertTable('dlchamcong', $data);
		// Xóa data trong bảng chấm công
		$this->payroll_model->deletePayroll();
		// Cập nhật trạng thái hoàn thành
		$this->base_model->updateTable('tlchamcong', 'id', '1', array('hoanthanh'=>1));
		// Return empty data
		$respone['error'] = false;
		$this->set_response($respone, REST_Controller::HTTP_OK);
	}

	public function exportExcel_post() {
		$this->load->model('base_model');
		$this->load->model('payroll_model');
		$this->load->library('PHPExcel');

		// Lấy dữ liệu chấm công
		$setupCC = $this->base_model->getTable('tlchamcong')[0];
		$month = $this->post('month');
		$year = $this->post('year');
		if ($setupCC['hoanthanh'] == '0' && $month == $setupCC['currMonth'] && $year == $setupCC['currYear']) {
			$data = $this->payroll_model->getPayroll();
		} else {
			$data = $this->payroll_model->getPayroll($month, $year);
		}
		$dayOfMonth = $this->datesOfMonth($month, $year);
		$day_numbers = $dayOfMonth['dayNumber'];

		// Xuất dữ liệu ra file excel
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0, 1, "BẢNG TỔNG HỢP CHẤM CÔNG THÁNG " . $month . " Năm " . $year);
		$rowTitle = 'A1:' . PHPExcel_Cell::stringFromColumnIndex($day_numbers + 7) . '1';
		$sheet->mergeCells($rowTitle);
		$sheet->getStyle('A1')->getAlignment()->applyFromArray(
	    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
		$sheet->getStyle($rowTitle)->getFont()->setSize(16);
		$sheet->getStyle($rowTitle)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A3', 'STT')
		->setCellValue('B3', 'Mã NV')
		->setCellValue('C3', 'Họ và tên');
		for ($i=0; $i < $day_numbers; $i++) { 
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($i+3) . '3', '' . $i+1);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+3) . '3', 'Tăng ca (giờ)')
		->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+4) . '3', 'Đến muộn (giờ)')
		->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+5) . '3', 'Nghỉ CP')
		->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+6) . '3', 'Nghỉ KP')
		->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+7) . '3', 'Tổng ngày công');

		$i = 4;
		foreach ($data as $item)
		{
			$row = json_decode(json_encode($item), true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . $i, $i-3)
			->setCellValue('B' . $i, $row['manv'])
			->setCellValue('C' . $i, $row['hoten']);
			$ttChamCong = json_decode($row['ttchamcong'], true);
			for ($j=0; $j < $day_numbers; $j++) { 
				$cellVal = (isset($ttChamCong[$j+1]) && $ttChamCong[$j+1] != $setupCC['khmacdinh'] && $ttChamCong[$j+1] != NULL) ? $ttChamCong[$j+1] : $setupCC['khmacdinh'];
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue(PHPExcel_Cell::stringFromColumnIndex($j+3) . $i, $cellVal);
			}
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+3) . $i, $row['tangca'])
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+4) . $i, $row['denmuon'])
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+5) . $i, $row['nghicophep'])
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+6) . $i, $row['nghikophep'])
			->setCellValue(PHPExcel_Cell::stringFromColumnIndex($day_numbers+7) . $i, $row['tongngaycong']);
			$i++;
		}
		//Set width column
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(25);	
		for ($j=0; $j < $day_numbers; $j++) { 
			$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j+3))->setWidth(5);
		}
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($day_numbers+3))->setWidth(10);
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($day_numbers+4))->setWidth(10);
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($day_numbers+5))->setWidth(10);
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($day_numbers+6))->setWidth(10);
		$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($day_numbers+7))->setWidth(10);
		$styleArray = array(
	    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    	)
  	);
  	$objPHPExcel->getActiveSheet()->getStyle('A3:' . PHPExcel_Cell::stringFromColumnIndex($day_numbers + 7) . ($i-1))->applyFromArray($styleArray);
		//Set background for header
		$objPHPExcel->getActiveSheet()->getStyle('A3:' . PHPExcel_Cell::stringFromColumnIndex($day_numbers + 7) . '3')->getFill()
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

	public function listAbsentUsers_get() {
		$numAbsentDay = $this->get('songay');
		$this->load->model('payroll_model');
		$data = array();
		$nghicophep = $this->payroll_model->getUsersAbsent($numAbsentDay, true);
		foreach ($nghicophep as $key => $value) {
			$nghicophep[$key]['trangthai'] = 'Nghỉ có phép';
			$data[] = $nghicophep[$key];
		}
		$nghikophep = $this->payroll_model->getUsersAbsent($numAbsentDay, false);
		foreach ($nghikophep as $key => $value) {
			$nghikophep[$key]['trangthai'] = 'Nghỉ không phép';
			$data[] = $nghikophep[$key];
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function exportAbsentUsers_post() {
		$this->load->model('payroll_model');
		$this->load->library('PHPExcel');
		$filter = $this->post('filter');
		$dayNumberAbsent = $this->post('dayNumber');
		$data = array();
		if ($filter != '') {
			if ($filter == 'Nghỉ có phép') {
				$nghicophep = $this->payroll_model->getUsersAbsent($dayNumberAbsent, true);
				foreach ($nghicophep as $key => $value) {
					$nghicophep[$key]['trangthai'] = 'Nghỉ có phép';
					$data[] = $nghicophep[$key];
				}
			} else {
				$nghikophep = $this->payroll_model->getUsersAbsent($dayNumberAbsent, false);
				foreach ($nghikophep as $key => $value) {
					$nghikophep[$key]['trangthai'] = 'Nghỉ không phép';
					$data[] = $nghikophep[$key];
				}
			}
		} else {
			$nghicophep = $this->payroll_model->getUsersAbsent($dayNumberAbsent, true);
			foreach ($nghicophep as $key => $value) {
				$nghicophep[$key]['trangthai'] = 'Nghỉ có phép';
				$data[] = $nghicophep[$key];
			}
			$nghikophep = $this->payroll_model->getUsersAbsent($dayNumberAbsent, false);
			foreach ($nghikophep as $key => $value) {
				$nghikophep[$key]['trangthai'] = 'Nghỉ không phép';
				$data[] = $nghikophep[$key];
			}
		}

		// Xuất dữ liệu ra file excel
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0, 1, "Danh Sách Nhân Viên Nghỉ " . $dayNumberAbsent . " Ngày Trong Tháng");
		$sheet->mergeCells('A1:E1');
		$sheet->getStyle('A1')->getAlignment()->applyFromArray(
	    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
		);
		$sheet->getStyle('A1:E1')->getFont()->setSize(16);
		$sheet->getStyle('A1:E1')->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A3', 'STT')
		->setCellValue('B3', 'Mã NV')
		->setCellValue('C3', 'Họ và tên')
		->setCellValue('D3', 'Phòng ban')
		->setCellValue('E3', 'Trạng thái nghỉ');

		$i = 4;
		foreach ($data as $row)
		{
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . $i, $i-3)
			->setCellValue('B' . $i, $row['manv'])
			->setCellValue('C' . $i, $row['hoten'])
			->setCellValue('D' . $i, $row['tenpb'])
			->setCellValue('E' . $i, $row['trangthai']);
			$i++;
		}	
		//Set width column
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(25);	
		$sheet->getColumnDimension('D')->setWidth(20);	
		$sheet->getColumnDimension('E')->setWidth(20);	
		$styleArray = array(
	    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    	)
  	);
  	$objPHPExcel->getActiveSheet()->getStyle('A3:E' . ($i-1))->applyFromArray($styleArray);
		//Set background for header
		$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFE8E5E5');
		unset($styleArray); 
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
    header('Cache-Control: max-age=0'); //no cache                
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
    $objWriter->save('php://output');
	}
}