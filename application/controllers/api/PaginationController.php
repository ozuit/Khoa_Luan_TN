<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
require 'BaseController.php';
/**
* 
*/
class PaginationController extends BaseController{ 
	public function __construct(){ 
		parent::__construct(); 
		$this->load->library('CI_acl');
		ob_start(); 
		$this->load->helper(array('form', 'url')); 
		$this->load->model('info_model'); 
		$this->load->model('base_model'); 
	} 

	public function announce_get(){ 
		$userID = $this->user_authed->user_id;
		$total_rows 	= $this->info_model->i_fGetTotalAnnouces($userID); 
		$perpage			= 5; 
		# Lấy offset 
		$page = $this->uri->segment(4);
		if (!$page || $page < 0 || $page > ceil($total_rows/$perpage)) {
			$offset =  1;
		} else {
			$offset = $page*5 - 4;
		}
		$this->load->library('pagination'); 
		$config['base_url'] 		= base_url().'#/ds-thong-bao/'; 
		$config['total_rows'] 		= $total_rows; 
		$config['per_page'] 		= $perpage; 
		$config['use_page_numbers'] = TRUE;
		$config['num_links']		= ceil($total_rows/$perpage);
		$config['cur_tag_open'] 	= '<a class="currentpage">'; 
		$config['cur_tag_close'] 	= '</a>'; 
		$config['next_link'] 		= 'Sau ›'; 
		$config['prev_link'] 		= '‹ Trước';
		$config['uri_segment']		= 4;
		# Khởi tạo phân trang 
		$this->pagination->initialize($config);
		# Tạo link phân trang 
		$str_links = $this->pagination->create_links(); 
		$data['pagination'] = explode('</a>',$str_links );
		$temp[0] = ($page>1) ? '<a href="#/ds-thong-bao/1" data-ci-pagination-page="1" rel="start">« Đầu</a>' : '';
		for ($i=0; $i < count($data['pagination']); $i++) { 
			$temp[$i+1] = $data['pagination'][$i].'</a>';
		}
		$temp[count($data['pagination'])] = ($page<ceil($total_rows/$perpage)) ?'<a href="#/ds-thong-bao/'.ceil($total_rows/$perpage).'" data-ci-pagination-page="'.round($total_rows/$perpage).'" rel="end">Cuối »</a>' : '';
		$data['pagination'] = $temp;
		# Đẩy dữ liệu ra view 
		$announcements = $this->info_model->a_fGetAnnounces($perpage, $offset-1, $userID);
		foreach ($announcements as $key => $value) {
			$userInfo = $this->base_model->getOneByField('nhanvien', 'taikhoan', $value['nguoitao']);
			$announcements[$key]['nguoitao'] = $userInfo['hoten'];
			$date=date_create($value['ngaytao']);
			$announcements[$key]['ngaytao'] = date_format($date,"d/m/Y - H:i");
		}
		$data['announcements'] = $announcements;
		$this->set_response($data, REST_Controller::HTTP_OK);
	}

	public function searchRequest_get() {
    $keyword = urldecode($this->uri->segment(4));
    $userID = $this->user_authed->user_id;
		$total_rows 	= $this->info_model->i_fGetTotalAnnouces($userID, $keyword); 
		$perpage			= 5; 
		# Lấy offset 
		$page = $this->uri->segment(5);
		if (!$page || $page < 0 || $page > ceil($total_rows/$perpage)) {
			$offset =  1;
		} else {
			$offset = $page*5 - 4;
		}
		# Cấu hình
		$this->load->library('pagination'); 
		$config['base_url'] 		= base_url().'#/ds-thong-bao/tim-kiem/'.$keyword.'/'; 
		$config['total_rows'] 		= $total_rows; 
		$config['per_page'] 		= $perpage; 
		$config['use_page_numbers'] = TRUE;
		$config['num_links']		= ceil($total_rows/$perpage);
		$config['cur_tag_open'] 	= '<a class="currentpage">'; 
		$config['cur_tag_close'] 	= '</a>'; 
		$config['next_link'] 		= 'Sau ›'; 
		$config['prev_link'] 		= '‹ Trước';
		$config['uri_segment']		= 5;
		# Khởi tạo phân trang 
		$this->pagination->initialize($config);
		# Tạo link phân trang 
		$str_links = $this->pagination->create_links(); 
		$data['pagination'] = explode('</a>',$str_links );
		$temp[0] = ($page>1) ? '<a href="#/ds-thong-bao/tim-kiem/'.$keyword.'/1" data-ci-pagination-page="1" rel="start">« Đầu</a>' : '';
		for ($i=0; $i < count($data['pagination']); $i++) { 
			$temp[$i+1] = $data['pagination'][$i].'</a>';
		}
		$temp[count($data['pagination'])] = ($page<ceil($total_rows/$perpage)) ?'<a href="#/ds-thong-bao/tim-kiem/'.$keyword.'/'.ceil($total_rows/$perpage).'" data-ci-pagination-page="'.round($total_rows/$perpage).'" rel="end">Cuối »</a>' : '';
		$data['pagination'] = $temp;
		# Đẩy dữ liệu ra view
		$announcements = $this->info_model->a_fGetAnnounces($perpage, $offset-1, $userID, $keyword);
		foreach ($announcements as $key => $value) {
			$userInfo = $this->base_model->getOneByField('nhanvien', 'taikhoan', $value['nguoitao']);
			$announcements[$key]['nguoitao'] = $userInfo['hoten'];
			$date=date_create($value['ngaytao']);
			$announcements[$key]['ngaytao'] = date_format($date,"d/m/Y - H:i");
		}
		$data['announcements'] = $announcements;
		$this->set_response($data, REST_Controller::HTTP_OK);
	}
}