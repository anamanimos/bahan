<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Manajemen Produk',
            'active_group' => 'inventory',
            'active_menu' => 'products',
            'breadcrumb' => ['Inventory', 'Products']
        ];
        $this->render('pages/inventory/products', $data);
    }
}
