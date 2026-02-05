<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gaji extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('M_keuangan');

        // 1. Cek Login
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        // 2. Cek Role: Pastikan dia Operator (atau Admin boleh intip juga)
        // Tapi utamanya ini untuk Operator melihat gajinya sendiri
    }

    // Halaman List Riwayat Gaji
    public function index()
    {
        $id_user = $this->session->userdata('id_user');

        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        // Default tahun ini
        if (empty($tahun)) $tahun = date('Y');

        $data['title'] = 'Riwayat Pendapatan Saya';
        $data['user']  = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();

        // Gunakan fungsi yang sudah ada di M_keuangan, tapi kunci id_user-nya
        $data['items'] = $this->M_keuangan->get_detail_payroll_user($id_user, $bulan, $tahun);

        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        $this->load->view('operator/v_gaji_list', $data);
        $this->load->view('templates/footer');
    }

    // Halaman Detail Slip (Reuse View Bendahara)
    public function slip($id_keuangan)
    {
        $id_user = $this->session->userdata('id_user');

        // Ambil data slip spesifik
        $item = $this->M_keuangan->get_single_payroll_item($id_keuangan, $id_user);

        // Security Check: Jika data tidak ditemukan (artinya bukan milik user ini), tolak
        if (!$item) {
            show_404();
        }

        $data['title'] = 'Slip Gaji Event: ' . $item->kode_transaksi;
        $data['slip'] = $item;

        $this->load->view('templates/header', $data);
        // Kita gunakan view yang sama persis dengan Bendahara agar desainnya konsisten
        $this->load->view('bendahara/v_slip_event', $data);
        $this->load->view('templates/footer');
    }
}
