<div class="container-fluid">

    <!-- Header & Info Operator -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;"><?= $operator->nama_lengkap ?></h1>
            <span class="badge badge-secondary"><?= $operator->username ?></span>
        </div>
        <a href="<?= base_url('riwayat') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- [FILTER FORM] -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body py-3 bg-light border-bottom-primary">
            <form method="get" action="" class="form-row align-items-end">
                <div class="col-auto">
                    <label class="small font-weight-bold text-muted mb-1">Filter Bulan</label>
                    <select name="bulan" class="form-control form-control-sm">
                        <option value="">-- Semua Bulan --</option>
                        <?php
                        $bulan_list = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                        foreach ($bulan_list as $key => $val):
                        ?>
                            <option value="<?= $key ?>" <?= ($filter_bulan == $key) ? 'selected' : '' ?>>
                                <?= $val ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="small font-weight-bold text-muted mb-1">Tahun</label>
                    <select name="tahun" class="form-control form-control-sm">
                        <option value="">-- Semua Tahun --</option>
                        <?php
                        $tahun_sekarang = date('Y');
                        for ($t = $tahun_sekarang; $t >= 2023; $t--):
                        ?>
                            <option value="<?= $t ?>" <?= ($filter_tahun == $t) ? 'selected' : '' ?>>
                                <?= $t ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Terapkan
                    </button>
                    <?php if (!empty($filter_bulan) || !empty($filter_tahun)): ?>
                        <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-secondary ml-1">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards Statistik (Dinamis ikut Filter) -->
    <div class="row mb-4">
        <!-- Total Event -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Penugasan
                                <?php if ($filter_bulan): ?>
                                    (Bulan Ini)
                                <?php endif; ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_event'] ?> Event</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Hari Kerja -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Jam Terbang
                                <?php if ($filter_bulan): ?>
                                    (Bulan Ini)
                                <?php endif; ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_hari'] ?> Hari</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rincian -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-dark">Rincian Riwayat Tugas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Tanggal Event</th>
                            <th>Durasi</th>
                            <th>Nama Event / Lokasi</th>
                            <th class="text-center">Peran</th>
                            <th class="text-center">Status Laporan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x mb-3 text-gray-300"></i><br>
                                    Tidak ditemukan riwayat tugas pada periode yang dipilih.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $ev): ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= date('d M Y', strtotime($ev->tgl_pinjam)) ?>
                                        <br><small class="text-muted">s/d <?= date('d M Y', strtotime($ev->tgl_kembali_rencana)) ?></small>
                                    </td>
                                    <td class="align-middle font-weight-bold text-primary">
                                        <?= $ev->durasi_hari ?> Hari
                                    </td>
                                    <td class="align-middle">
                                        <b class="text-dark"><?= $ev->nama_event ?></b>
                                        <br><small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= $ev->lokasi_event ?></small>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($ev->id_operator == $operator->id_user): ?>
                                            <span class="badge badge-primary px-2">Ketua Tim (PJ)</span>
                                        <?php else: ?>
                                            <span class="badge badge-info px-2">Pendamping</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($ev->status == 'selesai'): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php elseif ($ev->status == 'dipakai'): ?>
                                            <span class="badge badge-warning">Sedang Bertugas</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="<?= base_url('laporan/detail/' . $ev->id_peminjaman) ?>" class="btn btn-sm btn-info btn-circle" title="Lihat Laporan">
                                            <i class="fas fa-eye"></i>
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