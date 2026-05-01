<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stocks extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

    public function list() {
        // Mock stock data
        $data = [
            [
                'id' => 1,
                'sku' => 'KRN-001',
                'name' => 'Cotton Combed 30s',
                'category' => 'Kain Rajut',
                'stock' => 125.5,
                'unit' => 'Meter',
                'location' => 'Rak A1',
                'min_stock' => 50,
                'status' => 'In Stock'
            ],
            [
                'id' => 2,
                'sku' => 'KTN-001',
                'name' => 'Toyobo Fodu',
                'category' => 'Kain Tenun',
                'stock' => 45.0,
                'unit' => 'Meter',
                'location' => 'Rak B2',
                'min_stock' => 100,
                'status' => 'Low Stock'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw" => 1,
                "recordsTotal" => 2,
                "recordsFiltered" => 2,
                "data" => $data
            ]));
    }

    public function pr_list() {
        // Mock PR data with new statuses and total estimation
        $data = [
            ['id' => 'PR-20260501-001', 'date' => '2026-05-01', 'staff' => 'Staff Gudang A', 'items_count' => 5, 'total_est' => 575000, 'status' => 'Menunggu Review'],
            ['id' => 'PR-20260430-008', 'date' => '2026-04-30', 'staff' => 'Staff Gudang B', 'items_count' => 2, 'total_est' => 1250000, 'status' => 'Sudah Review'],
            ['id' => 'PR-20260430-007', 'date' => '2026-04-30', 'staff' => 'Admin Gudang', 'items_count' => 3, 'total_est' => 450000, 'status' => 'Draft'],
            ['id' => 'PR-20260429-012', 'date' => '2026-04-29', 'staff' => 'Staff Gudang A', 'items_count' => 10, 'total_est' => 8900000, 'status' => 'Sudah Review'],
            ['id' => 'PR-20260429-011', 'date' => '2026-04-29', 'staff' => 'Staff Gudang C', 'items_count' => 1, 'total_est' => 150000, 'status' => 'Menunggu Review'],
            ['id' => 'PR-20260428-005', 'date' => '2026-04-28', 'staff' => 'Admin Gudang', 'items_count' => 4, 'total_est' => 2300000, 'status' => 'Sudah Review'],
            ['id' => 'PR-20260428-004', 'date' => '2026-04-28', 'staff' => 'Staff Gudang B', 'items_count' => 6, 'total_est' => 750000, 'status' => 'Draft'],
            ['id' => 'PR-20260427-009', 'date' => '2026-04-27', 'staff' => 'Staff Gudang A', 'items_count' => 8, 'total_est' => 4200000, 'status' => 'Sudah Review'],
            ['id' => 'PR-20260427-003', 'date' => '2026-04-27', 'staff' => 'Staff Gudang C', 'items_count' => 2, 'total_est' => 320000, 'status' => 'Menunggu Review'],
            ['id' => 'PR-20260426-001', 'date' => '2026-04-26', 'staff' => 'Admin Gudang', 'items_count' => 5, 'total_est' => 1100000, 'status' => 'Sudah Review'],
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            ]));
    }

    public function submit_pr() {
        $product_ids = $this->input->post('product_id');
        $supplier_ids = $this->input->post('supplier_id');

        // Logic simulation: If ID is not numeric, it's a new entry to be added to DB
        $new_products_count = 0;
        $new_suppliers_count = 0;

        if ($product_ids) {
            foreach ($product_ids as $id) {
                if (!is_numeric($id) && strpos($id, 'new_') === false) $new_products_count++;
            }
        }

        if ($supplier_ids) {
            foreach ($supplier_ids as $id) {
                if (!is_numeric($id)) $new_suppliers_count++;
            }
        }

        $response = [
            'status' => 'success',
            'message' => 'Pengajuan Beli (PR) berhasil diajukan.' . 
                         ($new_suppliers_count > 0 ? " {$new_suppliers_count} supplier baru otomatis didaftarkan." : "") .
                         ($new_products_count > 0 ? " {$new_products_count} produk baru terdaftar sebagai draft." : "")
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function submit_gr() {
        // GR submission logic
        $response = [
            'status' => 'success',
            'message' => 'Input Nota (GR) berhasil disimpan, stok telah diperbarui'
        ];
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function search_products() {
        $q = $this->input->get('q');
        // Mock product data from master
        $products = [
            ['id' => 1, 'text' => '[KRN-001] Cotton Combed 30s - Putih'],
            ['id' => 2, 'text' => '[KTN-001] Toyobo Fodu - Navy'],
            ['id' => 3, 'text' => '[KRN-002] Cotton Combed 24s - Hitam'],
        ];

        if ($q) {
            $products = array_filter($products, function($p) use ($q) {
                return strpos(strtolower($p['text']), strtolower($q)) !== false;
            });
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "results" => array_values($products)
            ]));
    }

    public function search_suppliers() {
        $q = $this->input->get('q');
        // Mock supplier data
        $suppliers = [
            ['id' => 1, 'text' => 'Toko Subur Makmur'],
            ['id' => 2, 'text' => 'CV. Tekstil Jaya'],
            ['id' => 3, 'text' => 'PT. Bahan Utama'],
        ];

        if ($q) {
            $suppliers = array_filter($suppliers, function($s) use ($q) {
                return strpos(strtolower($s['text']), strtolower($q)) !== false;
            });
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "results" => array_values($suppliers)
            ]));
    }
    public function search_orders() {
        $q = $this->input->get('q');
        // Mock order data from ERP
        $orders = [
            ['id' => 101, 'text' => 'ORD-2026-001 [T-Shirt Polos]'],
            ['id' => 102, 'text' => 'ORD-2026-002 [Hoodie Zipper]'],
            ['id' => 103, 'text' => 'ORD-2026-003 [Kemeja Flanel]'],
        ];

        if ($q) {
            $orders = array_filter($orders, function($o) use ($q) {
                return strpos(strtolower($o['text']), strtolower($q)) !== false;
            });
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "results" => array_values($orders)
            ]));
    }

    public function pr_approval_list() {
        // Mock pending PRs for approval
        $data = [
            [
                'id' => 'PR-20260501-001',
                'date' => '2026-05-01',
                'staff' => 'Staff Gudang A',
                'total_est' => 575000,
                'status' => 'Pending Approval'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw" => 1,
                "recordsTotal" => 1,
                "recordsFiltered" => 1,
                "data" => $data
            ]));
    }

    public function gr_list() {
        $data = [
            ['id' => 'GR-20260501-001', 'pr_id' => 'PR-20260425-001', 'date' => '2026-05-01', 'supplier' => 'Toko Subur Makmur', 'items_count' => 12, 'warehouse' => 'Gudang Utama'],
            ['id' => 'GR-20260501-002', 'pr_id' => 'PR-20260425-002', 'date' => '2026-05-01', 'supplier' => 'CV. Tekstil Jaya', 'items_count' => 5, 'warehouse' => 'Gudang Bahan'],
            ['id' => 'GR-20260430-005', 'pr_id' => 'PR-20260420-010', 'date' => '2026-04-30', 'supplier' => 'PT. Bahan Utama', 'items_count' => 25, 'warehouse' => 'Gudang Utama'],
            ['id' => 'GR-20260429-012', 'pr_id' => 'PR-20260418-005', 'date' => '2026-04-29', 'supplier' => 'Supplier A', 'items_count' => 8, 'warehouse' => 'Gudang Aksesoris'],
            ['id' => 'GR-20260428-009', 'pr_id' => 'PR-20260415-012', 'date' => '2026-04-28', 'supplier' => 'CV Maju Jaya', 'items_count' => 15, 'warehouse' => 'Gudang Bahan'],
            ['id' => 'GR-20260427-003', 'pr_id' => 'PR-20260410-003', 'date' => '2026-04-27', 'supplier' => 'Supplier B', 'items_count' => 4, 'warehouse' => 'Gudang Utama'],
            ['id' => 'GR-20260426-001', 'pr_id' => 'PR-20260405-001', 'date' => '2026-04-26', 'supplier' => 'PT. Bahan Utama', 'items_count' => 10, 'warehouse' => 'Gudang Bahan'],
            ['id' => 'GR-20260425-008', 'pr_id' => 'PR-20260328-009', 'date' => '2026-04-25', 'supplier' => 'CV. Tekstil Jaya', 'items_count' => 20, 'warehouse' => 'Gudang Utama'],
            ['id' => 'GR-20260424-004', 'pr_id' => 'PR-20260325-004', 'date' => '2026-04-24', 'supplier' => 'Toko Subur Makmur', 'items_count' => 6, 'warehouse' => 'Gudang Aksesoris'],
            ['id' => 'GR-20260423-011', 'pr_id' => 'PR-20260320-011', 'date' => '2026-04-23', 'supplier' => 'CV Maju Jaya', 'items_count' => 12, 'warehouse' => 'Gudang Utama'],
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            ]));
    }
}
