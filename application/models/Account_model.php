<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/

class Account_model extends CI_Model
{
	protected $_table = 'taikhoan';

	public function __construct() {
		parent::__construct();
	}

    public function getAll($hideAdmin = NULL)
    {
        if ($hideAdmin != NULL) {
           $this->db->where('level !=', 4);     
        }
        $this->db->where('level !=', 1);
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

	public function checkLogin($user, $pass){
        $this->db->where('username',$user);
        $query=$this->db->get($this->_table);
        if($query->num_rows() > 0){
            $this->db->select('taikhoan.*, nhanvien.hoten');
            $this->db->from('taikhoan');
            $this->db->join('nhanvien', 'taikhoan.username = nhanvien.mnv');
            $this->db->where('username',$user);
            $query = $this->db->get();
            if (($query->row_array()['password'] == $pass)) {
                if ($query->row_array()['active'] == '1') {
                    return $query->row_array();
                }
            	else {
                    return 1; //Unactive
                }
            }
            else {
                return -1; //Miss password
            }
        }
        return 0; //Not found
    }

    public function checkUser($user_id) {
        $this->db->where('id', $user_id);
        $query = $this->db->get($this->_table);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return FALSE;
    }

    public function updateUser($where=null, $value=null, $data) {
        $this->db->where($where, $value);
        return $this->db->update($this->_table, $data);
    }

    public function addUser($data) {
        $this->db->where('username', $data['username']);
        $query = $this->db->get($this->_table);
        if ($query->num_rows() > 0) {
            return $query->row_array()['id'];
        }
        else {
            $this->db->insert($this->_table, $data);
            return $this->db->insert_id();
        }  
    }
}