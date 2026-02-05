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

        // Ambil Data Statistik (Saldo Real, Pos Kas PT, Pos Angsuran, Pos Royalti, Piutang)
        $stats = $this->M_keuangan->get_dashboard_stats();
        $data = array_merge($data, $stats); // Gabungkan array stats ke data view

        // Ambil Data Grafik & 5 Transaksi Terakhir
        $data['chart_data'] = $this->M_keuangan->get_trend_keuangan();
        $data['recent_trx'] = $this->M_keuangan->get_recent_transactions(5);

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_dashboard', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // 1. MASTER KATEGORI PENGELUARAN
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
    // 2. LAPORAN KEUANGAN (VIEW, DETAIL, DELETE, EDIT)
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

    // [BARU] HALAMAN EDIT LAPORAN
    public function edit_laporan($id_keuangan)
    {
        $header = $this->M_keuangan->get_keuangan_by_id($id_keuangan);
        if (!$header) show_404();

        $data['title'] = 'Edit Laporan: ' . $header->nama_event;
        $data['header'] = $header;
        $data['detail_sdm'] = $this->M_keuangan->get_sdm_by_id($id_keuangan);
        $data['detail_ops'] = $this->M_keuangan->get_ops_by_id($id_keuangan);
        $data['kategori_ops'] = $this->M_keuangan->get_all_kategori();

        // Ambil info hutang terbaru untuk setiap personil SDM agar bisa ditampilkan di form edit
        foreach ($data['detail_sdm'] as &$sdm) {
            $sdm->current_hutang = $this->M_keuangan->get_total_hutang_user($sdm->id_user);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_edit_laporan', $data);
        $this->load->view('templates/footer');
    }

    // [BARU] PROSES UPDATE LAPORAN
    public function proses_update()
    {
        $id_keuangan = $this->input->post('id_keuangan');

        // Data Header
        $data_header = [
            'id_peminjaman'     => $this->input->post('id_peminjaman'), // Tetap ID lama
            'tgl_laporan'       => date('Y-m-d'), // Update tgl laporan ke hari edit
            'jenis_pemasukan'   => $this->input->post('jenis_pemasukan'),
            'harga_set'         => $this->input->post('harga_set') ?: 0,
            'jml_gelanggang'    => $this->input->post('jml_gelanggang') ?: 0,
            'jml_hari'          => $this->input->post('jml_hari') ?: 0,
            'total_pemasukan'   => $this->input->post('total_pemasukan_final'),
            'fee_intern_active' => $this->input->post('fee_intern_check') ? 1 : 0,
            'fee_intern_nominal' => $this->input->post('fee_intern_val') ?: 0,
            'fee_ekstern_active' => $this->input->post('fee_ekstern_check') ? 1 : 0,
            'fee_ekstern_nominal' => $this->input->post('fee_ekstern_val') ?: 0,
            'total_biaya_sdm'   => $this->input->post('total_biaya_sdm'),
            'total_biaya_ops'   => $this->input->post('total_biaya_ops'),
            'laba_kotor'        => $this->input->post('laba_bersih_val'),
            'kas_pt_nominal'    => $this->input->post('share_kas_val'),
            'angsuran_nominal'  => $this->input->post('share_angsuran_val'),
            'royalti_nominal'   => $this->input->post('share_royalti_val')
        ];

        // Data SDM
        $data_gaji = [];
        $sdm_ids = $this->input->post('sdm_id_user');
        if ($sdm_ids) {
            foreach ($sdm_ids as $key => $uid) {
                if ($this->input->post('sdm_total')[$key] > 0) {
                    $data_gaji[] = [
                        'id_user' => $uid,
                        'id_keuangan' => $id_keuangan,
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

        // Data Ops
        $data_ops = [];
        $ops_kat = $this->input->post('ops_kategori');
        if ($ops_kat) {
            foreach ($ops_kat as $key => $kat_id) {
                if ($this->input->post('ops_nominal')[$key] > 0) {
                    $data_ops[] = [
                        'id_kategori' => $kat_id,
                        'id_keuangan' => $id_keuangan,
                        'keterangan'  => $this->input->post('ops_keterangan')[$key],
                        'nominal'     => $this->input->post('ops_nominal')[$key]
                    ];
                }
            }
        }

        if ($this->M_keuangan->update_transaksi_full($id_keuangan, $data_header, $data_ops, $data_gaji)) {
            $this->session->set_flashdata('success', 'Laporan Keuangan berhasil diperbarui!');
            redirect('bendahara/laporan');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui transaksi.');
            redirect('bendahara/edit_laporan/' . $id_keuangan);
        }
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

    // [AJAX] Mengambil data detail event & operator (PLUS DATA HUTANG)
    public function get_event_details_ajax()
    {
        $id_peminjaman = $this->input->post('id_peminjaman');
        $laporan = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();

        if ($laporan) {
            $personil = [];

            // 1. Operator Utama
            $utama = $this->db->get_where('tb_users', ['id_user' => $laporan->id_operator])->row();
            if ($utama) {
                // Ambil info hutang
                $hutang = $this->M_keuangan->get_total_hutang_user($utama->id_user);

                $personil[] = [
                    'id' => $utama->id_user,
                    'nama' => $utama->nama_lengkap,
                    'peran' => 'Ketua Tim (PJ)',
                    'hutang' => $hutang
                ];
            }

            // 2. Operator Tambahan (JSON)
            if (!empty($laporan->petugas_tambahan)) {
                $ids = json_decode($laporan->petugas_tambahan);
                if (is_array($ids) && !empty($ids)) {
                    $this->db->where_in('id_user', $ids);
                    $tambahan = $this->db->get('tb_users')->result();
                    foreach ($tambahan as $t) {
                        // Ambil info hutang
                        $hutang = $this->M_keuangan->get_total_hutang_user($t->id_user);

                        $personil[] = [
                            'id' => $t->id_user,
                            'nama' => $t->nama_lengkap,
                            'peran' => 'Pendamping',
                            'hutang' => $hutang
                        ];
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'event' => $laporan,
                'personil' => $personil
            ]);
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

            // Pemasukan
            'jenis_pemasukan'   => $this->input->post('jenis_pemasukan'),
            'harga_set'         => $this->input->post('harga_set') ?: 0,
            'jml_gelanggang'    => $this->input->post('jml_gelanggang') ?: 0,
            'jml_hari'          => $this->input->post('jml_hari') ?: 0,
            'total_pemasukan'   => $this->input->post('total_pemasukan_final'),

            // Fee Marketing
            'fee_intern_active' => $this->input->post('fee_intern_check') ? 1 : 0,
            'fee_intern_nominal' => $this->input->post('fee_intern_val') ?: 0,
            'fee_ekstern_active' => $this->input->post('fee_ekstern_check') ? 1 : 0,
            'fee_ekstern_nominal' => $this->input->post('fee_ekstern_val') ?: 0,

            // Hasil Hitungan (Agregat)
            'total_biaya_sdm'   => $this->input->post('total_biaya_sdm'),
            'total_biaya_ops'   => $this->input->post('total_biaya_ops'),
            'laba_kotor'        => $this->input->post('laba_bersih_val'),

            // Bagi Hasil (Alokasi Dana)
            'kas_pt_nominal'    => $this->input->post('share_kas_val'),
            'angsuran_nominal'  => $this->input->post('share_angsuran_val'),
            'royalti_nominal'   => $this->input->post('share_royalti_val')
        ];

        // 2. Ambil Data Gaji SDM (Array)
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

                        // Komponen Tambahan
                        'nominal_setting'   => $this->input->post('sdm_setting')[$key] ?: 0,
                        'nominal_transport' => $this->input->post('sdm_transport')[$key] ?: 0,
                        'nominal_bonus'     => $this->input->post('sdm_bonus')[$key] ?: 0,
                        'nominal_data'      => $this->input->post('sdm_data')[$key] ?: 0,

                        // [FITUR POTONG GAJI] Nominal Potongan Kasbon
                        'nominal_potongan'  => $this->input->post('sdm_potongan')[$key] ?: 0,

                        'total_diterima' => $this->input->post('sdm_total')[$key]
                    ];
                }
            }
        }

        // 3. Ambil Data Operasional (Array)
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

        // Simpan via Model (Model akan handle logika auto kas & potong utang)
        if ($this->M_keuangan->simpan_transaksi_full($data_header, $data_ops, $data_gaji)) {
            $this->session->set_flashdata('success', 'Laporan Keuangan & Payroll berhasil diproses! Saldo Kas telah diupdate.');
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

    // Slip Gaji Per Event
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
    // 5. MANAJEMEN KASBON (PINJAMAN)
    // ==========================================

    public function kasbon()
    {
        $data['title'] = 'Manajemen Kasbon Karyawan';
        $data['kasbon'] = $this->M_keuangan->get_all_kasbon();

        // Ambil list user (kecuali admin) untuk form pengajuan manual oleh bendahara
        $data['users'] = $this->db->get_where('tb_users', ['role !=' => 'admin'])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_kasbon', $data);
        $this->load->view('templates/footer');
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

    public function ajukan_kasbon()
    {
        $data = [
            'id_user'           => $this->input->post('id_user'),
            'tanggal_pengajuan' => date('Y-m-d'),
            'nominal_pinjaman'  => $this->input->post('nominal'),
            'keterangan'        => $this->input->post('keterangan'),
            'status'            => 'pending',
            'sisa_tagihan'      => $this->input->post('nominal')
        ];

        if ($this->M_keuangan->insert_kasbon($data)) {
            $this->session->set_flashdata('success', 'Pengajuan kasbon berhasil dicatat.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengajukan kasbon.');
        }
        redirect('bendahara/kasbon');
    }

    public function aksi_kasbon($id_kasbon, $aksi)
    {
        if ($aksi == 'acc') {
            $this->M_keuangan->update_status_kasbon($id_kasbon, 'active');
            $this->session->set_flashdata('success', 'Kasbon disetujui & aktif. Dana cair (Tercatat Pengeluaran).');
        } elseif ($aksi == 'tolak') {
            $this->M_keuangan->update_status_kasbon($id_kasbon, 'rejected');
            $this->session->set_flashdata('warning', 'Pengajuan kasbon ditolak.');
        } elseif ($aksi == 'hapus') {
            $this->db->delete('tb_kasbon', ['id_kasbon' => $id_kasbon]);
            $this->session->set_flashdata('success', 'Data kasbon dihapus.');
        }
        redirect('bendahara/kasbon');
    }

    public function bayar_kasbon_tunai()
    {
        $id_kasbon = $this->input->post('id_kasbon');
        $nominal = $this->input->post('nominal_bayar');

        $data_bayar = [
            'id_kasbon'     => $id_kasbon,
            'tanggal_bayar' => date('Y-m-d'),
            'nominal_bayar' => $nominal,
            'metode'        => 'tunai',
            'keterangan'    => 'Setor Tunai ke Bendahara'
        ];

        if ($this->M_keuangan->bayar_kasbon($id_kasbon, $data_bayar)) {
            $this->session->set_flashdata('success', 'Pembayaran tunai berhasil dicatat (Tercatat Pemasukan).');
        } else {
            $this->session->set_flashdata('error', 'Gagal mencatat pembayaran.');
        }
        redirect('bendahara/kasbon');
    }

    // ==========================================
    // 6. BUKU KAS UMUM (DENGAN EDIT)
    // ==========================================

    public function buku_kas()
    {
        $data['title'] = 'Buku Kas Umum (Arus Kas)';

        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        if (empty($tahun)) $tahun = date('Y');

        $data['kas'] = $this->M_keuangan->get_buku_kas($bulan, $tahun);
        $data['saldo_akhir'] = $this->M_keuangan->get_saldo_akhir();

        // [UPDATE] Mengambil Data Master Kategori untuk Dropdown
        $data['kategori_ops'] = $this->M_keuangan->get_all_kategori();

        $data['filter_bulan'] = $bulan;
        $data['filter_tahun'] = $tahun;

        $this->load->view('templates/header', $data);
        $this->load->view('bendahara/v_buku_kas', $data);
        $this->load->view('templates/footer');
    }

    // Handles both INSERT and UPDATE
    public function tambah_kas()
    {
        $id_kas = $this->input->post('id_kas'); // Cek jika ada ID (Berarti Edit)

        $data = [
            'tanggal'       => $this->input->post('tanggal'),
            'jenis'         => $this->input->post('jenis'),
            'kategori'      => $this->input->post('kategori'),
            'nominal'       => $this->input->post('nominal'),
            'keterangan'    => $this->input->post('keterangan')
        ];

        if (!empty($id_kas)) {
            // Proses Update
            if ($this->M_keuangan->update_kas_manual($id_kas, $data)) {
                $this->session->set_flashdata('success', 'Transaksi berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui (Mungkin transaksi otomatis).');
            }
        } else {
            // Proses Insert Baru
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

    // AJAX untuk mendapatkan data kas saat tombol edit diklik
    public function get_kas_ajax()
    {
        $id = $this->input->post('id');
        $data = $this->M_keuangan->get_kas_by_id($id);
        echo json_encode($data);
    }
}
