<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_inventory extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Set Timezone default model
        date_default_timezone_set('Asia/Jakarta');
    }

    // ==========================================================
    // LOGIC AUTHENTICATION & USER MANAGEMENT
    // ==========================================================

    public function cek_login($username)
    {
        return $this->db->get_where('tb_users', ['username' => $username])->row();
    }

    public function get_all_users()
    {
        return $this->db->get('tb_users')->result();
    }

    // Ambil user yang role-nya operator (untuk dropdown penugasan)
    public function get_all_operators()
    {
        return $this->db->get_where('tb_users', ['role' => 'operator'])->result();
    }

    public function insert_user($data)
    {
        return $this->db->insert('tb_users', $data);
    }

    public function update_user($id, $data)
    {
        $this->db->where('id_user', $id);
        return $this->db->update('tb_users', $data);
    }

    public function delete_user($id)
    {
        $this->db->where('id_user', $id);
        return $this->db->delete('tb_users');
    }

    // ==========================================================
    // LOGIC MASTER BARANG
    // ==========================================================

    public function get_all_barang()
    {
        return $this->db->get('tb_barang')->result();
    }

    public function get_all_kategori()
    {
        $this->db->order_by('nama_kategori', 'ASC');
        return $this->db->get('tb_kategori')->result();
    }

    // Helper: Ambil nama barang by ID (untuk judul history)
    public function get_nama_barang($id_barang)
    {
        $q = $this->db->get_where('tb_barang', ['id_barang' => $id_barang])->row();
        return $q ? $q->nama_barang : 'Unknown Item';
    }

    // Generator Kode Barang Otomatis (Format: PRE-001)
    public function generate_kode_barang($nama_kategori)
    {
        $prefix = 'INV';
        $mapping = [
            'Laptop' => 'LPT',
            'Tablet' => 'TAB',
            'Charger' => 'CHG',
            'Fan' => 'FAN',
            'Keyboard' => 'KEY',
            'Printer' => 'PRN',
            'Terminal Charger' => 'TRM',
            'Tripod' => 'TRP',
            'Router' => 'RTR',
            'Streaming' => 'STR',
            'Mouse' => 'MOU',
            'Converter' => 'CNV',
            'HDMI' => 'HDM',
            'RJ45' => 'RJ4',
            'Adapter' => 'ADP',
            'Display' => 'DSP'
        ];

        if (array_key_exists($nama_kategori, $mapping)) {
            $prefix = $mapping[$nama_kategori];
        } else {
            $prefix = strtoupper(substr(str_replace(' ', '', $nama_kategori), 0, 3));
        }

        $this->db->select('kode_barang');
        $this->db->like('kode_barang', $prefix . '-', 'after');
        $this->db->order_by('id_barang', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tb_barang');

        if ($query->num_rows() > 0) {
            $last_code = $query->row()->kode_barang;
            $last_number = (int) substr($last_code, -3);
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }

        return $prefix . '-' . sprintf("%03s", $new_number);
    }

    public function insert_barang($data)
    {
        return $this->db->insert('tb_barang', $data);
    }

    public function update_barang($id, $data)
    {
        $this->db->where('id_barang', $id);
        return $this->db->update('tb_barang', $data);
    }

    public function delete_barang($id)
    {
        $this->db->where('id_barang', $id);
        return $this->db->delete('tb_barang');
    }

    // ==========================================================
    // LOGIC LAPORAN (CORE TRANSACTION)
    // ==========================================================

    public function get_laporan($id_user = null)
    {
        $this->db->select('tb_peminjaman.*, tb_users.nama_lengkap as nama_operator');
        $this->db->from('tb_peminjaman');
        $this->db->join('tb_users', 'tb_users.id_user = tb_peminjaman.id_operator');

        if ($id_user != null) {
            // [UPDATE] Menggunakan logika kolaborasi: Cek ID Operator Utama ATAU Petugas Tambahan
            $this->db->group_start();
            $this->db->where('tb_peminjaman.id_operator', $id_user);
            $this->db->or_like('tb_peminjaman.petugas_tambahan', '"' . $id_user . '"');
            $this->db->group_end();
        }

        $this->db->order_by('tgl_pinjam', 'DESC');
        return $this->db->get()->result();
    }

    // Wrapper function agar konsisten pemanggilannya
    public function get_laporan_operator_kolaborasi($id_user)
    {
        return $this->get_laporan($id_user);
    }

    public function get_detail_laporan($id_peminjaman)
    {
        $this->db->select('tb_peminjaman_detail.*, tb_barang.nama_barang, tb_barang.kode_barang');
        $this->db->from('tb_peminjaman_detail');
        $this->db->join('tb_barang', 'tb_barang.id_barang = tb_peminjaman_detail.id_barang');
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->get()->result();
    }

    public function get_detail_by_id($id_detail)
    {
        return $this->db->get_where('tb_peminjaman_detail', ['id_detail' => $id_detail])->row();
    }

    public function create_laporan($data)
    {
        $this->db->insert('tb_peminjaman', $data);
        return $this->db->insert_id();
    }

    // Update Data Petugas (Utama & Tambahan)
    public function update_laporan_petugas($id_peminjaman, $data)
    {
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', $data);
    }

    // Hapus Laporan Penuh & Kembalikan Stok (Restore)
    public function delete_laporan_full($id_peminjaman)
    {
        $this->db->trans_start();

        // 1. Ambil detail untuk restore stok
        $details = $this->db->get_where('tb_peminjaman_detail', ['id_peminjaman' => $id_peminjaman])->result();

        foreach ($details as $d) {
            $this->db->set('stok_tersedia', 'stok_tersedia + ' . (int)$d->qty_pinjam, FALSE);
            $this->db->where('id_barang', $d->id_barang);
            $this->db->update('tb_barang');
        }

        // 2. Hapus detail & header
        $this->db->delete('tb_peminjaman_detail', ['id_peminjaman' => $id_peminjaman]);
        $this->db->delete('tb_peminjaman', ['id_peminjaman' => $id_peminjaman]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ==========================================================
    // LOGIC ITEM DETAIL & STOCK MANAGEMENT
    // ==========================================================

    public function add_item_ke_laporan($data_detail)
    {
        $this->db->trans_start();
        $this->db->insert('tb_peminjaman_detail', $data_detail);

        // Kurangi Stok Gudang
        $this->db->set('stok_tersedia', 'stok_tersedia - ' . (int)$data_detail['qty_pinjam'], FALSE);
        $this->db->where('id_barang', $data_detail['id_barang']);
        $this->db->update('tb_barang');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_qty_item($id_detail, $qty_baru)
    {
        $this->db->trans_start();

        $detail_lama = $this->db->get_where('tb_peminjaman_detail', ['id_detail' => $id_detail])->row();
        $qty_lama = $detail_lama->qty_pinjam;
        $id_barang = $detail_lama->id_barang;

        $selisih = $qty_baru - $qty_lama;

        // Update Qty di Detail
        $this->db->where('id_detail', $id_detail);
        $this->db->update('tb_peminjaman_detail', ['qty_pinjam' => $qty_baru]);

        // Sesuaikan Stok Gudang
        $this->db->set('stok_tersedia', 'stok_tersedia - ' . (int)$selisih, FALSE);
        $this->db->where('id_barang', $id_barang);
        $this->db->update('tb_barang');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_pengembalian($id_detail, $qty_kembali_baru, $kondisi)
    {
        $this->db->trans_start();

        $detail_lama = $this->db->get_where('tb_peminjaman_detail', ['id_detail' => $id_detail])->row();
        $qty_kembali_lama = $detail_lama->qty_kembali;
        $id_barang = $detail_lama->id_barang;

        $selisih_kembali = $qty_kembali_baru - $qty_kembali_lama;

        // Update Data Pengembalian
        $data_update = [
            'qty_kembali' => $qty_kembali_baru,
            'kondisi_kembali' => $kondisi
        ];
        $this->db->where('id_detail', $id_detail);
        $this->db->update('tb_peminjaman_detail', $data_update);

        // Kembalikan Stok ke Gudang (Increment Stok Tersedia)
        $this->db->set('stok_tersedia', 'stok_tersedia + ' . (int)$selisih_kembali, FALSE);
        $this->db->where('id_barang', $id_barang);
        $this->db->update('tb_barang');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ==========================================================
    // LOGIC STATUS REPORT (LOCK/UNLOCK/FINALIZE)
    // ==========================================================

    public function lock_laporan($id_peminjaman, $id_admin)
    {
        $data = [
            'is_locked' => 1,
            'status' => 'dipakai',
            'locked_by' => $id_admin,
            'locked_at' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', $data);
    }

    public function unlock_laporan($id_peminjaman)
    {
        $data = [
            'is_locked' => 0,
            'locked_by' => NULL,
            'locked_at' => NULL
        ];
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', $data);
    }

    public function finalize_laporan($id_peminjaman)
    {
        $data = [
            'status' => 'selesai',
            'tgl_kembali_realisasi' => date('Y-m-d')
        ];
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', $data);
    }

    // ==========================================================
    // LOGIC TRACKING & HISTORY
    // ==========================================================

    // Tracking Barang Keluar (Status != Selesai)
    public function get_barang_sedang_dipinjam()
    {
        $this->db->select('
            d.qty_pinjam, 
            b.kode_barang, b.nama_barang, 
            p.nama_event, p.tgl_pinjam, p.tgl_kembali_rencana, p.status, p.id_peminjaman,
            u.nama_lengkap as pj_nama
        ');
        $this->db->from('tb_peminjaman_detail d');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->join('tb_barang b', 'b.id_barang = d.id_barang');
        $this->db->join('tb_users u', 'u.id_user = p.id_operator');

        $this->db->where('p.status !=', 'selesai');

        $this->db->order_by('p.tgl_pinjam', 'ASC');
        return $this->db->get()->result();
    }

    // Riwayat Pemakaian Per Barang (Timeline)
    public function get_riwayat_pemakaian_barang($id_barang)
    {
        $this->db->select('
            p.nama_event, p.lokasi_event, p.tgl_pinjam, p.tgl_kembali_realisasi, p.status, p.kode_transaksi,
            d.qty_pinjam, d.qty_kembali, d.kondisi_kembali,
            u.nama_lengkap as pj_nama
        ');
        $this->db->from('tb_peminjaman_detail d');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = d.id_peminjaman');
        $this->db->join('tb_users u', 'u.id_user = p.id_operator');

        $this->db->where('d.id_barang', $id_barang);

        $this->db->order_by('p.tgl_pinjam', 'DESC');
        return $this->db->get()->result();
    }

    // [BARU] Riwayat Tugas Operator dengan Filter & Kolaborasi
    public function get_riwayat_tugas_operator($id_user, $bulan = null, $tahun = null)
    {
        $this->db->select('
            p.*, 
            DATEDIFF(p.tgl_kembali_rencana, p.tgl_pinjam) + 1 as durasi_hari
        ');
        $this->db->from('tb_peminjaman p');

        // Filter Bulan & Tahun jika dipilih
        if ($bulan && $tahun) {
            $this->db->where('MONTH(p.tgl_pinjam)', $bulan);
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        } elseif ($tahun) {
            // Jika cuma tahun yang dipilih
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        }

        // Logika Kolaborasi (Utama OR Pendamping)
        $this->db->group_start();
        $this->db->where('p.id_operator', $id_user);
        $this->db->or_like('p.petugas_tambahan', '"' . $id_user . '"');
        $this->db->group_end();

        // Urutkan dari event terbaru
        $this->db->order_by('p.tgl_pinjam', 'DESC');

        return $this->db->get()->result();
    }
}
