<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Kategori Produk',
            'active_group' => 'inventory',
            'active_menu' => 'categories',
            'breadcrumb' => ['Inventory', 'Categories']
        ];
        $this->render('pages/inventory/categories', $data);
    }
}
