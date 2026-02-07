<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bendahara extends CI_Controller
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

        // 2. Cek Role: Hanya 'admin' dan 'bendahara' yang boleh masuk
        $role = $this->session->userdata('role');
        if ($role != 'admin' && $role != 'bendahara') {
            redirect('dashboard');
        }
    }

    // ==========================================
    // 0. DASHBOARD KEUANGAN (HOME)
    // ==========================================

    public function index()
    {
        $data['title'] = 'Dashboard Keuangan';

        // [PENTING] Menggunakan fungsi stats dari Model Akang (saldo_kas_pt_now = Real - Angsuran - Royalti)
        $stats = $this->M_keuangan->get_dashboard_stats();
        $data = array_merge($data, $stats);

        // Ambil Data Grafik & 5 Transaksi Terakhir
        $data['chart_data'] = $this->M_keuangan->get_trend_keuangan();
        $data['recent_trx'] = $this->M_keuangan->get_recent_transactions(5);

        $this->load->view('templates/header', $data);
        // Sidebar dihapus sesuai permintaan
        $this->load->view('bendahara/v_dashboard', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // 1. MANAJEMEN KASBON 
    // ==========================================

    public function kasbon()
    {
        $data['title'] = 'Manajemen Kasbon';
        $data['kasbon'] = $this->M_keuangan->get_all_kasbon();
        $data['users'] = $this->db->get_where('tb_users', ['role !=' => 'admin'])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_kasbon', $data);
        $this->load->view('templates/footer');
    }

    // FUNGSI 1: Simpan Pengajuan Kasbon (Logic Manual di Controller agar Tanggal Sesuai)
    public function ajukan_kasbon()
    {
        // 1. Bersihkan format rupiah
        $nominal = preg_replace('/[^0-9]/', '', $this->input->post('nominal'));
        $id_user = $this->input->post('id_user');

        // 2. Tangkap Tanggal Manual dari Form
        $tgl_manual = $this->input->post('tanggal_pengajuan');
        if (empty($tgl_manual)) $tgl_manual = date('Y-m-d');

        // 3. Mulai Transaksi Manual (Agar Insert ke Kas Umum pakai tanggal manual)
        $this->db->trans_start();

        // A. Simpan ke tb_kasbon
        $data_kasbon = [
            'id_user'             => $id_user,
            'nominal_pinjaman'    => $nominal,
            'sisa_tagihan'        => $nominal,
            'keterangan'          => $this->input->post('keterangan'),
            'tanggal_pengajuan'   => $tgl_manual,
            'status'              => 'active', // Langsung aktif & cair
            'created_at'          => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tb_kasbon', $data_kasbon);
        $id_kasbon = $this->db->insert_id();

        // B. Simpan ke tb_kas_umum (PENGELUARAN)
        // Kita lakukan manual disini karena method model bawaan mungkin pakai date('Y-m-d') otomatis
        $user = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();
        $this->db->insert('tb_kas_umum', [
            'tanggal'       => $tgl_manual, // Pakai tanggal manual fisik uang keluar
            'jenis'         => 'keluar',
            'kategori'      => 'Pencairan Kasbon',
            'nominal'       => $nominal,
            'keterangan'    => 'Peminjaman a.n ' . ($user ? $user->nama_lengkap : 'Karyawan'),
            'sumber_auto'   => 'kasbon',
            'ref_id'        => $id_kasbon,
            'created_by'    => $this->session->userdata('id_user')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('success', 'Pengajuan kasbon berhasil dicatat! Dana telah dicairkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan kasbon.');
        }
        redirect('bendahara/kasbon');
    }

    // FUNGSI 2: Bayar Kasbon Tunai (Logic Manual di Controller agar Tanggal Sesuai)
    public function bayar_kasbon_tunai()
    {
        $id_kasbon = $this->input->post('id_kasbon');
        $nominal_bayar = preg_replace('/[^0-9]/', '', $this->input->post('nominal_bayar'));

        // Tangkap Tanggal Bayar Manual
        $tgl_bayar = $this->input->post('tanggal_bayar');
        if (empty($tgl_bayar)) $tgl_bayar = date('Y-m-d');

        $this->db->trans_start();

        // A. Simpan History Bayar
        $data_bayar = [
            'id_kasbon'     => $id_kasbon,
            'tanggal_bayar' => $tgl_bayar,
            'nominal_bayar' => $nominal_bayar,
            'metode'        => 'tunai',
            'keterangan'    => 'Setor Tunai ke Bendahara',
            'id_keuangan'   => 0
        ];
        $this->db->insert('tb_kasbon_bayar', $data_bayar);

        // B. Update Sisa Tagihan
        $this->db->set('sisa_tagihan', 'sisa_tagihan - ' . $nominal_bayar, FALSE);
        $this->db->where('id_kasbon', $id_kasbon);
        $this->db->update('tb_kasbon');

        // Cek Lunas
        $cek = $this->db->get_where('tb_kasbon', ['id_kasbon' => $id_kasbon])->row();
        if ($cek->sisa_tagihan <= 0) {
            $this->db->where('id_kasbon', $id_kasbon);
            $this->db->update('tb_kasbon', ['status' => 'lunas', 'sisa_tagihan' => 0]);
        }

        // C. Simpan ke tb_kas_umum (PEMASUKAN)
        $user = $this->db->get_where('tb_users', ['id_user' => $cek->id_user])->row();
        $this->db->insert('tb_kas_umum', [
            'tanggal'       => $tgl_bayar, // Gunakan tanggal manual
            'jenis'         => 'masuk',
            'kategori'      => 'Pelunasan Kasbon Tunai',
            'nominal'       => $nominal_bayar,
            'keterangan'    => 'Setoran Pelunasan: ' . ($user ? $user->nama_lengkap : 'Karyawan'),
            'sumber_auto'   => 'kasbon',
            'ref_id'        => $id_kasbon,
            'created_by'    => $this->session->userdata('id_user')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('success', 'Pembayaran kasbon diterima. Saldo Kas bertambah.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses pembayaran.');
        }

        redirect('bendahara/kasbon');
    }

    public function detail_kasbon($id_kasbon)
    {
        $this->db->select('k.*, u.nama_lengkap, u.username, u.role');
        $this->db->from('tb_kasbon k');
        $this->db->join('tb_users u', 'u.id_user = k.id_user');
        $this->db->where('k.id_kasbon', $id_kasbon);
        $kasbon = $this->db->get()->row();

        if (!$kasbon) {
            show_404();
        }

        $data['title'] = 'Detail Kasbon & Riwayat Pembayaran';
        $data['kasbon'] = $kasbon;
        $data['history'] = $this->M_keuangan->get_history_bayar($id_kasbon);

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_detail_kasbon', $data);
        $this->load->view('templates/footer');
    }

    public function aksi_kasbon($id_kasbon, $aksi)
    {
        if ($aksi == 'acc') {
            $this->M_keuangan->update_status_kasbon($id_kasbon, 'active');
            $this->session->set_flashdata('success', 'Kasbon disetujui & aktif.');
        } elseif ($aksi == 'tolak') {
            $this->M_keuangan->update_status_kasbon($id_kasbon, 'rejected');
            $this->session->set_flashdata('warning', 'Pengajuan kasbon ditolak.');
        } elseif ($aksi == 'hapus') {
            $this->db->delete('tb_kasbon', ['id_kasbon' => $id_kasbon]);
            $this->session->set_flashdata('success', 'Data kasbon dihapus.');
        }
        redirect('bendahara/kasbon');
    }

    // ==========================================
    // 2. LAPORAN KEUANGAN
    // ==========================================

    public function laporan()
    {
        $data['title'] = 'Daftar Laporan Keuangan';
        $data['laporan'] = $this->M_keuangan->get_all_laporan_keuangan();

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_laporan_keuangan', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id_keuangan)
    {
        $header = $this->M_keuangan->get_keuangan_by_id($id_keuangan);

        if (!$header) {
            show_404();
        }

        $data['title'] = 'Detail Keuangan: ' . $header->nama_event;
        $data['header'] = $header;
        $data['detail_sdm'] = $this->M_keuangan->get_sdm_by_id($id_keuangan);
        $data['detail_ops'] = $this->M_keuangan->get_ops_by_id($id_keuangan);

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_detail_keuangan', $data);
        $this->load->view('templates/footer');
    }

    public function hapus_laporan($id_keuangan)
    {
        if ($this->M_keuangan->delete_keuangan($id_keuangan)) {
            $this->session->set_flashdata('success', 'Laporan keuangan dihapus. Saldo kasbon karyawan telah dikembalikan (revert).');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus laporan.');
        }
        redirect('bendahara/laporan');
    }

    // HALAMAN EDIT LAPORAN
    public function edit_laporan($id_keuangan)
    {
        $header = $this->M_keuangan->get_keuangan_by_id($id_keuangan);
        if (!$header) show_404();

        $data['title'] = 'Edit Laporan: ' . $header->nama_event;
        $data['header'] = $header;
        $data['detail_sdm'] = $this->M_keuangan->get_sdm_by_id($id_keuangan);
        $data['detail_ops'] = $this->M_keuangan->get_ops_by_id($id_keuangan);
        $data['kategori_ops'] = $this->M_keuangan->get_all_kategori();

        foreach ($data['detail_sdm'] as &$sdm) {
            $sdm->current_hutang = $this->M_keuangan->get_total_hutang_user($sdm->id_user);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_edit_laporan', $data);
        $this->load->view('templates/footer');
    }

    // [UPDATE] PROSES UPDATE LAPORAN (LOGIKA SAFE CALCULATION)
    public function proses_update()
    {
        $id_keuangan = $this->input->post('id_keuangan');
        $id_peminjaman = $this->input->post('id_peminjaman');

        $clean_fn = function ($val) {
            $cleaned = preg_replace('/[^0-9]/', '', $val);
            return $cleaned === '' ? 0 : $cleaned;
        };

        // Bersihkan Data Input
        $total_pemasukan_input = $clean_fn($this->input->post('total_pemasukan_final'));

        // A. IT
        $it_active = $this->input->post('check_it') ? 1 : 0;
        $it_total = 0;
        if ($it_active) {
            if ($this->input->post('jenis_pemasukan') == 'detail') {
                $it_total = $clean_fn($this->input->post('harga_set')) * $this->input->post('jml_gelanggang') * $this->input->post('jml_hari');
            } else {
                $it_total = $clean_fn($this->input->post('subtotal_it'));
            }
        }

        // B. Logistik
        $log_active = $this->input->post('check_log') ? 1 : 0;
        $log_total = $clean_fn($this->input->post('subtotal_log'));

        // C. Lainnya
        $lain_active = $this->input->post('check_lain') ? 1 : 0;
        $lain_nominal = $clean_fn($this->input->post('lain_nominal'));
        $lain_total = $lain_active ? $lain_nominal : 0;

        // TOTAL SAFE
        $total_pemasukan_safe = ($total_pemasukan_input > 0) ? $total_pemasukan_input : ($it_total + $log_total + $lain_total);

        $total_biaya_sdm = $clean_fn($this->input->post('total_biaya_sdm'));
        $total_biaya_ops = $clean_fn($this->input->post('total_biaya_ops'));
        $fee_intern      = $clean_fn($this->input->post('fee_intern_val'));
        $fee_ekstern     = $clean_fn($this->input->post('fee_ekstern_val'));

        // Hitung Ulang Laba Bersih
        $laba_bersih_safe = $total_pemasukan_safe - $total_biaya_sdm - $total_biaya_ops - $fee_intern - $fee_ekstern;

        // Ambil Data Bagi Hasil
        $kas_pt   = $clean_fn($this->input->post('share_kas_val'));
        $angsuran = $clean_fn($this->input->post('share_angsuran_val'));
        $royalti  = $clean_fn($this->input->post('share_royalti_val'));

        // SUSUN DATA HEADER
        $data_header = [
            'id_peminjaman'       => $id_peminjaman,
            'tgl_laporan'         => date('Y-m-d H:i:s'),
            'total_pemasukan'     => $total_pemasukan_safe,
            'total_biaya_sdm'     => $total_biaya_sdm,
            'total_biaya_ops'     => $total_biaya_ops,
            'fee_intern_nominal'  => $fee_intern,
            'fee_ekstern_nominal' => $fee_ekstern,
            'laba_kotor'          => $laba_bersih_safe,

            'income_it_active'    => $it_active,
            'jenis_pemasukan'     => $this->input->post('jenis_pemasukan'),
            'harga_set'           => $clean_fn($this->input->post('harga_set')),
            'jml_gelanggang'      => $this->input->post('jml_gelanggang'),
            'jml_hari'            => $this->input->post('jml_hari'),

            'income_log_active'   => $log_active,
            'log_harga'           => $clean_fn($this->input->post('log_harga')),
            'log_qty'             => $this->input->post('log_qty'),
            'log_hari'            => $this->input->post('log_hari'),
            'log_total'           => $log_total,

            'income_lain_active'  => $lain_active,
            'lain_keterangan'     => $this->input->post('lain_keterangan'),
            'lain_nominal'        => $lain_nominal,

            'fee_intern_active'     => $this->input->post('fee_intern_check') ? 1 : 0,
            'fee_ekstern_active'    => $this->input->post('fee_ekstern_check') ? 1 : 0,
            'share_kas_active'      => $this->input->post('share_kas_check') ? 1 : 0,
            'share_angsuran_active' => $this->input->post('share_angsuran_check') ? 1 : 0,
            'share_royalti_active'  => $this->input->post('share_royalti_check') ? 1 : 0,

            'kas_pt_nominal'   => $kas_pt,
            'angsuran_nominal' => $angsuran,
            'royalti_nominal'  => $royalti,
        ];

        // SUSUN DATA OPS
        $data_ops = [];
        $ops_kat = $this->input->post('ops_kategori');
        if ($ops_kat) {
            foreach ($ops_kat as $i => $kat) {
                $nom = $clean_fn($this->input->post('ops_nominal')[$i]);
                if ($nom > 0) {
                    $data_ops[] = [
                        'id_kategori' => $kat,
                        'keterangan'  => $this->input->post('ops_keterangan')[$i],
                        'nominal'     => $nom
                    ];
                }
            }
        }

        // SUSUN DATA SDM
        $data_gaji = [];
        $sdm_user = $this->input->post('sdm_id_user');
        if ($sdm_user) {
            foreach ($sdm_user as $i => $uid) {
                $data_gaji[] = [
                    'id_user'           => $uid,
                    'peran'             => $this->input->post('sdm_peran')[$i],
                    'honor_harian'      => $clean_fn($this->input->post('sdm_honor')[$i]),
                    'jumlah_hari'       => $this->input->post('sdm_hari')[$i],
                    'nominal_setting'   => $clean_fn($this->input->post('sdm_setting')[$i]),
                    'nominal_transport' => $clean_fn($this->input->post('sdm_transport')[$i]),
                    'nominal_bonus'     => $clean_fn($this->input->post('sdm_bonus')[$i]),
                    'nominal_data'      => $clean_fn($this->input->post('sdm_data')[$i]),
                    'nominal_potongan'  => $clean_fn($this->input->post('sdm_potongan')[$i]),
                    'total_diterima'    => $clean_fn($this->input->post('sdm_total')[$i]),
                ];
            }
        }

        // EKSEKUSI KE MODEL
        $update_status = $this->M_keuangan->update_transaksi_full(
            $id_keuangan,
            $data_header,
            $data_ops,
            $data_gaji
        );

        if ($update_status) {
            $this->session->set_flashdata('success', 'Laporan berhasil diperbarui! Saldo Dashboard telah disesuaikan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal update laporan.');
        }

        redirect('bendahara/laporan');
    }

    // ==========================================
    // 3. INPUT TRANSAKSI (CREATE)
    // ==========================================

    public function buat_laporan()
    {
        $data['title'] = 'Input Laporan Keuangan';
        $data['events'] = $this->M_keuangan->get_unreported_events();
        $data['kategori_ops'] = $this->M_keuangan->get_all_kategori();

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_input_laporan', $data);
        $this->load->view('templates/footer');
    }

    public function get_event_details_ajax()
    {
        $id_peminjaman = $this->input->post('id_peminjaman');
        $laporan = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();

        if ($laporan) {
            $personil = [];
            $utama = $this->db->get_where('tb_users', ['id_user' => $laporan->id_operator])->row();
            if ($utama) {
                $hutang = $this->M_keuangan->get_total_hutang_user($utama->id_user);
                $personil[] = ['id' => $utama->id_user, 'nama' => $utama->nama_lengkap, 'peran' => 'Ketua Tim (PJ)', 'hutang' => $hutang];
            }
            if (!empty($laporan->petugas_tambahan)) {
                $ids = json_decode($laporan->petugas_tambahan);
                if (is_array($ids) && !empty($ids)) {
                    $this->db->where_in('id_user', $ids);
                    $tambahan = $this->db->get('tb_users')->result();
                    foreach ($tambahan as $t) {
                        $hutang = $this->M_keuangan->get_total_hutang_user($t->id_user);
                        $personil[] = ['id' => $t->id_user, 'nama' => $t->nama_lengkap, 'peran' => 'Pendamping', 'hutang' => $hutang];
                    }
                }
            }
            echo json_encode(['status' => 'success', 'event' => $laporan, 'personil' => $personil]);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function proses_simpan()
    {
        // 1. Ambil Data Header
        $data_header = [
            'id_peminjaman'     => $this->input->post('id_peminjaman'),
            'tgl_laporan'       => date('Y-m-d'),

            'income_it_active'  => $this->input->post('check_it') ? 1 : 0,
            'jenis_pemasukan'   => $this->input->post('jenis_pemasukan'),
            'harga_set'         => $this->input->post('harga_set') ?: 0,
            'jml_gelanggang'    => $this->input->post('jml_gelanggang') ?: 0,
            'jml_hari'          => $this->input->post('jml_hari') ?: 0,

            'income_log_active' => $this->input->post('check_log') ? 1 : 0,
            'log_harga'         => $this->input->post('log_harga') ?: 0,
            'log_qty'           => $this->input->post('log_qty') ?: 0,
            'log_hari'          => $this->input->post('log_hari') ?: 0,
            'log_total'         => $this->input->post('subtotal_log') ?: 0,

            'income_lain_active' => $this->input->post('check_lain') ? 1 : 0,
            'lain_keterangan'   => $this->input->post('lain_keterangan'),
            'lain_nominal'      => $this->input->post('lain_nominal') ?: 0,

            'total_pemasukan'   => $this->input->post('total_pemasukan_final'),

            'fee_intern_active' => $this->input->post('fee_intern_check') ? 1 : 0,
            'fee_intern_nominal' => $this->input->post('fee_intern_val') ?: 0,
            'fee_ekstern_active' => $this->input->post('fee_ekstern_check') ? 1 : 0,
            'fee_ekstern_nominal' => $this->input->post('fee_ekstern_val') ?: 0,

            'total_biaya_sdm'   => $this->input->post('total_biaya_sdm'),
            'total_biaya_ops'   => $this->input->post('total_biaya_ops'),
            'laba_kotor'        => $this->input->post('laba_bersih_val'),

            'share_kas_active'      => $this->input->post('share_kas_check') ? 1 : 0,
            'kas_pt_nominal'        => $this->input->post('share_kas_val') ?: 0,

            'share_angsuran_active' => $this->input->post('share_angsuran_check') ? 1 : 0,
            'angsuran_nominal'      => $this->input->post('share_angsuran_val') ?: 0,

            'share_royalti_active'  => $this->input->post('share_royalti_check') ? 1 : 0,
            'royalti_nominal'       => $this->input->post('share_royalti_val') ?: 0
        ];

        // 2. Data Gaji
        $data_gaji = [];
        $sdm_ids = $this->input->post('sdm_id_user');
        if ($sdm_ids) {
            foreach ($sdm_ids as $key => $uid) {
                if ($this->input->post('sdm_total')[$key] > 0) {
                    $data_gaji[] = [
                        'id_user' => $uid,
                        'id_keuangan' => 0,
                        'peran'   => $this->input->post('sdm_peran')[$key],
                        'honor_harian' => $this->input->post('sdm_honor')[$key] ?: 0,
                        'jumlah_hari'  => $this->input->post('sdm_hari')[$key] ?: 1,
                        'nominal_setting'   => $this->input->post('sdm_setting')[$key] ?: 0,
                        'nominal_transport' => $this->input->post('sdm_transport')[$key] ?: 0,
                        'nominal_bonus'     => $this->input->post('sdm_bonus')[$key] ?: 0,
                        'nominal_data'      => $this->input->post('sdm_data')[$key] ?: 0,
                        'nominal_potongan'  => $this->input->post('sdm_potongan')[$key] ?: 0,
                        'total_diterima' => $this->input->post('sdm_total')[$key]
                    ];
                }
            }
        }

        // 3. Data Ops
        $data_ops = [];
        $ops_kat = $this->input->post('ops_kategori');
        if ($ops_kat) {
            foreach ($ops_kat as $key => $kat_id) {
                if ($this->input->post('ops_nominal')[$key] > 0) {
                    $data_ops[] = [
                        'id_kategori' => $kat_id,
                        'id_keuangan' => 0,
                        'keterangan'  => $this->input->post('ops_keterangan')[$key],
                        'nominal'     => $this->input->post('ops_nominal')[$key]
                    ];
                }
            }
        }

        if ($this->M_keuangan->simpan_transaksi_full($data_header, $data_ops, $data_gaji)) {
            $this->session->set_flashdata('success', 'Laporan Keuangan berhasil disimpan!');
            redirect('bendahara/laporan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi.');
            redirect('bendahara/buat_laporan');
        }
    }

    // ==========================================
    // 4. PAYROLL & SLIP GAJI
    // ==========================================

    public function payroll()
    {
        $data['title'] = 'Rekap Gaji Operator';
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        if (empty($tahun)) $tahun = date('Y');

        $data['payroll'] = $this->M_keuangan->get_rekap_payroll($bulan, $tahun);
        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_payroll', $data);
        $this->load->view('templates/footer');
    }

    public function slip_gaji($id_user)
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $user = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();
        if (!$user) show_404();

        $data['title'] = 'Rincian Pendapatan: ' . $user->nama_lengkap;
        $data['user'] = $user;
        $data['items'] = $this->M_keuangan->get_detail_payroll_user($id_user, $bulan, $tahun);
        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_slip_gaji', $data);
        $this->load->view('templates/footer');
    }

    public function slip_event($id_keuangan, $id_user)
    {
        $item = $this->M_keuangan->get_single_payroll_item($id_keuangan, $id_user);

        if (!$item) {
            show_404();
        }

        $data['title'] = 'Slip Gaji Event: ' . $item->kode_transaksi;
        $data['slip'] = $item;

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_slip_event', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // 5. MASTER KATEGORI PENGELUARAN
    // ==========================================

    public function kategori()
    {
        $data['title'] = 'Kategori Pengeluaran';
        $data['kategori'] = $this->M_keuangan->get_all_kategori();

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_kategori', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_kategori()
    {
        $nama = $this->input->post('nama_kategori');

        if (!empty($nama)) {
            $this->M_keuangan->insert_kategori(['nama_kategori' => $nama]);
            $this->session->set_flashdata('success', 'Kategori berhasil ditambahkan.');
        }
        redirect('bendahara/kategori');
    }

    public function hapus_kategori($id)
    {
        if ($id) {
            $this->M_keuangan->delete_kategori($id);
            $this->session->set_flashdata('success', 'Kategori berhasil dihapus.');
        }
        redirect('bendahara/kategori');
    }

    // ==========================================
    // 6. BUKU KAS UMUM (DENGAN EDIT)
    // ==========================================

    public function buku_kas()
    {
        $data['title'] = 'Buku Kas Umum (Arus Kas)';

        // Ambil Filter
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        if (empty($tahun)) $tahun = date('Y');

        // 1. Ambil Data Transaksi Kas
        $data['kas'] = $this->M_keuangan->get_buku_kas($bulan, $tahun);

        // 2. Ambil Kategori untuk Dropdown Tambah Manual
        $data['kategori_ops'] = $this->M_keuangan->get_all_kategori();

        // 3. [BARU] Ambil Statistik Saldo (Real, Kas PT, Angsuran, Royalti)
        // Kita gunakan fungsi get_dashboard_stats() yang sudah ada di Model
        $stats = $this->M_keuangan->get_dashboard_stats();
        $data = array_merge($data, $stats); // Menggabungkan variabel saldo ke $data view

        // Filter Data untuk View
        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        // Sidebar tidak dipanggil di codingan sebelumnya, sesuaikan dengan struktur Akang
        // $this->load->view('templates/sidebar'); 
        $this->load->view('bendahara/v_buku_kas', $data);
        $this->load->view('templates/footer');
    }

    // [BARU] FUNGSI CETAK BUKU KAS (PDF)
    public function cetak_buku_kas()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        if (empty($tahun)) $tahun = date('Y');

        // Ambil Data
        $data['kas'] = $this->M_keuangan->get_buku_kas($bulan, $tahun);
        $data['saldo_akhir'] = $this->M_keuangan->get_saldo_akhir(); // Pastikan fungsi ini ada di Model

        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;
        $data['title'] = 'Preview Cetak Buku Kas';

        // LOAD VIEW DENGAN TEMPLATE DASHBOARD
        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_cetak_buku_kas', $data); // View yang akan kita update
        $this->load->view('templates/footer');
    }

    // ... (Fungsi tambah_kas, hapus_kas, get_kas_ajax TETAP SAMA) ...

    // Paste kembali fungsi CRUD Kas Manual agar file ini lengkap jika dicopas
    public function tambah_kas()
    {
        $id_kas = $this->input->post('id_kas');
        $data = [
            'tanggal'       => $this->input->post('tanggal'),
            'jenis'         => $this->input->post('jenis'),
            'kategori'      => $this->input->post('kategori'),
            'nominal'       => $this->input->post('nominal'),
            'keterangan'    => $this->input->post('keterangan')
        ];

        if (!empty($id_kas)) {
            if ($this->M_keuangan->update_kas_manual($id_kas, $data)) {
                $this->session->set_flashdata('success', 'Transaksi berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal update (Mungkin transaksi otomatis).');
            }
        } else {
            $data['sumber_auto'] = 'manual';
            $data['created_by'] = $this->session->userdata('id_user');
            if ($this->M_keuangan->insert_kas_manual($data)) {
                $this->session->set_flashdata('success', 'Transaksi berhasil dicatat.');
            }
        }
        redirect('bendahara/buku_kas');
    }

    public function hapus_kas($id_kas)
    {
        if ($this->M_keuangan->delete_kas_manual($id_kas)) {
            $this->session->set_flashdata('success', 'Transaksi manual berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus! Transaksi otomatis tidak bisa dihapus manual.');
        }
        redirect('bendahara/buku_kas');
    }

    public function get_kas_ajax()
    {
        $id = $this->input->post('id');
        echo json_encode($this->M_keuangan->get_kas_by_id($id));
    }
}
