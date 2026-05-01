<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

    public function tree() {
        // Mock hierarchical data
        $data = [
            [
                'id' => 'cat_1',
                'parent' => '#',
                'text' => 'Kain Rajut',
                'state' => ['opened' => true],
                'type' => 'root'
            ],
            [
                'id' => 'cat_2',
                'parent' => 'cat_1',
                'text' => 'Cotton Combed',
                'type' => 'child'
            ],
            [
                'id' => 'cat_3',
                'parent' => 'cat_2',
                'text' => '30s',
                'type' => 'subchild'
            ],
            [
                'id' => 'cat_4',
                'parent' => 'cat_2',
                'text' => '24s',
                'type' => 'subchild'
            ],
            [
                'id' => 'cat_5',
                'parent' => '#',
                'text' => 'Kain Tenun',
                'state' => ['opened' => true],
                'type' => 'root'
            ],
            [
                'id' => 'cat_6',
                'parent' => 'cat_5',
                'text' => 'Toyobo',
                'type' => 'child'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    public function move() {
        $id = $this->input->post('id');
        $parent = $this->input->post('parent');
        $position = $this->input->post('position');

        // Logic to update DB would go here
        
        $response = [
            'status' => 'success',
            'message' => 'Hirarki kategori berhasil diperbarui'
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }
}
