<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        // Load session or other common libraries here
    }

    protected function render($page, $data = []) {
        $data['page'] = $page;
        $this->load->view('layouts/master', $data);
    }

    protected function render_simple($page, $data = []) {
        $this->load->view($page, $data);
    }
}

