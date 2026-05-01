<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

    public function index() {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }
        $this->render_simple('pages/auth/login');
    }

    public function verify_sso() {
        // Logika verifikasi SSO sesuai PRD 6.8.3
        $token = $this->input->get('token');
        $email = $this->input->get('email');
        
        // Simulasi verifikasi sukses
        if ($token && $email) {
            $this->session->set_userdata([
                'user_id' => 1,
                'email' => $email,
                'role' => 'Admin'
            ]);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Token SSO tidak valid atau sudah kadaluarsa.');
            redirect('auth/login');
        }
    }
    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }
}
