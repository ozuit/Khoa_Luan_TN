<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
require 'BaseController.php';
class PaycheckController extends BaseController
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
	}

	public function loadSetupPaycheck_get () {
		$this->load->model('base_model');
		$data = $this->base_model->getOneByField('dinhmucluong', 'id', '1');
		return $this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function updateSetupPaycheck_post() {
		$postData = $this->post('setupPaycheck');
		$postData['tinhditre'] = ($postData['tinhditre'] == 'true') ? '1' : '0';
		$this->load->model('base_model');
		$this->base_model->updateTable('dinhmucluong', 'id', '1', $postData);
		return $this->set_response(array('message'=>'Cập nhật thiết lập thành công!'), REST_Controller::HTTP_OK);
	}

	public function newPaycheck_post() {
		$this->load->model('payroll_model');
		$this->load->model('paycheck_model');
		$this->load->model('base_model');
		$month = $this->post('month');
		$year = $this->post('year');
		try {
			$this->paycheck_model->deletePaycheck();
			$dataPayroll = $this->payroll_model->getPayroll($month, $year);
			$data['dataPaycheck'] = $this->paycheck_model->newPaycheck($dataPayroll);
			$this->base_model->updateTable('dinhmucluong', 'id', '1', array('hoanthanh'=>'0','currMonth'=>$month,'currYear'=>$year));
			$data['finish'] = 0;
			$data['error'] = false;
		}
		catch (Exception $e) {
			$data['error'] = true;
		}
		return $this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function loadPaycheck_get() {
		$data = array();
		$this->load->model('base_model');
		$this->load->model('paycheck_model');

		try {
			$setupPaycheck = $this->base_model->getTable('dinhmucluong', 'currMonth, currYear, hoanthanh')[0];
			$arrBackupPaycheck = $this->base_model->getTable('dulieuluong', 'thang, nam');
			$data['currMonth'] = $currMonth = (isset($_GET['month'])) ? $this->get('month') : $setupPaycheck['currMonth'];
			$data['currYear'] = $currYear = (isset($_GET['year'])) ? $this->get('year') : $setupPaycheck['currYear'];
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
			$data['arrYear'] = $arrYear;
			$data['arrMonth'] = $arrMonth;
			if ($setupPaycheck['hoanthanh'] == '0' && $currMonth == $setupPaycheck['currMonth'] && $currYear == $setupPaycheck['currYear']) {
				$data['dataPaycheck'] = $this->paycheck_model->getPaycheck();
			} else {
				$data['dataPaycheck'] = $this->paycheck_model->getPaycheck($currMonth, $currYear);
			}
			$data['finish'] = $this->paycheck_model->checkFinish($data['currMonth'], $data['currYear']);
		}
		catch(Exception $e) {
			$data['error'] = true;
		}
		$data['error'] = false;
		return $this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function finishPaycheck_get() {
		$this->load->model('paycheck_model');
		$this->load->model('base_model');
		// Lưu dữ liệu chấm công lại
		$this->db->from('dulieuluong');
		$numRows = $this->db->count_all_results();
		if ($numRows == 12) {
			$arrPayrollId = $this->base_model->getTable('dulieuluong', 'id');
			$firstRecord = $arrPayrollId[0]['id'];
			$this->base_model->deleteTable('dulieuluong', 'id', $firstRecord);
		}
		$setupCC = $this->base_model->getTable('dinhmucluong')[0];
		$data['dulieu'] = json_encode($this->paycheck_model->getPaycheck());
		$data['thang'] = $setupCC['currMonth'];
		$data['nam'] = $setupCC['currYear'];
		$this->base_model->insertTable('dulieuluong', $data);
		// Xóa data trong bảng chấm công
		$this->paycheck_model->deletePaycheck();
		// Cập nhật trạng thái hoàn thành
		$this->base_model->updateTable('dinhmucluong', 'id', '1', array('hoanthanh'=>1));
		// Return empty data
		$respone['error'] = false;
		$this->set_response($respone, REST_Controller::HTTP_OK);
	}

	public function exportPaycheck_post() {
		$currMonth = $this->post('month');
		$currYear = $this->post('year');
		$this->load->model('paycheck_model');
		if ($this->paycheck_model->checkFinish($currMonth, $currYear) === 1) {
			//Lấy dữ liệu từ table backup
			$dataPaycheck = $this->paycheck_model->getPaycheck($currMonth, $currYear);
		} else {
			//Lấy dữ liệu từ table temp
			$dataPaycheck = $this->paycheck_model->getPaycheck();
		}

		//Xuất dữ liệu ra file excel
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValueByColumnAndRow(0, 1, "BẢNG TỔNG HỢP LƯƠNG THÁNG " . $currMonth . " Năm " . $currYear);
		$rowTitle = 'A1:U1';
		$sheet->mergeCells($rowTitle);
		//Merge cell
		$sheet->mergeCells('A3:A4');
		$sheet->mergeCells('B3:B4');
		$sheet->mergeCells('C3:C4');
		$sheet->mergeCells('D3:E3');
		$sheet->mergeCells('F3:G3');
		$sheet->mergeCells('H3:J3');
		$sheet->mergeCells('K3:M3');
		$sheet->mergeCells('N3:O3');
		$sheet->mergeCells('P3:R3');
		$sheet->mergeCells('S3:S4');
		$sheet->mergeCells('T3:T4');
		$sheet->mergeCells('U3:U4');

	 	$style = array(
      'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
      )
    );
    $sheet->getStyle("A1:U4")->applyFromArray($style);
		$sheet->getStyle($rowTitle)->getFont()->setSize(16);
		$sheet->getStyle($rowTitle)->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A3', 'STT')
		->setCellValue('B3', 'Mã NV')
		->setCellValue('C3', 'Họ và tên')
		->setCellValue('D3', 'Định mức lương')
		->setCellValue('D4', 'Lương căn bản')
		->setCellValue('E4', 'Lương theo giờ')
		->setCellValue('F3', 'Tăng ca - về muộn')
		->setCellValue('F4', 'Trừ tiền đi trễ')
		->setCellValue('G4', 'Tiền tăng ca')
		->setCellValue('H3', 'Bảo hiểm do DN đóng')
		->setCellValue('H4', 'BHXH')
		->setCellValue('I4', 'BHYT')
		->setCellValue('J4', 'BHTN')
		->setCellValue('K3', 'Bảo hiểm do NLĐ đóng')
		->setCellValue('K4', 'BHXH')
		->setCellValue('L4', 'BHYT')
		->setCellValue('M4', 'BHTN')
		->setCellValue('N3', 'Phí công đoàn')
		->setCellValue('N4', 'DN đóng')
		->setCellValue('O4', 'NLĐ đóng')
		->setCellValue('P3', 'Các khoản tiền khác')
		->setCellValue('P4', 'Công tác phí')
		->setCellValue('Q4', 'Khen thưởng')
		->setCellValue('R4', 'Phạt')
		->setCellValue('S3', 'Tổng thu nhập')
		->setCellValue('T3', 'Thuế TNCN')
		->setCellValue('U3', 'Thực lãnh');

		$i = 5;
		foreach ($dataPaycheck as $item)
		{
			$row = json_decode(json_encode($item), true);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . $i, $i-4)
			->setCellValue('B' . $i, $row['manv'])
			->setCellValue('C' . $i, $row['hoten'])
			->setCellValue('D' . $i, $row['luongcb'])
			->setCellValue('E' . $i, $row['luonggio'])
			->setCellValue('F' . $i, $row['ditre'])
			->setCellValue('G' . $i, $row['tangca'])
			->setCellValue('H' . $i, $row['bhxh_cty'])
			->setCellValue('I' . $i, $row['bhyt_cty'])
			->setCellValue('J' . $i, $row['bhtn_cty'])
			->setCellValue('K' . $i, $row['bhxh_nv'])
			->setCellValue('L' . $i, $row['bhyt_nv'])
			->setCellValue('M' . $i, $row['bhtn_nv'])
			->setCellValue('N' . $i, $row['congdoan_cty'])
			->setCellValue('O' . $i, $row['congdoan_nv'])
			->setCellValue('P' . $i, $row['congtac'])
			->setCellValue('Q' . $i, $row['khenthuong'])
			->setCellValue('R' . $i, $row['phat'])
			->setCellValue('S' . $i, $row['tongluong'])
			->setCellValue('T' . $i, $row['thuetncn'])
			->setCellValue('U' . $i, $row['thuclanh']);
			$i++;
		}

		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(25);
		$styleArray = array(
	    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    	)
  	);
  	$objPHPExcel->getActiveSheet()->getStyle('A3:U' . ($i-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A3:U4')->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB('FFE8E5E5');
		unset($styleArray);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
    $objWriter->save('php://output');
	}

	public function sendMail_post() {
		$month = $this->post('month');
		$year = $this->post('year');
		$dataPaycheck = $this->post('data');
		try {
			$dataPaycheck['month'] = $month;
			$dataPaycheck['year'] = $year;
			$this->db->where('username', $dataPaycheck['manv']);
			$this->db->select('email');
			$query = $this->db->get('taikhoan');
			$email = $query->row_array()['email'];
			$subject = 'Phiếu lương cá nhân tháng ' . $month;
			$body = $this->load->view('emails/paycheck.php', $dataPaycheck, TRUE);
			$this->sendMail( NULL, NULL, $email, $subject, $body);
		}
		catch(Exception $e) {
			$respone['error'] = true;
		}
		$respone['error'] = false;
		$this->set_response($respone, REST_Controller::HTTP_OK);
	}

	public function printPDF_post() {
		$data = $this->post('data');
		$data['month'] = $this->post('month');
		$data['year'] = $this->post('year');
		$data['dateCreated'] = date("d/m/Y");

		$this->load->library('pdf');
		$this->pdf->load_view('template_pdf/phieuluong.html', $data);
		$this->pdf->set_paper('a4', 'portrait');
		$this->pdf->render();
		$data = $this->pdf->output();
		echo ($data);
	}

	public function dsPhuCap_get() {
		$this->load->model('paycheck_model');
		$this->load->model('base_model');
		$data['phucap'] = $this->paycheck_model->dsPhuCap();
		$data['chucdanh'] = $this->base_model->getTable('chucdanh', 'mcd, tencd');
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function themPhuCap_post() {
		$this->load->model('base_model');
		$data = $this->post('data');
		$this->base_model->insertTable('phucap', $data);
		$this->dsPhuCap_get();
	}

	public function suaPhuCap_post() {
		$this->load->model('base_model');
		$data = $this->post('data');
		unset($data['tencd']);
		$this->base_model->updateTable('phucap', 'id', $data['id'], $data);
		$this->dsPhuCap_get();
	}

	public function xoaPhuCap_get() {
		$this->load->model('base_model');
		$id = $this->get('id');
		$this->base_model->deleteTable('phucap', 'id', $id);
		$this->dsPhuCap_get();
	}
}