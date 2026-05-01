<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Requests extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Permintaan Internal (Material Request)',
            'active_group' => 'sales',
            'active_menu' => 'requests',
            'breadcrumb' => ['Sales', 'Material Request']
        ];
        $this->render('pages/sales/requests', $data);
    }
}
