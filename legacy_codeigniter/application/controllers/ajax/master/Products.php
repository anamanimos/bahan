<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

    public function list() {
        // Mock data from the original controller (expanded to support server-side features)
        $all_products = [
            ['id' => 1, 'name' => 'Cotton Combed 30s', 'sku' => 'KNB-001', 'stock' => 25, 'category' => 'Kain Rajut', 'color' => 'Hitam', 'color_hex' => '#000000', 'image' => '1.png'],
            ['id' => 2, 'name' => 'Cotton Combed 24s', 'sku' => 'KNB-002', 'stock' => 15, 'category' => 'Kain Rajut', 'color' => 'Putih', 'color_hex' => '#FFFFFF', 'image' => '2.png'],
            ['id' => 3, 'name' => 'Fleece Cotton', 'sku' => 'KNB-003', 'stock' => 10, 'category' => 'Kain Rajut', 'color' => 'Navy', 'color_hex' => '#000080', 'image' => '3.png'],
            ['id' => 4, 'name' => 'Linen Euro', 'sku' => 'TKN-001', 'stock' => 40, 'category' => 'Kain Tenun', 'color' => 'Cream', 'color_hex' => '#FFFDD0', 'image' => '4.png'],
            ['id' => 5, 'name' => 'Toyobo Fodu', 'sku' => 'TKN-002', 'stock' => 30, 'category' => 'Kain Tenun', 'color' => 'Maroon', 'color_hex' => '#800000', 'image' => '5.png'],
            ['id' => 6, 'name' => 'Baby Terry', 'sku' => 'KNB-004', 'stock' => 20, 'category' => 'Kain Rajut', 'color' => 'Abu Muda', 'color_hex' => '#D3D3D3', 'image' => '6.png'],
            ['id' => 7, 'name' => 'Oxford Denim', 'sku' => 'TKN-003', 'stock' => 50, 'category' => 'Kain Tenun', 'color' => 'Blue Denim', 'color_hex' => '#5D8AA8', 'image' => '7.png'],
            ['id' => 8, 'name' => 'CVC Lacoste', 'sku' => 'KNB-005', 'stock' => 12, 'category' => 'Kain Rajut', 'color' => 'Hijau Botol', 'color_hex' => '#006A4E', 'image' => '8.png'],
            ['id' => 9, 'name' => 'Rayon Twill', 'sku' => 'TKN-004', 'stock' => 45, 'category' => 'Kain Tenun', 'color' => 'Terracotta', 'color_hex' => '#E2725B', 'image' => '9.png'],
            ['id' => 10, 'name' => 'Spandex Balon', 'sku' => 'KNB-006', 'stock' => 35, 'category' => 'Kain Rajut', 'color' => 'Ungu', 'color_hex' => '#800080', 'image' => '10.png'],
            ['id' => 11, 'name' => 'Drill Unione', 'sku' => 'TKN-005', 'stock' => 60, 'category' => 'Kain Tenun', 'color' => 'Khaki', 'color_hex' => '#C3B091', 'image' => '11.png'],
            ['id' => 12, 'name' => 'Scuba Premium', 'sku' => 'KNB-007', 'stock' => 18, 'category' => 'Kain Rajut', 'color' => 'Pink', 'color_hex' => '#FFC0CB', 'image' => '12.png'],
            ['id' => 13, 'name' => 'Polyester Mesh', 'sku' => 'KNB-008', 'stock' => 100, 'category' => 'Kain Rajut', 'color' => 'Neon Green', 'color_hex' => '#39FF14', 'image' => '13.png'],
            ['id' => 14, 'name' => 'Jacquard Silk', 'sku' => 'TKN-006', 'stock' => 5, 'category' => 'Kain Tenun', 'color' => 'Gold', 'color_hex' => '#FFD700', 'image' => '14.png'],
            ['id' => 15, 'name' => 'Canvas Marsoto', 'sku' => 'TKN-007', 'stock' => 22, 'category' => 'Kain Tenun', 'color' => 'Broken White', 'color_hex' => '#F8F8FF', 'image' => '15.png'],
            ['id' => 16, 'name' => 'Ripstop Tornado', 'sku' => 'TKN-008', 'stock' => 33, 'category' => 'Kain Tenun', 'color' => 'Army Green', 'color_hex' => '#4B5320', 'image' => '16.png'],
            ['id' => 17, 'name' => 'Jersey ITY', 'sku' => 'KNB-009', 'stock' => 28, 'category' => 'Kain Rajut', 'color' => 'Mustard', 'color_hex' => '#FFDB58', 'image' => '17.png'],
            ['id' => 18, 'name' => 'Satin Velvet', 'sku' => 'TKN-009', 'stock' => 42, 'category' => 'Kain Tenun', 'color' => 'Lavender', 'color_hex' => '#E6E6FA', 'image' => '18.png'],
            ['id' => 19, 'name' => 'Crinkle Airflow', 'sku' => 'TKN-010', 'stock' => 55, 'category' => 'Kain Tenun', 'color' => 'Sage Green', 'color_hex' => '#9C9F84', 'image' => '19.png'],
            ['id' => 20, 'name' => 'Rib Knit', 'sku' => 'KNB-010', 'stock' => 14, 'category' => 'Kain Rajut', 'color' => 'Cokelat', 'color_hex' => '#D2B48C', 'image' => '20.png'],
        ];

        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        $order = $this->input->post('order');
        $columns = $this->input->post('columns');
        $filters = $this->input->post('filters');

        // Advanced Filtering
        if (!empty($filters)) {
            // Filter by categories
            if (!empty($filters['categories'])) {
                $categories = array_filter((array) $filters['categories']);
                if (!empty($categories)) {
                    $all_products = array_filter($all_products, function($p) use ($categories) {
                        return in_array($p['category'], $categories);
                    });
                }
            }

            // Filter by colors
            if (!empty($filters['colors'])) {
                $colors = array_filter((array) $filters['colors']);
                if (!empty($colors)) {
                    $all_products = array_filter($all_products, function($p) use ($colors) {
                        return in_array($p['color'], $colors);
                    });
                }
            }
        }

        // Simple search filtering
        if (!empty($search)) {
            $all_products = array_filter($all_products, function($p) use ($search) {
                return stripos($p['name'], $search) !== false || stripos($p['sku'], $search) !== false;
            });
        }

        // Sorting logic
        if (!empty($order)) {
            $col_idx = $order[0]['column'];
            $col_dir = $order[0]['dir'];
            $col_name = $columns[$col_idx]['data'];

            usort($all_products, function($a, $b) use ($col_name, $col_dir) {
                $val_a = $a[$col_name];
                $val_b = $b[$col_name];

                if (is_numeric($val_a) && is_numeric($val_b)) {
                    $result = $val_a <=> $val_b;
                } else {
                    $result = strcasecmp($val_a, $val_b);
                }

                return ($col_dir === 'asc') ? $result : -$result;
            });
        }

        $total_records = 20;
        $filtered_records = count($all_products);
        $data = array_slice($all_products, $start, $length);

        $response = [
            "status" => "success",
            "message" => "Data produk berhasil diambil",
            "draw" => intval($draw),
            "recordsTotal" => $total_records,
            "recordsFiltered" => $filtered_records,
            "data" => array_values($data)
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }
}
