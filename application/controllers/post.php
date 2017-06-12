<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends MY_Controller{
	public $rules = [
		[
			'field' => 'user_id',
			'rules' => 'trim|required|is_numeric|callback_exist_db[user.id]'
		],[
			'field' => 'content',
			'rules' => 'trim|required|min_length[10]|max_length[500]'
		]
	];

	public function create(){
		parent::create();
	}

	public function user($id){
		//$this->urlIDCheck($id,$this->get)
	}
}