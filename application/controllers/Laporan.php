<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
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
        $role = $this->session->userdata('role');
        $id_user = (string) $this->session->userdata('id_user');

        if ($role == 'admin') {
            $data['laporan'] = $this->M_inventory->get_laporan(null);
        } else {
            // [UPDATE] Mengambil laporan di mana user menjadi Utama ATAU Pendamping
            $data['laporan'] = $this->M_inventory->get_laporan_operator_kolaborasi($id_user);
        }

        $data['title'] = 'Daftar Laporan';
        $this->load->view('templates/header', $data);
        $this->load->view('laporan/v_index', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id_peminjaman)
    {
        $id_user = (string) $this->session->userdata('id_user');
        $role = $this->session->userdata('role');

        // Ambil data laporan
        $laporan = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();

        if (!$laporan) {
            show_404();
        }

        // [SECURITY CHECK] Pastikan user punya hak akses ke laporan ini
        if ($role != 'admin') {
            $is_utama = ($laporan->id_operator == $id_user);
            $pt = json_decode($laporan->petugas_tambahan);
            // Cek apakah user ada di array petugas tambahan
            $is_pendamping = (is_array($pt) && in_array($id_user, $pt));

            if (!$is_utama && !$is_pendamping) {
                $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke laporan ini.');
                redirect('laporan');
                return;
            }
        }

        $data['title'] = 'Detail Laporan';
        $data['laporan'] = $laporan;

        // Ambil nama operator utama
        $op_utama = $this->db->get_where('tb_users', ['id_user' => $laporan->id_operator])->row();
        $data['laporan']->nama_operator = $op_utama ? $op_utama->nama_lengkap : '-';

        // Ambil Nama Petugas Tambahan
        $data['list_petugas_tambahan'] = [];
        if (!empty($laporan->petugas_tambahan)) {
            $ids = json_decode($laporan->petugas_tambahan);
            if (!empty($ids)) {
                $this->db->select('nama_lengkap');
                $this->db->where_in('id_user', $ids);
                $data['list_petugas_tambahan'] = $this->db->get('tb_users')->result();
            }
        }

        $data['detail'] = $this->M_inventory->get_detail_laporan($id_peminjaman);
        $data['semua_barang'] = $this->M_inventory->get_all_barang();
        $data['list_operator'] = $this->M_inventory->get_all_operators();

        $this->load->view('templates/header', $data);
        $this->load->view('laporan/v_detail', $data);
        $this->load->view('templates/footer');
    }

    // --- FUNGSI ADMIN: BUAT & EDIT LAPORAN ---

    public function buat_baru()
    {
        if ($this->session->userdata('role') != 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak! Hanya Admin yang dapat membuat laporan baru.');
            redirect('laporan');
            return;
        }

        $data['title'] = 'Buat Laporan Baru';
        $data['list_operator'] = $this->M_inventory->get_all_operators();

        $this->load->view('templates/header', $data);
        $this->load->view('laporan/v_tambah', $data);
        $this->load->view('templates/footer');
    }

    public function proses_tambah()
    {
        if ($this->session->userdata('role') != 'admin') {
            redirect('laporan');
        }

        $kode = 'TRX-' . time();

        $petugas_tambahan = $this->input->post('petugas_tambahan');
        $json_petugas = !empty($petugas_tambahan) ? json_encode($petugas_tambahan) : null;

        $data = [
            'kode_transaksi' => $kode,
            'id_operator' => $this->input->post('id_operator'),
            'petugas_tambahan' => $json_petugas,
            'nama_event' => $this->input->post('nama_event'),
            'lokasi_event' => $this->input->post('lokasi_event'),
            'tgl_pinjam' => $this->input->post('tgl_pinjam'),
            'tgl_kembali_rencana' => $this->input->post('tgl_kembali_rencana'),
            'keterangan' => $this->input->post('keterangan'),
            'status' => 'draft',
            'is_locked' => 0
        ];

        $insert_id = $this->M_inventory->create_laporan($data);

        if ($insert_id) {
            $this->session->set_flashdata('success', 'Laporan berhasil dibuat! Silakan tambahkan barang.');
            redirect('laporan/detail/' . $insert_id);
        } else {
            redirect('laporan');
        }
    }

    public function update_petugas_action()
    {
        if ($this->session->userdata('role') != 'admin') {
            redirect('laporan');
        }

        $id_peminjaman = $this->input->post('id_peminjaman');
        $id_operator_baru = $this->input->post('id_operator');

        $petugas_tambahan = $this->input->post('petugas_tambahan');
        $json_petugas = !empty($petugas_tambahan) ? json_encode($petugas_tambahan) : null;

        $data_update = [
            'id_operator' => $id_operator_baru,
            'petugas_tambahan' => $json_petugas
        ];

        if ($this->M_inventory->update_laporan_petugas($id_peminjaman, $data_update)) {
            $this->session->set_flashdata('success', 'Data personil bertugas berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data personil.');
        }

        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function hapus_laporan($id_peminjaman)
    {
        if ($this->session->userdata('role') != 'admin') {
            redirect('laporan');
        }

        if ($this->M_inventory->delete_laporan_full($id_peminjaman)) {
            $this->session->set_flashdata('success', 'Laporan berhasil dihapus & stok telah dikembalikan ke gudang.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus laporan.');
        }
        redirect('laporan');
    }

    // --- FUNGSI ITEM (Bisa Admin & Operator yang Bertugas) ---
    // Tambahkan validasi akses di sini jika ingin lebih ketat (seperti di function detail)

    public function tambah_item_action()
    {
        $id_peminjaman = $this->input->post('id_peminjaman');

        // Cek Status Lock
        $cek_status = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if ($cek_status->is_locked == 1) {
            $this->session->set_flashdata('error', 'MAAF! Laporan ini terkunci.');
            redirect('laporan/detail/' . $id_peminjaman);
            return;
        }

        $data_detail = [
            'id_peminjaman' => $id_peminjaman,
            'id_barang'     => $this->input->post('id_barang'),
            'qty_pinjam'    => $this->input->post('qty')
        ];

        $this->M_inventory->add_item_ke_laporan($data_detail);
        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function update_item_action()
    {
        $id_peminjaman = $this->input->post('id_peminjaman');
        $id_detail = $this->input->post('id_detail');
        $qty_baru = $this->input->post('qty_edit');

        $cek_status = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if ($cek_status->is_locked == 1) {
            $this->session->set_flashdata('error', 'Gagal update! Laporan terkunci.');
            redirect('laporan/detail/' . $id_peminjaman);
            return;
        }

        $this->M_inventory->update_qty_item($id_detail, $qty_baru);
        $this->session->set_flashdata('success', 'Jumlah barang berhasil diperbarui.');
        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function hapus_item($id_detail, $id_peminjaman)
    {
        $cek_status = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if ($cek_status->is_locked == 1) {
            $this->session->set_flashdata('error', 'Gagal hapus! Laporan terkunci.');
            redirect('laporan/detail/' . $id_peminjaman);
            return;
        }

        $item = $this->db->get_where('tb_peminjaman_detail', ['id_detail' => $id_detail])->row();

        // Kembalikan stok manual sebelum hapus
        $this->db->set('stok_tersedia', 'stok_tersedia + ' . (int)$item->qty_pinjam, FALSE);
        $this->db->where('id_barang', $item->id_barang);
        $this->db->update('tb_barang');

        $this->db->delete('tb_peminjaman_detail', ['id_detail' => $id_detail]);
        $this->session->set_flashdata('success', 'Item berhasil dihapus dan stok dikembalikan.');
        redirect('laporan/detail/' . $id_peminjaman);
    }

    // --- FUNGSI STATUS ---

    public function lock_action($id_peminjaman)
    {
        if ($this->session->userdata('role') != 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('laporan/detail/' . $id_peminjaman);
        }

        $id_admin = $this->session->userdata('id_user');
        $this->M_inventory->lock_laporan($id_peminjaman, $id_admin);

        $this->session->set_flashdata('success', 'Laporan berhasil di-LOCK. Mode pengembalian aktif.');
        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function unlock_action($id_peminjaman)
    {
        if ($this->session->userdata('role') != 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('laporan/detail/' . $id_peminjaman);
        }

        $this->M_inventory->unlock_laporan($id_peminjaman);

        $this->session->set_flashdata('success', 'Laporan berhasil di-UNLOCK.');
        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function finalize_action($id_peminjaman)
    {
        if ($this->session->userdata('role') != 'admin') {
            redirect('laporan/detail/' . $id_peminjaman);
        }

        $this->M_inventory->finalize_laporan($id_peminjaman);

        $this->session->set_flashdata('success', 'Transaksi ditandai SELESAI. Terima kasih.');
        redirect('laporan/detail/' . $id_peminjaman);
    }

    public function proses_pengembalian_item()
    {
        $id_peminjaman = $this->input->post('id_peminjaman');
        $id_detail = $this->input->post('id_detail');
        $qty_kembali = $this->input->post('qty_kembali');
        $kondisi = $this->input->post('kondisi_kembali');

        $detail = $this->db->get_where('tb_peminjaman_detail', ['id_detail' => $id_detail])->row();

        if ($qty_kembali > $detail->qty_pinjam) {
            $this->session->set_flashdata('error', 'GAGAL: Jumlah kembali melebihi jumlah yang dipinjam!');
            redirect('laporan/detail/' . $id_peminjaman);
            return;
        }

        $this->M_inventory->update_pengembalian($id_detail, $qty_kembali, $kondisi);

        $this->session->set_flashdata('success', 'Data pengembalian barang berhasil disimpan.');
        redirect('laporan/detail/' . $id_peminjaman);
    }
}
