<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_keuangan extends CI_Model
{

    // ==========================================
    // 1. MASTER KATEGORI PENGELUARAN
    // ==========================================

    public function get_all_kategori()
    {
        return $this->db->get('tb_kategori_keuangan')->result();
    }

    public function insert_kategori($data)
    {
        return $this->db->insert('tb_kategori_keuangan', $data);
    }

    public function delete_kategori($id)
    {
        $this->db->where('id_kategori', $id);
        return $this->db->delete('tb_kategori_keuangan');
    }

    // ==========================================
    // 2. LOGIKA INPUT TRANSAKSI (CREATE)
    // ==========================================

    public function get_unreported_events()
    {
        $this->db->select('id_peminjaman');
        $this->db->from('tb_keuangan_event');
        $subquery = $this->db->get_compiled_select();

        $this->db->select('*');
        $this->db->from('tb_peminjaman');
        $this->db->where("id_peminjaman NOT IN ($subquery)", NULL, FALSE);
        $this->db->order_by('tgl_pinjam', 'DESC');

        return $this->db->get()->result();
    }

    // Simpan Transaksi Lengkap + Logika Auto Kas (Updated: Gross Flow)
    public function simpan_transaksi_full($data_header, $data_ops, $data_gaji)
    {
        $this->db->trans_start();

        // 1. Simpan Header Keuangan
        $this->db->insert('tb_keuangan_event', $data_header);
        $id_keuangan = $this->db->insert_id();

        // Ambil Nama Event
        $info_event = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $data_header['id_peminjaman']])->row();
        $nama_event = $info_event ? $info_event->nama_event : 'Event ID ' . $data_header['id_peminjaman'];

        // 2. AUTO KAS - ARUS KAS REAL (Pemasukan & Pengeluaran Event)
        // Menggantikan logika lama yang hanya mencatat Profit/Bagi Hasil

        // A. Catat TOTAL PEMASUKAN EVENT (Uang Masuk)
        if ($data_header['total_pemasukan'] > 0) {
            $this->db->insert('tb_kas_umum', [
                'tanggal'       => date('Y-m-d'),
                'jenis'         => 'masuk',
                'kategori'      => 'Pemasukan Event',
                'nominal'       => $data_header['total_pemasukan'],
                'keterangan'    => 'Total Income: ' . $nama_event,
                'sumber_auto'   => 'event',
                'ref_id'        => $id_keuangan,
                'created_by'    => $this->session->userdata('id_user')
            ]);
        }

        // B. Catat TOTAL PENGELUARAN EVENT (Uang Keluar)
        // Hitung total belanja (SDM + Ops + Fee Marketing)
        $total_keluar_event = $data_header['total_biaya_sdm'] + $data_header['total_biaya_ops'];
        if (isset($data_header['fee_intern_nominal'])) $total_keluar_event += $data_header['fee_intern_nominal'];
        if (isset($data_header['fee_ekstern_nominal'])) $total_keluar_event += $data_header['fee_ekstern_nominal'];

        if ($total_keluar_event > 0) {
            $this->db->insert('tb_kas_umum', [
                'tanggal'       => date('Y-m-d'),
                'jenis'         => 'keluar',
                'kategori'      => 'Pengeluaran Event',
                'nominal'       => $total_keluar_event,
                'keterangan'    => 'Total Expense (SDM+Ops): ' . $nama_event,
                'sumber_auto'   => 'event',
                'ref_id'        => $id_keuangan,
                'created_by'    => $this->session->userdata('id_user')
            ]);
        }

        // 3. Simpan Biaya Operasional (Detail)
        if (!empty($data_ops)) {
            $batch_ops = [];
            foreach ($data_ops as $ops) {
                $ops['id_keuangan'] = $id_keuangan;
                $batch_ops[] = $ops;
            }
            if (count($batch_ops) > 0) {
                $this->db->insert_batch('tb_pengeluaran_ops', $batch_ops);
            }
        }

        // 4. Simpan Gaji SDM & Proses Potongan Kasbon
        if (!empty($data_gaji)) {
            $batch_gaji = [];
            foreach ($data_gaji as $gaji) {
                $gaji['id_keuangan'] = $id_keuangan;

                // LOGIKA POTONG KASBON (DIPERBAIKI AGAR BALANCE)
                if (isset($gaji['nominal_potongan']) && $gaji['nominal_potongan'] > 0) {
                    $sisa_potongan = $gaji['nominal_potongan'];
                    $total_terbayar = 0;
                    $id_user = $gaji['id_user'];

                    $this->db->where('id_user', $id_user);
                    $this->db->where('status', 'active');
                    $this->db->order_by('tanggal_pengajuan', 'ASC');
                    $active_kasbons = $this->db->get('tb_kasbon')->result();

                    foreach ($active_kasbons as $k) {
                        if ($sisa_potongan <= 0) break;
                        $bayar = ($sisa_potongan >= $k->sisa_tagihan) ? $k->sisa_tagihan : $sisa_potongan;

                        $data_bayar = [
                            'id_kasbon'     => $k->id_kasbon,
                            'tanggal_bayar' => date('Y-m-d'),
                            'nominal_bayar' => $bayar,
                            'metode'        => 'potong_gaji',
                            'id_keuangan'   => $id_keuangan,
                            'keterangan'    => 'Potong Gaji Event: ' . $nama_event
                        ];
                        $this->db->insert('tb_kasbon_bayar', $data_bayar);

                        // Update sisa tagihan
                        $sisa_baru = $k->sisa_tagihan - $bayar;
                        $update_data = ['sisa_tagihan' => $sisa_baru];
                        if ($sisa_baru <= 0) {
                            $update_data['status'] = 'lunas';
                        }
                        $this->db->where('id_kasbon', $k->id_kasbon);
                        $this->db->update('tb_kasbon', $update_data);

                        $sisa_potongan -= $bayar;
                        $total_terbayar += $bayar;
                    }

                    // Masukkan Uang Potongan ke Kas Umum agar Balance
                    // (Karena Pengeluaran Event mencatat Gaji FULL, maka Potongan harus masuk lagi sebagai Pemasukan Pelunasan)
                    if ($total_terbayar > 0) {
                        $user_info = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();
                        $this->db->insert('tb_kas_umum', [
                            'tanggal'       => date('Y-m-d'),
                            'jenis'         => 'masuk',
                            'kategori'      => 'Pelunasan Kasbon (Potong Gaji)',
                            'nominal'       => $total_terbayar,
                            'keterangan'    => 'Potong Gaji a.n ' . ($user_info ? $user_info->nama_lengkap : 'Karyawan') . ' (' . $nama_event . ')',
                            'sumber_auto'   => 'event',
                            'ref_id'        => $id_keuangan,
                            'created_by'    => $this->session->userdata('id_user')
                        ]);
                    }
                }
                $batch_gaji[] = $gaji;
            }

            if (count($batch_gaji) > 0) {
                $this->db->insert_batch('tb_pengeluaran_sdm', $batch_gaji);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ==========================================
    // 3. EDIT LAPORAN KEUANGAN (UPDATE)
    // ==========================================

    public function update_transaksi_full($id_keuangan, $data_header, $data_ops, $data_gaji)
    {
        $this->db->trans_start();

        // --- STEP 1: REVERT (BATALKAN EFEK LAPORAN LAMA) ---
        $riwayat_bayar = $this->db->get_where('tb_kasbon_bayar', ['id_keuangan' => $id_keuangan])->result();
        if (!empty($riwayat_bayar)) {
            foreach ($riwayat_bayar as $bayar) {
                $this->db->set('sisa_tagihan', 'sisa_tagihan + ' . $bayar->nominal_bayar, FALSE);
                $this->db->set('status', 'active');
                $this->db->where('id_kasbon', $bayar->id_kasbon);
                $this->db->update('tb_kasbon');
                $this->db->delete('tb_kasbon_bayar', ['id_bayar' => $bayar->id_bayar]);
            }
        }

        // Hapus Data Kas Lama (Pemasukan Event, Pengeluaran Event, Potongan Gaji)
        $this->db->delete('tb_kas_umum', ['sumber_auto' => 'event', 'ref_id' => $id_keuangan]);
        $this->db->delete('tb_pengeluaran_ops', ['id_keuangan' => $id_keuangan]);
        $this->db->delete('tb_pengeluaran_sdm', ['id_keuangan' => $id_keuangan]);


        // --- STEP 2: UPDATE DATA BARU (RE-APPLY) ---

        // A. Update Header
        $this->db->where('id_keuangan', $id_keuangan);
        $this->db->update('tb_keuangan_event', $data_header);

        $info_event = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $data_header['id_peminjaman']])->row();
        $nama_event = $info_event ? $info_event->nama_event : 'Event ID ' . $data_header['id_peminjaman'];

        // B. AUTO KAS - ARUS KAS REAL (Updated)

        // 1. Catat Total Pemasukan
        if ($data_header['total_pemasukan'] > 0) {
            $this->db->insert('tb_kas_umum', [
                'tanggal' => date('Y-m-d'),
                'jenis' => 'masuk',
                'kategori' => 'Pemasukan Event',
                'nominal' => $data_header['total_pemasukan'],
                'keterangan' => 'Total Income: ' . $nama_event,
                'sumber_auto' => 'event',
                'ref_id' => $id_keuangan,
                'created_by' => $this->session->userdata('id_user')
            ]);
        }

        // 2. Catat Total Pengeluaran
        $total_keluar_event = $data_header['total_biaya_sdm'] + $data_header['total_biaya_ops'];
        if (isset($data_header['fee_intern_nominal'])) $total_keluar_event += $data_header['fee_intern_nominal'];
        if (isset($data_header['fee_ekstern_nominal'])) $total_keluar_event += $data_header['fee_ekstern_nominal'];

        if ($total_keluar_event > 0) {
            $this->db->insert('tb_kas_umum', [
                'tanggal' => date('Y-m-d'),
                'jenis' => 'keluar',
                'kategori' => 'Pengeluaran Event',
                'nominal' => $total_keluar_event,
                'keterangan' => 'Total Expense (SDM+Ops): ' . $nama_event,
                'sumber_auto' => 'event',
                'ref_id' => $id_keuangan,
                'created_by' => $this->session->userdata('id_user')
            ]);
        }

        // C. Insert Ops Baru
        if (!empty($data_ops)) {
            $batch_ops = [];
            foreach ($data_ops as $ops) {
                $ops['id_keuangan'] = $id_keuangan;
                $batch_ops[] = $ops;
            }
            if (count($batch_ops) > 0) $this->db->insert_batch('tb_pengeluaran_ops', $batch_ops);
        }

        // D. Insert SDM Baru & Potong Kasbon Ulang
        if (!empty($data_gaji)) {
            $batch_gaji = [];
            foreach ($data_gaji as $gaji) {
                $gaji['id_keuangan'] = $id_keuangan;

                // LOGIKA POTONG KASBON
                if (isset($gaji['nominal_potongan']) && $gaji['nominal_potongan'] > 0) {
                    $sisa_potongan = $gaji['nominal_potongan'];
                    $total_terbayar = 0;
                    $id_user = $gaji['id_user'];

                    $this->db->where('id_user', $id_user);
                    $this->db->where('status', 'active');
                    $this->db->order_by('tanggal_pengajuan', 'ASC');
                    $active_kasbons = $this->db->get('tb_kasbon')->result();

                    foreach ($active_kasbons as $k) {
                        if ($sisa_potongan <= 0) break;
                        $bayar = ($sisa_potongan >= $k->sisa_tagihan) ? $k->sisa_tagihan : $sisa_potongan;

                        $data_bayar = [
                            'id_kasbon' => $k->id_kasbon,
                            'tanggal_bayar' => date('Y-m-d'),
                            'nominal_bayar' => $bayar,
                            'metode' => 'potong_gaji',
                            'id_keuangan' => $id_keuangan,
                            'keterangan' => 'Potong Gaji Event: ' . $nama_event
                        ];
                        $this->db->insert('tb_kasbon_bayar', $data_bayar);

                        $sisa_baru = $k->sisa_tagihan - $bayar;
                        $update_data = ['sisa_tagihan' => $sisa_baru];
                        if ($sisa_baru <= 0) $update_data['status'] = 'lunas';

                        $this->db->where('id_kasbon', $k->id_kasbon);
                        $this->db->update('tb_kasbon', $update_data);

                        $sisa_potongan -= $bayar;
                        $total_terbayar += $bayar;
                    }

                    if ($total_terbayar > 0) {
                        $user_info = $this->db->get_where('tb_users', ['id_user' => $id_user])->row();
                        $this->db->insert('tb_kas_umum', [
                            'tanggal'       => date('Y-m-d'),
                            'jenis'         => 'masuk',
                            'kategori'      => 'Pelunasan Kasbon (Potong Gaji)',
                            'nominal'       => $total_terbayar,
                            'keterangan'    => 'Potong Gaji a.n ' . ($user_info ? $user_info->nama_lengkap : 'Karyawan') . ' (' . $nama_event . ')',
                            'sumber_auto'   => 'event',
                            'ref_id'        => $id_keuangan,
                            'created_by'    => $this->session->userdata('id_user')
                        ]);
                    }
                }
                $batch_gaji[] = $gaji;
            }
            if (count($batch_gaji) > 0) $this->db->insert_batch('tb_pengeluaran_sdm', $batch_gaji);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ==========================================
    // 4. DATA VIEWING (READ & DELETE)
    // ==========================================

    public function get_all_laporan_keuangan()
    {
        $this->db->select('k.*, p.nama_event, p.lokasi_event, p.kode_transaksi');
        $this->db->from('tb_keuangan_event k');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman');
        $this->db->order_by('k.tgl_laporan', 'DESC');
        return $this->db->get()->result();
    }

    public function get_keuangan_by_id($id_keuangan)
    {
        $this->db->select('
            k.*, 
            p.nama_event, 
            p.lokasi_event, 
            p.tgl_pinjam, 
            p.tgl_kembali_realisasi, 
            p.tgl_kembali_rencana, 
            p.kode_transaksi,
            p.id_operator, 
            p.petugas_tambahan
        ');
        $this->db->from('tb_keuangan_event k');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman');
        $this->db->where('k.id_keuangan', $id_keuangan);
        return $this->db->get()->row();
    }

    public function get_sdm_by_id($id_keuangan)
    {
        $this->db->select('s.*, u.nama_lengkap');
        $this->db->from('tb_pengeluaran_sdm s');
        $this->db->join('tb_users u', 'u.id_user = s.id_user');
        $this->db->where('s.id_keuangan', $id_keuangan);
        return $this->db->get()->result();
    }

    public function get_ops_by_id($id_keuangan)
    {
        $this->db->select('o.*, k.nama_kategori');
        $this->db->from('tb_pengeluaran_ops o');
        $this->db->join('tb_kategori_keuangan k', 'k.id_kategori = o.id_kategori');
        $this->db->where('o.id_keuangan', $id_keuangan);
        return $this->db->get()->result();
    }

    public function delete_keuangan($id_keuangan)
    {
        $this->db->trans_start();

        // 1. REVERT PEMBAYARAN KASBON
        $riwayat_bayar = $this->db->get_where('tb_kasbon_bayar', ['id_keuangan' => $id_keuangan])->result();
        if (!empty($riwayat_bayar)) {
            foreach ($riwayat_bayar as $bayar) {
                $this->db->set('sisa_tagihan', 'sisa_tagihan + ' . $bayar->nominal_bayar, FALSE);
                $this->db->set('status', 'active');
                $this->db->where('id_kasbon', $bayar->id_kasbon);
                $this->db->update('tb_kasbon');
                $this->db->delete('tb_kasbon_bayar', ['id_bayar' => $bayar->id_bayar]);
            }
        }

        // 2. Hapus data kas umum otomatis terkait
        $this->db->delete('tb_kas_umum', ['sumber_auto' => 'event', 'ref_id' => $id_keuangan]);

        // 3. Hapus data detail laporan & header
        $this->db->delete('tb_pengeluaran_ops', ['id_keuangan' => $id_keuangan]);
        $this->db->delete('tb_pengeluaran_sdm', ['id_keuangan' => $id_keuangan]);
        $this->db->delete('tb_keuangan_event', ['id_keuangan' => $id_keuangan]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // ==========================================
    // 5. PAYROLL / GAJI OPERATOR
    // ==========================================

    public function get_rekap_payroll($bulan = null, $tahun = null)
    {
        $this->db->select('
            u.id_user, u.nama_lengkap, u.username, 
            COUNT(s.id_gaji) as total_event, 
            SUM(s.total_diterima) as total_pendapatan
        ');
        $this->db->from('tb_users u');
        $this->db->join('tb_pengeluaran_sdm s', 's.id_user = u.id_user', 'left');
        $this->db->join('tb_keuangan_event k', 'k.id_keuangan = s.id_keuangan', 'left');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman', 'left');

        $this->db->where('u.role', 'operator');

        if ($bulan && $tahun) {
            $this->db->where('MONTH(p.tgl_pinjam)', $bulan);
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        } elseif ($tahun) {
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        }

        $this->db->group_by('u.id_user');
        $this->db->order_by('total_pendapatan', 'DESC');

        return $this->db->get()->result();
    }

    public function get_detail_payroll_user($id_user, $bulan = null, $tahun = null)
    {
        $this->db->select('
            s.*, 
            p.nama_event, p.tgl_pinjam, p.tgl_kembali_realisasi, p.kode_transaksi, p.lokasi_event,
            k.id_keuangan
        ');
        $this->db->from('tb_pengeluaran_sdm s');
        $this->db->join('tb_keuangan_event k', 'k.id_keuangan = s.id_keuangan');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman');

        $this->db->where('s.id_user', $id_user);

        if ($bulan && $tahun) {
            $this->db->where('MONTH(p.tgl_pinjam)', $bulan);
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        } elseif ($tahun) {
            $this->db->where('YEAR(p.tgl_pinjam)', $tahun);
        }

        $this->db->order_by('p.tgl_pinjam', 'DESC');
        return $this->db->get()->result();
    }

    public function get_single_payroll_item($id_keuangan, $id_user)
    {
        $this->db->select('
            s.*, 
            k.tgl_laporan, 
            p.nama_event, p.lokasi_event, p.tgl_pinjam, p.tgl_kembali_rencana, p.kode_transaksi, 
            u.nama_lengkap, u.username, u.role
        ');
        $this->db->from('tb_pengeluaran_sdm s');
        $this->db->join('tb_keuangan_event k', 'k.id_keuangan = s.id_keuangan');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman');
        $this->db->join('tb_users u', 'u.id_user = s.id_user');

        $this->db->where('s.id_keuangan', $id_keuangan);
        $this->db->where('s.id_user', $id_user);

        return $this->db->get()->row();
    }

    // ==========================================
    // 6. MANAJEMEN KASBON (PINJAMAN)
    // ==========================================

    public function get_all_kasbon()
    {
        $this->db->select('k.*, u.nama_lengkap, u.username');
        $this->db->from('tb_kasbon k');
        $this->db->join('tb_users u', 'u.id_user = k.id_user');
        $this->db->order_by('k.status', 'ASC');
        $this->db->order_by('k.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_kasbon_by_user($id_user)
    {
        $this->db->select('*');
        $this->db->from('tb_kasbon');
        $this->db->where('id_user', $id_user);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_total_hutang_user($id_user)
    {
        $this->db->select_sum('sisa_tagihan');
        $this->db->where('id_user', $id_user);
        $this->db->where('status', 'active');
        $result = $this->db->get('tb_kasbon')->row();
        return $result->sisa_tagihan ?: 0;
    }

    public function insert_kasbon($data)
    {
        return $this->db->insert('tb_kasbon', $data);
    }

    // [AUTO KAS OUT] Saat Kasbon ACC
    public function update_status_kasbon($id_kasbon, $status)
    {
        $this->db->trans_start();
        $this->db->where('id_kasbon', $id_kasbon);
        $this->db->update('tb_kasbon', ['status' => $status]);

        if ($status == 'active') {
            $kasbon = $this->db->get_where('tb_kasbon', ['id_kasbon' => $id_kasbon])->row();
            $user = $this->db->get_where('tb_users', ['id_user' => $kasbon->id_user])->row();

            $this->db->insert('tb_kas_umum', [
                'tanggal'       => date('Y-m-d'),
                'jenis'         => 'keluar',
                'kategori'      => 'Pencairan Kasbon',
                'nominal'       => $kasbon->nominal_pinjaman,
                'keterangan'    => 'Pencairan Pinjaman: ' . $user->nama_lengkap,
                'sumber_auto'   => 'kasbon',
                'ref_id'        => $id_kasbon,
                'created_by'    => $this->session->userdata('id_user')
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // [AUTO KAS IN] Saat Bayar Tunai
    public function bayar_kasbon($id_kasbon, $data_bayar)
    {
        $this->db->trans_start();
        $this->db->insert('tb_kasbon_bayar', $data_bayar);

        $this->db->set('sisa_tagihan', 'sisa_tagihan - ' . $data_bayar['nominal_bayar'], FALSE);
        $this->db->where('id_kasbon', $id_kasbon);
        $this->db->update('tb_kasbon');

        $cek = $this->db->get_where('tb_kasbon', ['id_kasbon' => $id_kasbon])->row();
        if ($cek->sisa_tagihan <= 0) {
            $this->db->where('id_kasbon', $id_kasbon);
            $this->db->update('tb_kasbon', ['status' => 'lunas', 'sisa_tagihan' => 0]);
        }

        if ($data_bayar['metode'] == 'tunai') {
            $kasbon = $this->db->get_where('tb_kasbon', ['id_kasbon' => $id_kasbon])->row();
            $user = $this->db->get_where('tb_users', ['id_user' => $kasbon->id_user])->row();

            $this->db->insert('tb_kas_umum', [
                'tanggal'       => date('Y-m-d'),
                'jenis'         => 'masuk',
                'kategori'      => 'Pelunasan Kasbon Tunai',
                'nominal'       => $data_bayar['nominal_bayar'],
                'keterangan'    => 'Setoran Pelunasan: ' . $user->nama_lengkap,
                'sumber_auto'   => 'kasbon',
                'ref_id'        => $id_kasbon,
                'created_by'    => $this->session->userdata('id_user')
            ]);
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_history_bayar($id_kasbon)
    {
        return $this->db->get_where('tb_kasbon_bayar', ['id_kasbon' => $id_kasbon])->result();
    }

    // ==========================================
    // 7. BUKU KAS UMUM (GENERAL LEDGER)
    // ==========================================

    public function get_buku_kas($bulan = null, $tahun = null)
    {
        $this->db->select('*');
        $this->db->from('tb_kas_umum');
        if ($bulan && $tahun) {
            $this->db->where('MONTH(tanggal)', $bulan);
            $this->db->where('YEAR(tanggal)', $tahun);
        } elseif ($tahun) {
            $this->db->where('YEAR(tanggal)', $tahun);
        }
        $this->db->order_by('tanggal', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    // [BARU] EDIT TRANSAKSI MANUAL
    public function get_kas_by_id($id_kas)
    {
        return $this->db->get_where('tb_kas_umum', ['id_kas' => $id_kas])->row();
    }

    public function insert_kas_manual($data)
    {
        return $this->db->insert('tb_kas_umum', $data);
    }

    public function update_kas_manual($id_kas, $data)
    {
        $this->db->where('id_kas', $id_kas);
        $this->db->where('sumber_auto', 'manual'); // Security
        return $this->db->update('tb_kas_umum', $data);
    }

    public function delete_kas_manual($id_kas)
    {
        $this->db->where('id_kas', $id_kas);
        $this->db->where('sumber_auto', 'manual');
        return $this->db->delete('tb_kas_umum');
    }

    public function get_saldo_akhir()
    {
        $this->db->select_sum('nominal');
        $masuk = $this->db->get_where('tb_kas_umum', ['jenis' => 'masuk'])->row()->nominal;

        $this->db->select_sum('nominal');
        $keluar = $this->db->get_where('tb_kas_umum', ['jenis' => 'keluar'])->row()->nominal;

        return ($masuk - $keluar);
    }

    // ==========================================
    // 8. DASHBOARD STATISTICS [UPDATE]
    // ==========================================

    public function get_dashboard_stats()
    {
        $data = [];

        // 1. TOTAL UANG FISIK (Brankas Utama)
        $data['saldo_real'] = $this->get_saldo_akhir();

        // 2. DATA ALOKASI PROFIT (Akumulasi dari Tabel Event)
        // [PENTING] Data ini tetap diambil dari tb_keuangan_event meskipun tb_kas_umumnya sudah berubah
        $this->db->select_sum('kas_pt_nominal', 'total_kas_pt');
        $this->db->select_sum('angsuran_nominal', 'total_angsuran_alloc');
        $this->db->select_sum('royalti_nominal', 'total_royalti_alloc');
        $this->db->select_sum('total_pemasukan', 'omset_bruto');
        $q_event = $this->db->get('tb_keuangan_event')->row();

        $data['total_kas_pt_alloc']     = $q_event->total_kas_pt ?: 0;
        $data['total_angsuran_alloc']   = $q_event->total_angsuran_alloc ?: 0;
        $data['total_royalti_alloc']    = $q_event->total_royalti_alloc ?: 0;
        $data['omset_bruto']            = $q_event->omset_bruto ?: 0;

        // 3. HITUNG POS ANGSURAN (Saldo Virtual)
        $this->db->select_sum('nominal');
        $this->db->where('jenis', 'keluar');
        $this->db->like('kategori', 'Pembayaran Angsuran');
        $q_bayar_angsuran = $this->db->get('tb_kas_umum')->row();
        $total_bayar_angsuran = $q_bayar_angsuran->nominal ?: 0;

        $data['saldo_angsuran_now'] = $data['total_angsuran_alloc'] - $total_bayar_angsuran;

        // 4. HITUNG POS ROYALTI (Saldo Virtual)
        $this->db->select_sum('nominal');
        $this->db->where('jenis', 'keluar');
        $this->db->like('kategori', 'Pembayaran Royalti');
        $q_bayar_royalti = $this->db->get('tb_kas_umum')->row();
        $total_bayar_royalti = $q_bayar_royalti->nominal ?: 0;

        $data['saldo_royalti_now'] = $data['total_royalti_alloc'] - $total_bayar_royalti;

        // 5. HITUNG POS KAS PT (Available)
        // Saldo Real - (Angsuran + Royalti)
        $data['saldo_kas_pt_now'] = $data['saldo_real'] - $data['saldo_angsuran_now'] - $data['saldo_royalti_now'];

        // 6. ASET PIUTANG & PENDING
        $this->db->select_sum('sisa_tagihan');
        $this->db->where('status', 'active');
        $q_kasbon = $this->db->get('tb_kasbon')->row();
        $data['piutang_karyawan'] = $q_kasbon->sisa_tagihan ?: 0;

        $unreported = $this->get_unreported_events();
        $data['event_pending'] = count($unreported);

        return $data;
    }

    public function get_recent_transactions($limit = 5)
    {
        $this->db->order_by('tanggal', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tb_kas_umum', $limit)->result();
    }

    public function get_trend_keuangan()
    {
        $query = $this->db->query("
            SELECT 
                DATE_FORMAT(tanggal, '%Y-%m') as periode,
                DATE_FORMAT(tanggal, '%M') as nama_bulan,
                SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE 0 END) as total_masuk,
                SUM(CASE WHEN jenis = 'keluar' THEN nominal ELSE 0 END) as total_keluar
            FROM tb_kas_umum
            WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY periode
            ORDER BY periode ASC
        ");
        return $query->result();
    }
}
