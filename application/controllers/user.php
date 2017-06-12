<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {
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
	public $rules = [
				[
					'field' => 'name',
					'rules' => 'trim|callback_req|!is_numeric'
				],[
					'field' => 'password',
					'rules' => 'trim|callback_req|sha1'
				],[
					'field' => 'email',
					'rules' => 'trim|callback_req|valid_email'
				],[
					'field' => 'gender',
					'rules' => 'trim|callback_req|alpha'
				],[
					'field' => 'birthday',
					'rules' => 'trim|callback_req'
				],[
					'field' => 'detail',
					'rules' => 'trim|callback_req|max_length[500]'
				],[
					'field' => 'img',
					'rules' => 'callback_post_upload[img]'
				]
		];
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */