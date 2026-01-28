<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // [PENTING] Set Timezone ke Asia/Jakarta (WIB)
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('M_inventory');

        // Cek Login
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        // Security: Hanya Admin yang boleh akses Master Barang
        if ($this->session->userdata('role') != 'admin') {
            show_404(); // Tampilkan 404 jika bukan admin
        }
    }

    public function index()
    {
        $data['title'] = 'Master Barang';
        $data['barang'] = $this->M_inventory->get_all_barang();
        $data['list_kategori'] = $this->M_inventory->get_all_kategori();

        $this->load->view('templates/header', $data);
        $this->load->view('barang/v_index', $data);
        $this->load->view('templates/footer');
    }

    // [BARU] Halaman History Pemakaian Barang
    // Diakses via tombol "History" (ikon jam) di tabel Master Barang
    public function history($id_barang)
    {
        $data['title'] = 'Riwayat Pemakaian Barang';

        // Ambil nama barang untuk judul
        $data['nama_barang'] = $this->M_inventory->get_nama_barang($id_barang);

        // Ambil data timeline penggunaan dari model
        $data['riwayat'] = $this->M_inventory->get_riwayat_pemakaian_barang($id_barang);

        $this->load->view('templates/header', $data);
        $this->load->view('barang/v_history', $data);
        $this->load->view('templates/footer');
    }

    public function proses_tambah()
    {
        $kategori = $this->input->post('kategori');

        // Generate kode otomatis (misal LPT-001)
        $kode_otomatis = $this->M_inventory->generate_kode_barang($kategori);
        $stok_awal = $this->input->post('stok_total');

        $data = [
            'kode_barang'   => $kode_otomatis,
            'nama_barang'   => $this->input->post('nama_barang'),
            'kategori'      => $kategori,
            'stok_total'    => $stok_awal,
            'stok_tersedia' => $stok_awal, // Awalnya stok tersedia = total
            'kondisi'       => 'baik'
        ];

        if ($this->M_inventory->insert_barang($data)) {
            // Catat Log Stok Awal
            $id_barang_baru = $this->db->insert_id();
            $this->catat_log_stok($id_barang_baru, 0, $stok_awal, 'Barang Baru (Initial Stock)');

            $this->session->set_flashdata('success', 'Data barang berhasil ditambahkan dengan Kode: <b>' . $kode_otomatis . '</b>');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data barang.');
        }

        redirect('barang');
    }

    public function proses_edit()
    {
        $id_barang = $this->input->post('id_barang');

        // Ambil data lama untuk hitung selisih stok
        $barang_lama = $this->db->get_where('tb_barang', ['id_barang' => $id_barang])->row();

        $stok_total_baru = $this->input->post('stok_total');
        $stok_total_lama = $barang_lama->stok_total;

        // Hitung selisih
        $selisih = $stok_total_baru - $stok_total_lama;
        // Sesuaikan stok tersedia
        $stok_tersedia_baru = $barang_lama->stok_tersedia + $selisih;

        $data = [
            'nama_barang'   => $this->input->post('nama_barang'),
            'kategori'      => $this->input->post('kategori'),
            'stok_total'    => $stok_total_baru,
            'stok_tersedia' => $stok_tersedia_baru
        ];

        if ($this->M_inventory->update_barang($id_barang, $data)) {
            // Catat Log Stok hanya jika ada perubahan jumlah
            if ($selisih != 0) {
                $keterangan = ($selisih > 0) ? 'Penambahan Stok / Restock' : 'Koreksi Stok (Pengurangan)';
                $this->catat_log_stok($id_barang, $stok_total_lama, $selisih, $keterangan);
            }

            $this->session->set_flashdata('success', 'Data barang berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data.');
        }

        redirect('barang');
    }

    public function hapus($id)
    {
        if ($this->M_inventory->delete_barang($id)) {
            $this->session->set_flashdata('success', 'Data barang berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data.');
        }
        redirect('barang');
    }

    // Helper Function Log (Private)
    private function catat_log_stok($id_barang, $qty_awal, $qty_perubahan, $keterangan)
    {
        $data_log = [
            'id_barang'     => $id_barang,
            'qty_awal'      => $qty_awal,
            'qty_perubahan' => $qty_perubahan,
            'qty_akhir'     => $qty_awal + $qty_perubahan,
            'keterangan'    => $keterangan,
            'created_by'    => $this->session->userdata('id_user'),
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tb_riwayat_stok', $data_log);
    }
}
