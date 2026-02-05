<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load model dan library yang dibutuhkan
        $this->load->model('M_inventory');
        $this->load->library('session');
    }

    public function index()
    {
        // [UPDATE] Cek jika user sudah login, redirect sesuai ROLE
        if ($this->session->userdata('id_user')) {
            $role = $this->session->userdata('role');
            if ($role == 'bendahara') {
                redirect('bendahara');
            } else {
                redirect('dashboard');
            }
        }
        $this->load->view('v_login');
    }

    public function process()
    {
        // Ambil input dengan XSS filtering (TRUE)
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        // Cek user di database lewat Model
        $user = $this->M_inventory->cek_login($username);

        if ($user) {
            // Cek Password menggunakan Hash (password_verify)
            if (password_verify($password, $user->password)) {

                // Set Session Data (Sesuai file lama Anda)
                $session_data = [
                    'id_user'   => $user->id_user,
                    'nama'      => $user->nama_lengkap,
                    'role'      => $user->role, // 'admin', 'operator', atau 'bendahara'
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($session_data);

                // [SWEETALERT] Set Flashdata untuk Login Berhasil
                $this->session->set_flashdata('swal_icon', 'success');
                $this->session->set_flashdata('swal_title', 'Login Berhasil!');
                $this->session->set_flashdata('swal_text', 'Selamat datang kembali, ' . $user->nama_lengkap);

                // [UPDATE LOGIC] Redirect Berdasarkan Role
                if ($user->role == 'bendahara') {
                    redirect('bendahara');
                } else {
                    redirect('dashboard');
                }
            } else {
                // Password Salah
                $this->session->set_flashdata('error', 'Password yang Anda masukkan salah.');
                redirect('auth');
            }
        } else {
            // Username Tidak Ditemukan
            $this->session->set_flashdata('error', 'Username tidak terdaftar.');
            redirect('auth');
        }
    }

    public function logout()
    {
        // Hapus sesi spesifik
        $items = ['id_user', 'nama', 'role', 'logged_in'];
        $this->session->unset_userdata($items);

        // [SWEETALERT] Set Flashdata untuk Logout
        $this->session->set_flashdata('swal_icon', 'success');
        $this->session->set_flashdata('swal_title', 'Berhasil Logout');
        $this->session->set_flashdata('swal_text', 'Anda telah keluar dari sistem.');

        redirect('auth');
    }
}
