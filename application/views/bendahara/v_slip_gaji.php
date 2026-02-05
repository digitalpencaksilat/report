<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Rincian Pendapatan</h1>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-secondary mr-2">
                <i class="fas fa-print"></i> Cetak Rekap
            </button>
            <a href="<?= base_url('bendahara/payroll?bulan=' . $filter_bulan . '&tahun=' . $filter_tahun) ?>" class="btn btn-sm btn-dark">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- INFO HEADER -->
    <div class="card shadow mb-4 border-left-info">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-dark"><?= $user->nama_lengkap ?></h5>
                    <p class="mb-0 text-muted">Role: <?= ucfirst($user->role) ?></p>
                </div>
                <div class="col-md-6 text-md-right">
                    <h6 class="text-muted">Periode Laporan</h6>
                    <h4 class="font-weight-bold text-primary">
                        <?= $filter_bulan ? date('F', mktime(0, 0, 0, $filter_bulan, 10)) : 'Semua Bulan' ?>
                        <?= $filter_tahun ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RINCIAN -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-dark">Detail Event & Pendapatan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="bg-light text-center">
                        <tr>
                            <th class="align-middle">Tanggal</th>
                            <th class="align-middle text-left">Event</th>
                            <th class="align-middle">Honor Pokok</th>
                            <th class="align-middle">Setting</th>
                            <th class="align-middle">Transport</th>
                            <th class="align-middle">Bonus</th>
                            <th class="align-middle">Data</th>
                            <th class="align-middle">Total</th>
                            <th class="align-middle" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        if (empty($items)):
                        ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">Tidak ada data pendapatan pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $i):
                                $grand_total += $i->total_diterima;
                                $honor_total = $i->honor_harian * $i->jumlah_hari;
                            ?>
                                <tr>
                                    <td class="text-center align-middle"><?= date('d/m/Y', strtotime($i->tgl_pinjam)) ?></td>
                                    <td class="align-middle">
                                        <b><?= $i->nama_event ?></b><br>
                                        <small class="text-muted"><?= $i->lokasi_event ?></small><br>
                                        <span class="badge badge-secondary" style="font-size: 0.7em"><?= $i->peran ?></span>
                                    </td>
                                    <td class="text-right align-middle">
                                        <?= number_format($honor_total, 0, ',', '.') ?>
                                        <br><small class="text-muted">(<?= $i->jumlah_hari ?> Hari)</small>
                                    </td>
                                    <td class="text-right align-middle"><?= number_format($i->nominal_setting, 0, ',', '.') ?></td>
                                    <td class="text-right align-middle"><?= number_format($i->nominal_transport, 0, ',', '.') ?></td>
                                    <td class="text-right align-middle"><?= number_format($i->nominal_bonus, 0, ',', '.') ?></td>
                                    <td class="text-right align-middle"><?= number_format($i->nominal_data, 0, ',', '.') ?></td>
                                    <td class="text-right align-middle font-weight-bold text-dark bg-light">
                                        <?= number_format($i->total_diterima, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?= base_url('bendahara/slip_event/' . $i->id_keuangan . '/' . $i->id_user) ?>" class="btn btn-sm btn-primary shadow-sm" title="Lihat Slip Event" target="_blank">
                                            <i class="fas fa-file-invoice"></i> Slip
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-dark text-white">
                            <td colspan="7" class="text-right font-weight-bold pr-3 align-middle">TOTAL PENDAPATAN BERSIH:</td>
                            <td colspan="2" class="text-left font-weight-bold align-middle" style="font-size: 1.1rem;">
                                Rp <?= number_format($grand_total, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>