<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Cek Login
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
        $this->load->model('M_inventory');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $role = $this->session->userdata('role');
        $id_user = $this->session->userdata('id_user');

        if ($role == 'admin') {
            // --- DATA UNTUK ADMIN (DIPERTAHANKAN SESUAI REQUEST) ---

            // 1. Total Jenis Barang
            $data['total_barang'] = $this->db->count_all('tb_barang');

            // 2. Total Event Aktif (Belum Selesai)
            $this->db->where('status !=', 'selesai');
            $data['event_aktif'] = $this->db->count_all_results('tb_peminjaman');

            // 3. Total Barang Sedang Keluar (Dipinjam - Kembali)
            $query_keluar = $this->db->query("
                SELECT SUM(d.qty_pinjam - d.qty_kembali) as total_keluar 
                FROM tb_peminjaman_detail d
                JOIN tb_peminjaman p ON p.id_peminjaman = d.id_peminjaman
                WHERE p.status != 'selesai'
            ");
            $data['barang_keluar'] = $query_keluar->row()->total_keluar ?? 0;

            // 4. Stok Menipis (Stok Tersedia < 5)
            $this->db->where('stok_tersedia <=', 5);
            $data['stok_kritis'] = $this->db->count_all_results('tb_barang');

            // 5. Riwayat Log Stok (5 Terakhir)
            $this->db->select('tb_riwayat_stok.*, tb_barang.nama_barang, tb_users.nama_lengkap');
            $this->db->from('tb_riwayat_stok');
            $this->db->join('tb_barang', 'tb_barang.id_barang = tb_riwayat_stok.id_barang');
            $this->db->join('tb_users', 'tb_users.id_user = tb_riwayat_stok.created_by');
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(5);
            $data['logs'] = $this->db->get()->result();
        } else {
            // --- DATA UNTUK OPERATOR (DIPERBAIKI UNTUK PENDAMPING) ---

            // 1. Event Saya yang Aktif (Utama ATAU Pendamping)
            $this->db->where('status !=', 'selesai');
            $this->db->group_start(); // Mulai Grouping OR
            $this->db->where('id_operator', $id_user); // Cek Operator Utama
            $this->db->or_like('petugas_tambahan', '"' . $id_user . '"'); // Cek Pendamping (JSON)
            $this->db->group_end(); // Tutup Grouping
            $data['my_active_events'] = $this->db->count_all_results('tb_peminjaman');

            // 2. Total Barang yang Saya Bawa
            // Menggunakan Logic OR pada SQL manual
            // Kita cari string ID user dalam format JSON (misal "5")
            $json_id_search = '%"' . $id_user . '"%';

            $query_my_items = $this->db->query("
                SELECT SUM(d.qty_pinjam - d.qty_kembali) as total_bawa 
                FROM tb_peminjaman_detail d
                JOIN tb_peminjaman p ON p.id_peminjaman = d.id_peminjaman
                WHERE p.status != 'selesai' 
                AND (p.id_operator = '$id_user' OR p.petugas_tambahan LIKE '$json_id_search')
            ");
            $data['my_items'] = $query_my_items->row()->total_bawa ?? 0;

            // 3. Daftar Event Aktif (Tabel)
            $this->db->where('status !=', 'selesai');
            $this->db->group_start(); // Mulai Grouping OR
            $this->db->where('id_operator', $id_user);
            $this->db->or_like('petugas_tambahan', '"' . $id_user . '"');
            $this->db->group_end(); // Tutup Grouping

            $this->db->order_by('tgl_pinjam', 'ASC');
            $data['active_events_list'] = $this->db->get('tb_peminjaman')->result();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/v_dashboard', $data);
        $this->load->view('templates/footer');
    }
}
