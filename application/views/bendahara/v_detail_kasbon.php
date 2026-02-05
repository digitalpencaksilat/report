<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Detail Kasbon & Riwayat Pembayaran</h1>
        <a href="<?= base_url('bendahara/kasbon') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- DETAIL HEADER KASBON -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Informasi Peminjam</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $kasbon->nama_lengkap ?></div>
                            <p class="text-muted small mb-0"><?= ucfirst($kasbon->role) ?> - @<?= $kasbon->username ?></p>
                            <hr>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pinjaman</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($kasbon->nominal_pinjaman, 0, ',', '.') ?></div>
                            <small class="text-muted">Diajukan: <?= date('d M Y', strtotime($kasbon->tanggal_pengajuan)) ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATUS TAGIHAN -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sisa Tagihan (Hutang)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($kasbon->sisa_tagihan, 0, ',', '.') ?></div>
                            <div class="mt-2">
                                <?php if ($kasbon->status == 'lunas'): ?>
                                    <span class="badge badge-success px-3 py-2">LUNAS</span>
                                <?php elseif ($kasbon->status == 'active'): ?>
                                    <span class="badge badge-warning px-3 py-2">BELUM LUNAS</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary px-3 py-2"><?= strtoupper($kasbon->status) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KETERANGAN -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Keterangan / Keperluan</div>
                    <p class="mb-0 text-gray-800"><?= $kasbon->keterangan ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT PEMBAYARAN -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran Cicilan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode Pembayaran</th>
                            <th>Keterangan</th>
                            <th class="text-right">Nominal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pembayaran untuk kasbon ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            $total_bayar = 0;
                            foreach ($history as $h): $total_bayar += $h->nominal_bayar; ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d M Y', strtotime($h->tanggal_bayar)) ?></td>
                                    <td>
                                        <?php if ($h->metode == 'potong_gaji'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-cut mr-1"></i> Potong Gaji</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fas fa-money-bill-wave mr-1"></i> Setor Tunai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $h->keterangan ?>
                                        <?php if ($h->id_keuangan): ?>
                                            <a href="<?= base_url('bendahara/detail/' . $h->id_keuangan) ?>" target="_blank" class="ml-1 text-primary"><i class="fas fa-external-link-alt"></i> Cek Event</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        Rp <?= number_format($h->nominal_bayar, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($history)): ?>
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right pr-3">Total Telah Dibayar:</td>
                                <td class="text-right text-success">Rp <?= number_format($total_bayar, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>