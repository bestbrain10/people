<?php
//sleep(2);
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
        $_POST = !empty($_POST)?$_POST : $this->res();
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
                'rules' => 'trim|max_length[500]'
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
        //find out how to handle database errors $this->db->errors()
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
                    $result = call_user_func_array([$this->{$model}, $method], $this->arg($_POST));
                    if($result) {
                        $this->display("$model $done",[$model => $result]);
                    }else{
                        $this->display("Operation Failed");
                    }
                }else{

                    //error with form validation
                    $this->output->set_status_header(400);
                    $this->errorFields();
                    $this->display("$model field(s) has invalid values");
                }
            }else {
                //else just fall through
                $done = $method == "delete"? "deleted": "found";
                $result = call_user_func_array([$this->{$model}, $method], $this->arg(@$_POST,true));
                if($result){
                    $this->display("$model $done",[$model => $result]);
                }else{
                    http_response_code(400);
                    $this->errors($this->{$model}->error);
                    $this->display("$method Operation Failed");
                }
            }
        }elseif(is_numeric($method)){
            //if a number was called on the model
            if($result = call_user_func([$this->{$model},"get"],$method)){
                $this->display("$model found",[$model => $result]);
            }else{
                http_response_code(404);
                $this->display("$model not found");
            }
        }else{
            //model or method doest exist
            $this->output->set_status_header(400);
            $this->display("unable to locate resource '$method'");
        }
    }

    /**
     * splits the CodeIgniter uri_string and return everything from the third index to be passed as argument to models
     * @param array $extra is an array to be appended to the argument extracted from the url
     * @param bool $assoc
     * @return array
     */
    private function arg($extra=[],$assoc = false){
            $arg = array_slice(explode('/',$this->uri->uri_string()),3);
            if($assoc && sizeof($arg) >= 2){
                $arg = [$this->toAssoc($arg)];
            }
            /*
             * if the url contains up to two fields after get then it should be assoc
             * if assoc isset then go ahead and make it assoc
             *
             * if extra not set just return arg isset
             * elseif arg is empty return an array containing extra
             * elseif arg is not empty create a new array containing the first element of arg and append extra to it
             */
            return $extra? $arg?[reset($arg),$extra]:[$extra] : $arg;
    }

    private function toAssoc(Array $array){
        foreach($array as $k => $v){
            if($k%2 == 0){
                $key[] = $v;
            }else{
                $val[] = $v;
            }
        }
        if(sizeof($key) != sizeof($val)){
            $val = array_pad($val,sizeof($val)+1,null);
        }
        return array_combine($key,$val);
    }


    public function errorFields(){
        foreach($_POST as $k => $v){
            if($m = $this->form_validation->error($k)){
                $this->errors([$k => strip_tags($m)]);
            }
        }
    }

    public function res(){
            $data = file_get_contents("php://input");
            //try to JSONDecode first
            try {
                return $this->jsonDecode($data);
            }catch(Exception $e){
                $m = explode('&', $data);
                foreach ($m as $k) {
                    $d = explode('=', $k);
                    $ky[] = urldecode(reset($d));
                    $vy[] = urldecode(end($d));
                }
                $n = array_combine($ky, $vy);
                return $n;
            }
    }


    public function jsonEncode($data,$option=32){
        $m = json_encode($data,$option);
        if(json_last_error()){
            throw new Exception(json_last_error_msg(),json_last_error());
        }else{
            return $m;
        }
    }

    public function jsonDecode($data){
        $m = json_decode($data);
        if(json_last_error()){
            throw new Exception(json_last_error_msg(),json_last_error());
        }else{
            return $m;
        }
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


    /*
    public function error_sanitize($err,$tags = false){
        $err = ($tags)?trim($err):trim(strip_tags($err));
        if(strpos($err,"\n")):
            return explode("\n",$err);
        else:
            return $err;
        endif;
    }
    */

    public function errors($err=[]){
        //grab the fields causing the errors
        $err = (is_array(reset($err)) && sizeof($err) == 1)?reset($err):$err;
            $this->_error =  !empty($err)? $this->_error = array_merge($this->_error,$err):$this->_error;
            return $this->_error;
    }

    public function req($value){
        $this->form_validation->set_message('!empty', 'field cannot be empty');
        $this->form_validation->set_message('req', 'please Provide %s');
        if($this->uri->segment(2) == 'update'):
            return true;
        else:
            return (bool) $value;
        endif;
    }

}