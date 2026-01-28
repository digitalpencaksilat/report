<div class="container-fluid">

    <!-- SET TIMEZONE INDONESIA (WIB) -->
    <?php date_default_timezone_set('Asia/Jakarta'); ?>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Dashboard Overview</h1>
        <div class="text-muted small">
            <i class="fas fa-calendar mr-1"></i> <?= date('d F Y') ?> <span class="ml-2"><i class="fas fa-clock mr-1"></i> <?= date('H:i') ?> WIB</span>
        </div>
    </div>

    <!-- --------------------------------- -->
    <!-- TAMPILAN ADMIN -->
    <!-- --------------------------------- -->
    <?php if ($this->session->userdata('role') == 'admin'): ?>

        <!-- Content Row Stats -->
        <div class="row">

            <!-- Card: Total Aset Barang -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100 py-2" style="border-left: 5px solid var(--brand-primary);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: var(--brand-primary);">
                                    Total Aset Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_barang ?> <small>Item</small></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Barang Keluar (Dipinjam) -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100 py-2" style="border-left: 5px solid var(--brand-secondary);">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Sedang Dipinjam</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $barang_keluar ?> <small>Unit</small></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dolly fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Event Aktif -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100 py-2" style="border-left: 5px solid #1cc88a;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Event Berjalan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $event_aktif ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Stok Menipis -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100 py-2" style="border-left: 5px solid #e74a3b;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Stok Menipis (< 5)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stok_kritis ?> <small>Jenis</small></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Row: Logs & Actions -->
            <div class="row">
                <!-- Log Aktivitas Stok -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                            <h6 class="m-0 font-weight-bold" style="color: var(--brand-primary);"><i class="fas fa-history mr-2"></i>Riwayat Perubahan Stok</h6>
                            <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-brand">Ke Master Barang</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Barang</th>
                                            <th>Perubahan</th>
                                            <th>User</th>
                                            <th>Ket</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($logs)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Belum ada aktivitas stok.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <!-- Menampilkan waktu log yang sudah disesuaikan timezone Asia/Jakarta -->
                                                    <td><small><?= date('d/m H:i', strtotime($log->created_at)) ?> WIB</small></td>
                                                    <td><?= $log->nama_barang ?></td>
                                                    <td class="text-center font-weight-bold">
                                                        <?php if ($log->qty_perubahan > 0): ?>
                                                            <span class="text-success">+<?= $log->qty_perubahan ?></span>
                                                        <?php else: ?>
                                                            <span class="text-danger"><?= $log->qty_perubahan ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><small><?= $log->nama_lengkap ?></small></td>
                                                    <td><small class="text-muted"><?= $log->keterangan ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Admin -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-dark">Aksi Cepat</h6>
                        </div>
                        <div class="card-body">
                            <p>Pintasan menu untuk pengelolaan:</p>
                            <a href="<?= base_url('barang') ?>" class="btn btn-light btn-block text-left mb-2 border">
                                <i class="fas fa-box-open mr-2 text-primary"></i> Tambah Stok Barang
                            </a>
                            <a href="<?= base_url('laporan') ?>" class="btn btn-light btn-block text-left mb-2 border">
                                <i class="fas fa-file-alt mr-2 text-warning"></i> Cek Laporan Masuk
                            </a>
                            <a href="<?= base_url('users') ?>" class="btn btn-light btn-block text-left border">
                                <i class="fas fa-users-cog mr-2 text-success"></i> Kelola Operator
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <!-- --------------------------------- -->
            <!-- TAMPILAN OPERATOR -->
            <!-- --------------------------------- -->
        <?php else: ?>

            <div class="row">
                <!-- Card: Event Saya -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card h-100 py-2" style="border-left: 5px solid var(--brand-primary);">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: var(--brand-primary);">
                                        Event Aktif Saya</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $my_active_events ?> <small>Kegiatan</small></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Tanggung Jawab Barang -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card h-100 py-2" style="border-left: 5px solid var(--brand-secondary);">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Barang Dalam Tanggung Jawab</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $my_items ?> <small>Unit</small></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                            <h6 class="m-0 font-weight-bold text-dark">Daftar Kegiatan Berjalan</h6>
                            <a href="<?= base_url('laporan/buat_baru') ?>" class="btn btn-sm btn-brand"><i class="fas fa-plus mr-1"></i> Buat Laporan Baru</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Tgl Pinjam</th>
                                            <th>Nama Event</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($active_events_list)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Tidak ada event aktif saat ini.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($active_events_list as $ev): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($ev->tgl_pinjam)) ?></td>
                                                    <td>
                                                        <b><?= $ev->nama_event ?></b><br>
                                                        <small class="text-muted"><?= $ev->kode_transaksi ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($ev->is_locked == 1): ?>
                                                            <span class="badge badge-dark">Dipakai / Locked</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-info">Draft</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('laporan/detail/' . $ev->id_peminjaman) ?>" class="btn btn-sm btn-primary">
                                                            Detail <i class="fas fa-arrow-right ml-1"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        </div>