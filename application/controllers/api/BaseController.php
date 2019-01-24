<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
* 
*/
abstract class BaseController extends REST_Controller
{
  protected $user_authed = null;
  public function __construct() {
    parent::__construct();
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    $this->load->library("JWT");
    $header = getallheaders();
    if (isset($header['Authorization'])) {
      $token = explode(' ', $header['Authorization'])[1];
      $secretKey = base64_decode(SECRET_KEY); 
      $this->user_authed = $this->jwt->decode($token, $secretKey, ALGORITHM)->data;
      if (isset($this->user_authed->level)) {
        $this->session->set_userdata('level', $this->user_authed->level);   
      } 
    }    
    else {
      $this->set_response([
        'status' => FALSE,
        'message' => 'Bạn không có quyền truy cập vào hệ thống!'
        ], REST_Controller::HTTP_UNAUTHORIZED);
    } 
  }

  public function sendMail($fromEmail = NULL, $fromUser = NULL ,$toEmail, $subject, $body) {
    /*Config on localhost*/
    $config = Array(
      'protocol' => 'smtp',
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_port' => 465,
      'smtp_user' => 'hrm2016.demo@gmail.com',
      'smtp_pass' => 'Demo@123',
      'mailtype'  => 'html',
      );
    /*************************************************/
    if ($fromEmail == NULL) {
      $fromEmail = 'duytan.uit@gmail.com';
    }
    if ($fromUser == NULL) {
      $fromUser = 'Administrator';
    }   
    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");
    $this->email->from($fromEmail, $fromUser);
    $this->email->to($toEmail);
    $this->email->subject($subject);
    $this->email->message($body);
    if (!$this->email->send()) {
      show_error($this->email->print_debugger()); 
    }
  }

  public function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $pos = array_keys($words);
      $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
  }

  function datesOfMonth($month, $year) {
    $result = array();
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $iter = 24*60*60;
    $numSun = array();
    $numSat = array();
    $start = mktime(0, 0, 0, $month, 1, $year);
    $end = mktime(0, 0, 0, $month, $num, $year);
    for($i = $start; $i <= $end; $i=$i+$iter)
    {
      if(Date('D',$i) == 'Sat')
      {
        $numSat[Date('j',$i)] = 'T7';
      }
      if(Date('D',$i) == 'Sun')
      {
        $numSun[Date('j',$i)] = 'CN';
      }
    }
    $result['dayNumber'] = $num;
    $result['sunday'] = $numSun;
    $result['saturday'] = $numSat;
    return $result;
  }
}