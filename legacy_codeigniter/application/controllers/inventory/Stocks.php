<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stocks extends MY_Controller {

    public function index() {
        $data = [
            'title' => 'Dashboard Stok',
            'active_group' => 'inventory',
            'active_menu' => 'stocks',
            'breadcrumb' => ['Inventori', 'Dashboard Stok'],
            'css' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.css'
            ],
            'js' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.js',
                'assets/custom/js/pages/inventory/stocks.js'
            ]
        ];
        $this->render('pages/inventory/stocks', $data);
    }

    public function pr() {
        $data = [
            'title' => 'Daftar Pengajuan Beli (PR)',
            'active_group' => 'inventory',
            'active_menu' => 'pr',
            'breadcrumb' => ['Inventori', 'Daftar PR'],
            'css' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.css'
            ],
            'js' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.js',
                'assets/custom/js/pages/inventory/pr_list.js'
            ]
        ];
        $this->render('pages/inventory/pr_list', $data);
    }

    public function pr_create() {
        $data = [
            'title' => 'Buat Pengajuan Beli (PR)',
            'active_group' => 'inventory',
            'active_menu' => 'pr',
            'breadcrumb' => ['Inventori', 'Buat PR'],
            'js' => [
                'assets/custom/js/pages/inventory/pr_form.js'
            ]
        ];
        $this->render('pages/inventory/pr_form', $data);
    }

    public function pr_approval_detail($id = null) {
        if (!$id) redirect('inventory/purchase-requisition/approval');
        
        $data = [
            'title' => 'Detail Persetujuan PR: ' . $id,
            'active_group' => 'inventory',
            'active_menu' => 'pr_approval',
            'pr_id' => $id,
            'breadcrumb' => ['Inventori', 'Persetujuan PR', 'Detail'],
            'js' => [
                'assets/custom/js/pages/inventory/pr_approval_detail.js'
            ]
        ];
        $this->render('pages/inventory/pr_approval_detail', $data);
    }

    public function gr_list() {
        $data = [
            'title' => 'Daftar Penerimaan Barang (GR)',
            'active_group' => 'inventory',
            'active_menu' => 'gr',
            'breadcrumb' => ['Inventori', 'Daftar GR'],
            'css' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.css'
            ],
            'js' => [
                'assets/vendors/plugins/custom/datatables/datatables.bundle.js',
                'assets/custom/js/pages/inventory/gr_list.js?v=1.0.1'
            ]
        ];
        $this->render('pages/inventory/gr_list', $data);
    }

    public function gr_create() {
        $data = [
            'title' => 'Input Penerimaan Barang (GR)',
            'active_group' => 'inventory',
            'active_menu' => 'gr',
            'breadcrumb' => ['Inventori', 'Input GR'],
            'js' => [
                'assets/custom/js/pages/inventory/gr_form.js?v=1.0.7'
            ]
        ];
        $this->render('pages/inventory/gr_form', $data);
    }
}
