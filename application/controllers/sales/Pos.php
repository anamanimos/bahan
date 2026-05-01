<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = [
            'title' => 'Point of Sale (POS)',
            'active_group' => 'sales',
            'active_menu' => 'pos',
            'breadcrumb' => ['Sales', 'POS'],
            // Menambahkan JS eksternal khusus halaman POS sesuai STANDART.md
            'js' => ['pages/sales/pos.js'] 
        ];
        $this->render('pages/sales/pos', $data);
    }
}
