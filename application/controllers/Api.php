<?php

/**
 * Created by PhpStorm.
 * User: Bestbrain Livinus
 * Date: 6/12/2017
 * Time: 10:28 AM
 */

class Api extends CI_Controller
{
    private $_error = array();
    public function __construct()
    {
        header("Content-type: Application/json");
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation','fv');
        $this->load->database();
        $this->form_validation->set_message('is_numeric',"The {field} cannot be numeric");
    }

    public function post(){
        //check if user exist
        $this->init([
            [
                'field' => 'user_id',
                'rules' => 'trim|required|is_numeric|callback_exist_db[user.id]'
            ],[
                'field' => 'content',
                'rules' => 'trim|required|min_length[10]|max_length[500]'
            ]
        ]);
    }

    public function comment(){
        //make a validation method to check is user actually made the post
        $this->init([
            [
                'field' => 'user_id',
                'rules' => 'trim|required|is_numeric|callback_exist_db[user.id]'
            ],[
                'field' => 'post_id',
                'rules' => 'trim|required|is_numeric|callback_exist_db[post.id]'
            ],[
                'field' => 'content',
                'rules' => 'trim|required|min_length[10]|max_length[500]'
            ]
        ]);
    }

    public function user(){

        $this->init([
            [
                'field' => 'name',
                'rules' => 'trim|callback_req'
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
                'field' => 'bio',
                'rules' => 'trim|callback_req|max_length[500]'
            ],[
                'field' => 'img',
                'rules' => 'trim'
            ]
        ]);
    }

    public function message(){
        $this->init([
            [
                'field' => 'sender_id',
                'rules' => 'trim|required|is_numeric|callback_exist_db[user.id]'
            ],[
                'field' => 'reciever_id',
                'rules' => 'trim|required|is_numeric|callback_exist_db[user.id]'
            ],[
                'field' => 'content',
                'rules' => 'trim|required'
            ]
        ]);
    }


    /**
     * initializes api calls
     * @param array $rules
     * @return mixed
     */
    private function init(Array $rules){
        $model = $this->uri->segment(2);
        $method = $this->uri->segment(3);
        //get the model and method from the url then load it
        $this->load->model("app{$model}",$model);

        if(method_exists($this->{$model},$method)){
           //if its a write operation stop for validations
            if($method == "create" || $method == "update") {
                $this->output->set_status_header($method == "update"?200:201);
                $this->form_validation->set_rules($rules);
                if($this->form_validation->run()){
                    $done = $method == "update"? "Updated": "created";
                    $this->display("$model $done",[$model => call_user_func_array([$this->{$model}, $method], $this->arg($_POST))]);
                }else{
                    //error with form validation
                    $this->output->set_status_header(400);
                    $this->errors(validation_errors());
                    $this->display("$model field(s) has invalid values");
                }
            }else {
                //else just fall through
                $done = $method == "get"? "found": "deleted";
                $this->display("$model $done",[$model => call_user_func_array([$this->{$model}, $method], $this->arg())]);
            }
        }elseif(is_numeric($method)){
            $this->display("$model found",[$model => call_user_func([$this->{$model},"get"],$method)]);
        }else{
            //error
        }
        ///TODO: make an entry point for form_validations
    }

    /**
     * splits the code igniter uri_string and return everything from the third index to be passed as argument to models
     * @param array $extra is an array to be appended to the argument extracted from the url
     * @return array
     */
    private function arg($extra=[]){
        $arg = explode('/',$this->uri->uri_string());
        for($i = 0;$i< 3;$i++) {
            array_shift($arg);
        }
        return $extra? $arg?[reset($arg),$extra]:[$extra] : $arg;
    }

    /**
     * @param mixed $message
     * @param array $arr
     */
    public function display($message, $arr=[]){
      /*
       * gets the message you want to display
       * checks if there are any errors present
       * create an array containing your message and errors(if present)
       * {
       *    'message or data': ...
       *    'error (if present)': ...
       * }
       */

        $r['message'] = $message;
        if($this->errors()){
            $r = array_merge($r,['error'=>$this->errors()]);
        }
        if(!empty($arr)){
            $r = array_merge($r,$arr);
        }

        echo json_encode((object) $r,JSON_NUMERIC_CHECK);
    }

    //validation callbacks

    public function exist_db($value,$arg){
        list($table,$col) = explode('.',$arg);
        $this->db->get_where($table,[$col => $value]);
        $this->form_validation->set_message('exist_db', "The {$table} {$col} '{$value}' does not exist");
        return (bool) $this->db->affected_rows();
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
        //should automatically return validation errors if no arguments where given
        //check the _error variable, if its empty try to populate with validation errors, if empty
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

}