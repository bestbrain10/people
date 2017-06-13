<?php

class MY_Model extends CI_Model{

	const TABLE = false;
	public function __construct(){
	    if(!$this::TABLE){
	        throw new Exception("Please Specify TABLE constant",1146);
        }
		parent::__construct();
		$this->load->database();
	}

	public function all(){
		return $this->db->get($this::TABLE)->result();
	}

	public function create($data){
		if($this->db->insert($this::TABLE, $data)){
		    $got = $this->get($this->db->insert_id());
			$this->populate($got);
			return $got;
		}else{
			return false;
		}
	}

	public function get($id,$populate = false){
		$data = $this->db->get_where($this::TABLE,['id' => $id])->result();
		if ($populate):
			$this->populate($data);
		//should you return???
		endif;
		return $data;
	}

	public function populate($obj){
		$obj = (array) $obj;
		foreach ($obj as $key => $value) {
			$this->$key = $value;
		}
	}

	public function delete($id){
		if($this->db->get_where($this::TABLE,['id' => $id])->result()){
			$this->db->delete($this::TABLE,['id'=>$id]);
			return true;
		}else{
			return false;
		}
	}

	public function update($id,$data){
		$this->db->update($this::TABLE,$data,['id' => $id]);
		if($this->db->affected_rows()){
			$this->populate($this->get($id));
			return (empty($this))?false:true;
		}else{
			return false;
		}
	}


	
}
