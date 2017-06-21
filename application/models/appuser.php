<?php

class Appuser extends MY_Model{
    public $error = Array();
 	const TABLE = 'user';
 	protected $hide = ['password'];

 	public function login($data){
        //get the user by email, compare password with hash
 	    if($result = $this->get(['email'=>$data['email']])){
            if(sizeof($result) ==1) {
                $result = $result[0];
                if (sha1($data['password']) === $result->password) {
                    return $result;
                } else {
                    array_push($this->error,['password'=>"incorrect password"]);
                    return false;
                }
            }
        }else{
 	        array_push($this->error,['email'=>"invalid email"]);
        }
    }
}

