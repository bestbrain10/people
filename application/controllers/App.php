<?php

class App extends My_Controller{
    //main controller since agnular will be running other shit
    public function index(){
        //if not logged in
        $this->loadview('register',['title'=>'Welcome']);
    }

    //template grabbing dude or maybe it own class


}