<?php

class Home extends My_Controller{
    public function index(){
        $this->loadview('register',['title'=>'Welcome']);
    }


}