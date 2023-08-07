<?php 
namespace App\Libraries;
use \Config\Services;
use \Config\Database;

class Datatables {
	
	protected $db_total;
	protected $db_filtered;
	protected $input;
	protected $search;
	public $db;
	protected $order;
	protected $result;
	protected $resultFiltered;

	public function __construct($db=null){
		$this->db_filtered = $db;
		$this->input = Services::request();
		return $this;
	}

	public function search($str) {
		if (is_array($str)) {
			$this->search = $str;
		} else {
			$array = explode(', ', $str);
			$this->search = $array;
		}
		return $this;
	}

	public function getData() {
		$start = $this->input->getPost('start') ?: 0;
		$length = $this->input->getPost('length') ?: 10;
		$order_column = $this->input->getPost('order') ? $this->input->getPost('order')[0]['column'] + 1 : 1;
		$order_dir = $this->input->getPost('order') ? $this->input->getPost('order')[0]['dir'] : 'asc';
		$search = $this->input->getPost('search') ? $this->input->getPost('search')['value'] : '';

		if ($search && $this->search) {
			$this->db_filtered->groupStart();
			foreach ($this->search as $key => $value) {
				if (!$key) {
					$this->db_filtered->like($value, $search);
				} else {
					$this->db_filtered->orLike($value, $search);
				}
			}
			$this->db_filtered->groupEnd();
		}

		$this->db_filtered->orderBy($order_column, $order_dir);
		$this->db_total = $this->db_filtered->get(0,0,false)->getResult();
		$data = $this->db_filtered->get($length, $start, $reset=false)->getResult('array');
		return $data;
	}

	public function render($data) {
		$draw = $this->input->getPost('draw') ?: 0;
		$recordsTotal = count($this->db_total);
		// $recordsFiltered = $this->db_total;
		$output = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data,
        );

		echo json_encode($output);
	}

	public function renderNoKeys($array) {
		$data = [];
        foreach ($array as $key => $value) $data[] = array_values($value);
		$draw = $this->input->getPost('draw') ?: 0;
		$recordsTotal = count($this->db_total);
		// $recordsFiltered = $this->db_filtered->countAll();
		$output = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => array_values($data),
        );

		echo json_encode($output);
	}

	#custom add

	public function table($table) {
		$this->db = Database::connect()->table($table);
		return $this;
	}

	public function get() {
		$start = $this->input->getPost('start') ?: 0;
		$length = $this->input->getPost('length') ?: 10;
		$order_column = $this->input->getPost('order') ? $this->input->getPost('order')[0]['column'] : 1;
		$order_dir = $this->input->getPost('order') ? $this->input->getPost('order')[0]['dir'] : 'asc';
		$search = $this->input->getPost('search') ? $this->input->getPost('search')['value'] : '';
		if ($this->search && is_array($this->search)) {
			$this->order = $this->search;
			$this->db->groupStart();
			foreach ($this->search as $key => $value) {
				if (!$key) {
					$this->db->like($value, $search);
				} else {
					$this->db->orLike($value, $search);
				}
			}
			$this->db->groupEnd();
		}

		if ($this->order && is_array($this->order)) {
			$this->db->orderBy($this->order[$order_column], $order_dir);
		}

		$this->resultFiltered = $this->db->get(0, 0, false)->getNumRows();
		$this->result = $this->db->get($length, $start, true)->getResultArray();
		return $this;
	}

	public function modifyColumn($callback) {
		$this->result = array_map($callback, $this->result);
		return $this;
	}

	public function getRender() {
		$data = array_map('array_values', $this->result);
		$draw = $this->input->getPost('draw') ?: 0;
		$output = array(
            "draw" => $draw,
            "recordsTotal" => $this->resultFiltered,
            "recordsFiltered" => $this->resultFiltered,
            "data" => $data,
        );
		echo json_encode($output, JSON_PRETTY_PRINT);
	}

}
