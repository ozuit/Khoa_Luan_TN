<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
require APPPATH . '/libraries/REST_Controller.php';
class AuthController extends REST_Controller
{
	
	function __construct()
	{
		// Construct the parent class
		parent::__construct();
		$this->load->library("JWT");
	}

	public function checkToken_post()
	{
		$token = $this->post('access_token');
		$secretKey = base64_decode(SECRET_KEY); 
    $token_info = $this->jwt->decode($token, $secretKey, ALGORITHM);
    if (($token_info->exp - time()) > 0) {
    	//Reset token info
    	$token_info->exp = time() + TOKEN_TIMEOUT;
    	$secretKey = base64_decode(SECRET_KEY);
			$token = $this->jwt->encode($token_info, $secretKey, ALGORITHM);
    	$this->set_response([
				'status' => TRUE,
				'message' => 'Success',
				'refresh_token' => $token
				], REST_Controller::HTTP_OK);
    } else {
    	$this->set_response([
				'status' => FALSE,
				'message' => 'Token hết hiệu lực'
				], REST_Controller::HTTP_UNAUTHORIZED);
    }
	}

	public function login_post()
	{
		//cấu hình thông tin do google cung cấp
		$api_url     = 'https://www.google.com/recaptcha/api/siteverify';
		$site_key    = SITE_KEY_CAPTCHA;
		$secret_key  = SECRET_KEY_CAPTCHA;
		
		//lấy dữ liệu được post lên
		$site_key_post = $this->post('g-recaptcha-response');

	    //lấy IP của khach
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$remoteip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$remoteip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$remoteip = $_SERVER['REMOTE_ADDR'];
		}
		
	    //tạo link kết nối
		$api_url = $api_url.'?secret='.$secret_key.'&response='.$site_key_post.'&remoteip='.$remoteip;
	    //lấy kết quả trả về từ google
		$response = file_get_contents($api_url);
	    //dữ liệu trả về dạng json
		$response = json_decode($response);
		if(!isset($response->success) || $response->success == false)
		{
			$this->set_response([
				'status' => FALSE,
				'message' => 'Capcha không hợp lệ!'
				], REST_Controller::HTTP_UNAUTHORIZED);
		}
		else {
			$this->load->model('account_model');
			$user = strtoupper($this->post('username'));
			$pass = $this->post('password');
			$info_user = $this->account_model->checkLogin($user, $pass);
			switch ($info_user) {
				case -1:
				$this->set_response([
					'status' => FALSE,
					'message' => 'Mật khẩu không chính xác'
					], REST_Controller::HTTP_FORBIDDEN);
				break;
				case 0:
				$this->set_response([
					'status' => FALSE,
					'message' => 'Tài khoản không tồn tại'
					], REST_Controller::HTTP_NOT_FOUND);
				break;
				case 1:
				$this->set_response([
					'status' => FALSE,
					'message' => 'Tài khoản đang bị khóa'
					], REST_Controller::HTTP_UNAUTHORIZED);
				break;
				default:
				{
					//Update user online status
					$this->load->model('base_model');
					$this->base_model->updateTable('taikhoan', 'id', $info_user['id'], array('online'=>1));
					//Set quyền truy cập quản lý upload
					if ($info_user['level'] == '1' || $info_user['level'] == '2' || $info_user['level'] == '3') {
						$this->session->set_userdata('file_manager',true);
					}	
					$nhanvien = $this->base_model->getOneByField('nhanvien', 'mnv', $info_user['username']);
					//Set token response
					$payload = [
            'jti'  => base64_encode(mcrypt_create_iv(32)),
            'exp'  => time() + TOKEN_TIMEOUT,          
            'data' => [                  
								'user_id' => $info_user['id'], 
							 	'level' => $info_user['level'],
							 	'chucdanh' => $nhanvien['macd'],
              ]
	        ];	
	        $secretKey = base64_decode(SECRET_KEY);
					$token = $this->jwt->encode($payload, $secretKey, ALGORITHM);

					$this->set_response(array('token'=>$token, 'name'=> $info_user['hoten'], 'email'=>$info_user['email']), REST_Controller::HTTP_OK);
				}
				break;
			}
		}	
	}

	public function logout_post()
	{
		$this->load->model('base_model');
		$this->base_model->updateTable('taikhoan', 'id', $this->post('user_id'), array('online'=>0));
		if ($this->session->userdata('file_manager') != null) {
			$this->session->unset_userdata('file_manager');
		}	
	}
}