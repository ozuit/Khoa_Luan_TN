<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
require 'BaseController.php';
class AccountController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('CI_acl');
	}

	public function showAccount_get() {
		$this->load->model('account_model');
		$this->load->model('base_model');
		$currUserId = $this->user_authed->user_id;
		$currUserInfo = $this->base_model->getOneByField('taikhoan', 'id', $currUserId);
		if ($currUserInfo['level'] == 4) {
			$account_data =  $this->account_model->getAll(true);
		} else {
			$account_data =  $this->account_model->getAll();
		}
		
		$this->set_response($account_data, REST_Controller::HTTP_OK);
	}

	public function changeActive_put() {
		$this->load->model('account_model');
		$user_id = $this->put('user_id');
		$status = $this->put('status');
		$status = ($status == 'true')? 1 : 0;
		$this->account_model->updateUser('id', $user_id, array('active'=>$status));
		$this->set_response([
			'status' => TRUE,
			'message' => 'Đã cập nhật thành công'
		], REST_Controller::HTTP_OK);
	}

	public function changeLevel_put() {
		$this->load->model('account_model');
		$user_id = $this->put('user_id');
		$level = $this->put('level');	
		if ($level == 1) {
			$this->set_response([
				'status' => FALSE,
				'message' => 'Yêu cầu không được chấp nhận'
			], REST_Controller::HTTP_NOT_MODIFIED);
		}
		else {
			$this->account_model->updateUser('id', $user_id, array('level'=>$level));
			$this->set_response([
				'status' => TRUE,
				'message' => 'Đã cập nhật thành công'
			], REST_Controller::HTTP_OK);
		}
	}

	/************SCHEDULE**************/
	public function readSchedule_get() {
		$data = array(); 
		$currUserId = $this->user_authed->user_id;
		$this->load->model('base_model');
		$data = $this->base_model->getAllByField('ghichu','UserID',$currUserId);
		foreach ($data as $key => $value) {
			$value['IsAllDay'] = ($value['IsAllDay'] == 1) ? true : false;
		}
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	private function refactorSchedule($schedule) {
		$arrData = json_decode($schedule, true)[0];
		unset($arrData['startTimezone']);
		unset($arrData['endTimezone']);	
		unset($arrData['recurrenceRule']);	
		unset($arrData['recurrenceException']);	
		$arrData['Start'] = '/Date('.strtotime($arrData['Start']).'000)/';
		$arrData['End'] = '/Date('.strtotime($arrData['End']).'000)/';

		return $arrData;
	}

	public function createSchedule_get() {
		$jsonData = $this->get('models');
		$arrData = $this->refactorSchedule($jsonData);
		unset($arrData['TaskID']);	
		$arrData['UserID'] = $this->user_authed->user_id;
		$this->load->model('base_model');
		$arrData['TaskID'] = $this->base_model->insertTable('ghichu',$arrData);
		$this->set_response($arrData, REST_Controller::HTTP_OK);
	}

	public function updateSchedule_get() {
		$jsonData = $this->get('models');
		$arrData = $this->refactorSchedule($jsonData);
		$this->load->model('base_model');
		$this->base_model->updateTable('ghichu','TaskID',$arrData['TaskID'],$arrData);
		$this->set_response($arrData, REST_Controller::HTTP_OK);
	}

	public function deleteSchedule_get() {
		$jsonData = $this->get('models');
		$arrData = $this->refactorSchedule($jsonData);
		$this->load->model('base_model');
		$this->base_model->deleteTable('ghichu','TaskID',$arrData['TaskID']);
		$this->set_response($arrData, REST_Controller::HTTP_OK);
	}

	public function countSchedule_get()
	{
		$this->load->model('base_model');
		$arrSchedules = $this->base_model->getAllByField('ghichu', 'UserID', $this->user_authed->user_id);
		$sum = 0;
		foreach ($arrSchedules as $key => $schedule) {
			$endDate = substr($schedule['End'], 6, 13);
			if ( $endDate/1000 >= time() ) {
				$sum += 1;
			}
		}
		$this->set_response($sum, REST_Controller::HTTP_OK);
	}
}