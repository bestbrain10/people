<?php

class My_Controller extends CI_Controller{
    /**
     * loads the view using the default layout
     * @param $view
     * @param $data
     */
    public function loadview($view, $data){
        @$data['nav'] = $data['nav'] || true;
        $data = array_merge($data,['view'=>$view]);
        $this->load->view('layout',$data);
    }


}