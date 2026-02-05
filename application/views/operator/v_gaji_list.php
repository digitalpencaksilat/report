<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Gaji Saya</h1>
    </div>

    <!-- FILTER PERIODE -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body py-3 bg-light border-bottom-primary">
            <form method="get" action="" class="form-row align-items-end">
                <div class="col-auto">
                    <label class="small font-weight-bold text-muted mb-1">Bulan</label>
                    <select name="bulan" class="form-control form-control-sm">
                        <option value="">-- Semua Bulan --</option>
                        <?php
                        $bulan_list = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        foreach ($bulan_list as $key => $val):
                        ?>
                            <option value="<?= $key ?>" <?= ($filter_bulan == $key) ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="small font-weight-bold text-muted mb-1">Tahun</label>
                    <select name="tahun" class="form-control form-control-sm">
                        <?php
                        $tahun_sekarang = date('Y');
                        for ($t = $tahun_sekarang; $t >= 2023; $t--):
                        ?>
                            <option value="<?= $t ?>" <?= ($filter_tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL RINCIAN -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-dark">Riwayat Pendapatan Honorarium</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="bg-dark text-white text-center">
                        <tr>
                            <th class="align-middle" width="15%">Tanggal</th>
                            <th class="align-middle text-left">Event & Lokasi</th>
                            <th class="align-middle text-right" width="20%">Total Diterima</th>
                            <th class="align-middle" width="10%">Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        if (empty($items)):
                        ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data pendapatan pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $i):
                                $grand_total += $i->total_diterima;
                            ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <span class="font-weight-bold text-dark"><?= date('d M Y', strtotime($i->tgl_pinjam)) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <b class="text-primary"><?= $i->nama_event ?></b><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i> <?= $i->lokasi_event ?></small>
                                        <div class="mt-1">
                                            <span class="badge badge-secondary" style="font-size: 0.7em"><?= $i->peran ?></span>
                                        </div>
                                    </td>
                                    <td class="text-right align-middle font-weight-bold text-success" style="font-size: 1.1rem;">
                                        Rp <?= number_format($i->total_diterima, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?= base_url('gaji/slip/' . $i->id_keuangan) ?>" class="btn btn-sm btn-primary shadow-sm" title="Download Slip">
                                            <i class="fas fa-file-invoice"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($items)): ?>
                        <tfoot>
                            <tr class="bg-light text-dark">
                                <td colspan="2" class="text-right font-weight-bold pr-3 align-middle text-uppercase">Total Pendapatan Periode Ini:</td>
                                <td class="text-right font-weight-bold align-middle" style="font-size: 1.2rem;">
                                    Rp <?= number_format($grand_total, 0, ',', '.') ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>