<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Manajemen Produk',
            'active_group' => 'master',
            'active_menu' => 'products',
            'breadcrumb' => ['Master', 'Products'],
            'toolbar_actions' => '<a href="'.base_url('master/products/categories').'" class="btn btn-sm btn-light-primary"><i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>Kelola Kategori</a>',
            'css' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.css',
                'assets/custom/css/pages/master/products.css'
            ],
            'js' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.js',
                'assets/custom/js/pages/master/products.js'
            ]
        ];
        $this->render('pages/master/products', $data);
    }

    public function categories() {
        $data = [
            'title' => 'Kategori Produk',
            'active_group' => 'master',
            'active_menu' => 'products',
            'breadcrumb' => ['Master', 'Products', 'Categories'],
            'css' => [
                'assets/vendors/plugins/custom/jstree/jstree.bundle.css'
            ],
            'js' => [
                'assets/vendors/plugins/custom/jstree/jstree.bundle.js',
                'assets/custom/js/pages/master/categories.js'
            ]
        ];
        $this->render('pages/master/categories', $data);
    }

    public function create() {
        $data = [
            'title' => 'Tambah Produk Baru',
            'active_group' => 'master',
            'active_menu' => 'products',
            'breadcrumb' => ['Master', 'Products', 'Add Product'],
            'js' => ['assets/custom/js/inventory/products_form.js']
        ];
        $this->render('pages/master/products_form', $data);
    }

    public function edit($id) {
        // Mock data for UI development
        $product = [
            'id' => $id,
            'sku' => 'SKU-001',
            'name' => 'Kain Cotton Combed 30s',
            'description' => 'Bahan kaos berkualitas tinggi.',
            'category_id' => 1,
            'status' => 'active',
            'retail_price' => 50000,
            'internal_price' => 45000,
            'base_unit' => 'm',
            'sales_unit' => 'm',
            'conversion' => 1,
            'rop' => 10,
            'location' => 'Rak A1',
            'specs' => [
                'width' => '150cm',
                'grammage' => '150-160gsm',
                'composition' => '100% Cotton',
                'color' => 'Hitam',
                'motif' => 'Polos'
            ]
        ];

        $data = [
            'title' => 'Edit Produk',
            'active_group' => 'master',
            'active_menu' => 'products',
            'breadcrumb' => ['Master', 'Products', 'Edit Product'],
            'js' => ['assets/custom/js/inventory/products_form.js'],
            'product' => $product
        ];
        $this->render('pages/master/products_form', $data);
    }

    public function store() {
        // Handle store logic
    }

    public function update($id) {
        // Handle update logic
    }
}
