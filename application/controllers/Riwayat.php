<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Riwayat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Set Timezone
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('M_inventory');

        // Cek Login
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        // Security Check: Hanya Admin yang boleh akses halaman ini
        // Operator biasa tidak boleh melihat kinerja operator lain
        if ($this->session->userdata('role') != 'admin') {
            redirect('dashboard');
        }
    }

    // Halaman Index: Menampilkan daftar semua operator
    public function index()
    {
        $data['title'] = 'Daftar Kinerja Operator';

        // Ambil semua user dengan role 'operator'
        $data['operators'] = $this->M_inventory->get_all_operators();

        $this->load->view('templates/header', $data);
        $this->load->view('riwayat/v_index', $data);
        $this->load->view('templates/footer');
    }

    // Halaman Detail: Menampilkan riwayat tugas spesifik satu operator
    // Mendukung Filter Bulan & Tahun via URL Parameter (GET)
    public function user($id_user)
    {
        // Ambil data operator berdasarkan ID
        $operator = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();

        // Jika user tidak ditemukan, tampilkan 404
        if (!$operator) {
            show_404();
        }

        $data['title'] = 'Riwayat Tugas: ' . $operator->nama_lengkap;
        $data['operator'] = $operator;

        // --- LOGIKA FILTER ---
        // Ambil parameter dari URL, misal: riwayat/user/5?bulan=10&tahun=2023
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        // Validasi sederhana: Jika kosong, set null (artinya tampilkan semua)
        if (empty($bulan)) $bulan = null;
        if (empty($tahun)) $tahun = null;

        // Panggil Model dengan parameter filter
        $events = $this->M_inventory->get_riwayat_tugas_operator($id_user, $bulan, $tahun);

        // --- HITUNG STATISTIK (Berdasarkan data yang difilter) ---
        $total_event = count($events);
        $total_hari = 0;

        foreach ($events as $e) {
            // Kolom durasi_hari sudah dihitung otomatis oleh MySQL (DATEDIFF) di Model
            $total_hari += $e->durasi_hari;
        }

        // Siapkan data untuk View
        $data['events'] = $events;
        $data['stats'] = [
            'total_event' => $total_event,
            'total_hari' => $total_hari
        ];

        // Kirim balik data filter ke view agar dropdown tetap terpilih setelah submit
        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        $this->load->view('riwayat/v_detail', $data);
        $this->load->view('templates/footer');
    }
}
