<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Dashboard Utama',
            'active_menu' => 'dashboard',
            'breadcrumb' => ['Dashboard']
        ];
        $this->render('pages/dashboard/index', $data);
    }
}
