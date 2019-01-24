<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/

class Base_model extends CI_Model
{
	public function __construct() {
		parent::__construct();
	}

	public function getTable($table, $list_field = NULL) {
		if ($list_field != NULL) {
			$this->db->select($list_field);
		}
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function getOneByField($table, $field, $value) {
		$this->db->where($field, $value);
		$query = $this->db->get($table);
		return $query->row_array();
	}

	public function getAllByField($table, $field, $value) {
		$this->db->where($field, $value);
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function insertTable($table, $data, $check_exist = NULL) {
		if(isset($check_exist)) {
			$this->db->where($check_exist, $data[$check_exist]);
			$query = $this->db->get($table);
			if ($query->num_rows() > 0) {
				return FALSE;
			}
		}
		$this->db->insert($table, $data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function updateTable($table, $field, $value, $data) {
		try {
			$this->db->where($field, $value);
			$this->db->update($table, $data);
			return TRUE;
		}
		catch (Exception $e) {
			return FALSE;
		}
	}

	public function deleteTable($table, $field = NULL, $value = NULL) {
		if ($field != NULL) {
			$this->db->where($field, $value);
		}
		$this->db->delete($table);
	}

	public function checkExist($table, $field, $value) {
		$this->db->where($field, $value);
		$query = $this->db->get($table);
		if($query->num_rows() > 0) {
			return TRUE;
		}
		return FALSE;
	}

	public function countValue($table, $field, $value)
	{
		$this->db->where($field, $value);
		return $this->db->count_all_results($table);
	}
}