<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	public $_error = array();
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
		$this->_name = get_called_class();
		header("content-type: application/json");
		parent::__construct();
		$this->load->model('app'.$this->_name,"{$this->_name}");
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation','fv');
		$this->load->database();
		$this->form_validation->set_rules($this->rules);
	}

	public function display($message,$arr=[]){
		$r['message'] = $message; 
		if($this->errors()){
			$r = array_merge($r,['error'=>$this->errors()]);
		}
		if(!empty($arr)){
			$r = array_merge($r,$arr);
		}
		echo json_encode((object) $r);
	}

	public function error_sanitize($err,$tags = false){
		$err = ($tags)?trim($err):trim(strip_tags($err));
		if(strpos($err,"\n")):
			return explode("\n",$err);
		else:
			return $err;
		endif;
	}

	public function errors($err=[]){
		if(!empty($err)){
			if(is_array($err)){
				$this->_error = array_merge($this->_error,$err);
			}else{
					$err = $this->error_sanitize($err);
					if(is_array($err)):
						$this->_error = array_merge($this->_error,$err);
					else:
						array_push($this->_error,$err);
					endif;
			}
			return $this->_error;
		}else{
			return $this->_error;
		}
	}

	public function req($value){
	  $this->form_validation->set_message('req', 'provide %s or else');
		if($this->uri->segment(2) == 'update'):
			return true;
		else:
			return (bool) $value;
		endif;
	}

	public function upload($file)
	{
		if(isset($_FILES) && isset($_FILES[$file])):
			$this->load->library('upload', [
				'upload_path' 	=> './upload/',
				'allowed_types' => 'gif|jpg|png',
				'max_size' 		=> '1024',
				'max_width'		=> '1024',
				'max_height'	=> '768'
			]);
		
			if (!$this->upload->do_upload($file)):
				$this->errors($this->upload->display_errors());
				return false;
			else:
				return $this->upload->data();
			endif;
		else:
			$this->errors("File for '{$file}' field is missing");
			return false;
		endif;
	}

	public function post_upload($value,$field){
		//allow user to specify if the field can be optional during updates
		$this->form_validation->set_message('post_upload', "Please Provide a file for the '%s' field");
		@list($field,$req) = explode(',',$field);
		if(($this->uri->segment(2) == "update" && $req) && !$value):
			return true;
		endif;
		if(isset($_FILES[$field])){
			$_POST[$field] = $_FILES[$field]['name'];
			if(validation_errors()){
				return true;
			}else{
				return (bool) ($_POST[$field] = $this->upload($field,true)['file_name']);
			}
		}else{
			return false;
		}
	}

	public function exist_db($value,$arg){
		list($table,$col) = explode('.',$arg);
		$this->db->get_where($table,[$col => $value]);
		$this->form_validation->set_message('exist_db', "The {$table} {$col} does not exist");
		return (bool) $this->db->affected_rows();
	}


	public function create($func = true){
		if(isset($_POST)){
			$this->form_validation->set_message('required', 'provide %s or else');
			if ($this->form_validation->run()){
					$this->{$this->_name}->create($_POST);
					$this->display($this->_name.' Created', [$this->_name => $this->{$this->_name}]);
					return true;
			}else{
				$this->errors(validation_errors());
				$this->display("Failed to create {$this->_name}");
				return false;				
			}
		}
	}

	public function index()
	{
		//list all the students, adjust for pagination and criteria selection (like)
		echo json_encode($this->{$this->_name}->all());
	}

	public function delete($id){
		$this->urlIDCheck($id,$this->{$this->_name}->delete($id));		
	}

	public function update($id){		
		if($this->form_validation->run()):
			$this->urlIDCheck($id,$this->{$this->_name}->update($id,$_POST));
			return true;
		else:
			$this->errors(validation_errors());
			$this->display("Failed to update {$this->_name}");
			return false;
		endif;
	}

	public function get($id){
		$this->urlIDCheck($id,$this->{$this->_name}->get($id,true));
	}

	protected function urlIDCheck($id,$func){
		if(is_numeric($id)){
		
			if($func){
				$this->display("{$this->_name} Found",[$this->_name => $this->{$this->_name}]);
				return true;
			}else{
				$this->errors("{$this->_name} ID {$id} not found");
				$this->display("Operation Failed");
				return false;
			}
		}else{
			$err = ($id)?$id.' is not a valid number':
							"Provide a valid {$this->_name} ID";
			$this->errors($err);
			$this->display("Operation Failed");
			return false;
		}	
	}

}
