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
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $role = $this->session->userdata('role');
        $id_user = $this->session->userdata('id_user');

        if ($role == 'admin') {
            // --- DATA UNTUK ADMIN ---

            // 1. Total Jenis Barang
            $data['total_barang'] = $this->db->count_all('tb_barang');

            // 2. Total Event Aktif (Belum Selesai)
            $this->db->where('status !=', 'selesai');
            $data['event_aktif'] = $this->db->count_all_results('tb_peminjaman');

            // 3. Total Barang Sedang Keluar (Dipinjam - Kembali)
            // Query: Sum (qty_pinjam - qty_kembali) dari detail peminjaman yang headernya belum selesai
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

            // 5. Riwayat Log Stok (5 Terakhir) - Mengambil dari tabel log yang baru kita bahas
            // Pastikan tabel tb_riwayat_stok sudah dibuat ya Kang
            $this->db->select('tb_riwayat_stok.*, tb_barang.nama_barang, tb_users.nama_lengkap');
            $this->db->from('tb_riwayat_stok');
            $this->db->join('tb_barang', 'tb_barang.id_barang = tb_riwayat_stok.id_barang');
            $this->db->join('tb_users', 'tb_users.id_user = tb_riwayat_stok.created_by');
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(5);
            $data['logs'] = $this->db->get()->result();
        } else {
            // --- DATA UNTUK OPERATOR ---

            // 1. Event Saya yang Aktif
            $this->db->where('id_operator', $id_user);
            $this->db->where('status !=', 'selesai');
            $data['my_active_events'] = $this->db->count_all_results('tb_peminjaman');

            // 2. Total Barang yang Saya Bawa
            $query_my_items = $this->db->query("
                SELECT SUM(d.qty_pinjam - d.qty_kembali) as total_bawa 
                FROM tb_peminjaman_detail d
                JOIN tb_peminjaman p ON p.id_peminjaman = d.id_peminjaman
                WHERE p.status != 'selesai' AND p.id_operator = $id_user
            ");
            $data['my_items'] = $query_my_items->row()->total_bawa ?? 0;

            // 3. Daftar Event Aktif (Tabel)
            $this->db->where('id_operator', $id_user);
            $this->db->where('status !=', 'selesai');
            $this->db->order_by('tgl_pinjam', 'ASC');
            $data['active_events_list'] = $this->db->get('tb_peminjaman')->result();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/v_dashboard', $data);
        $this->load->view('templates/footer');
    }
}
