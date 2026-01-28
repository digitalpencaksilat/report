<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tracking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('M_inventory');

        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Tracking Barang Keluar';

        // Ambil data barang yang sedang dipinjam
        $data['barang_keluar'] = $this->M_inventory->get_barang_sedang_dipinjam();

        $this->load->view('templates/header', $data);
        $this->load->view('tracking/v_index', $data);
        $this->load->view('templates/footer');
    }
}
