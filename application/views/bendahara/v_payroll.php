<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Payroll Operator</h1>
        <p class="mb-0 text-muted">Rekapitulasi pendapatan personil lapangan.</p>
    </div>

    <!-- FILTER FORM -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body py-3 bg-light border-bottom-primary">
            <form method="get" action="" class="form-row align-items-end">
                <div class="col-auto">
                    <label class="small font-weight-bold text-muted mb-1">Filter Bulan</label>
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
                    <a href="<?= base_url('bendahara/payroll') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL REKAP -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pendapatan Operator</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Operator</th>
                            <th class="text-center">Jumlah Event</th>
                            <th class="text-right">Total Pendapatan (Rp)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($payroll as $p): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <b><?= $p->nama_lengkap ?></b><br>
                                    <small class="text-muted">@<?= $p->username ?></small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info px-2 py-1"><?= $p->total_event ?> Event</span>
                                </td>
                                <td class="align-middle text-right font-weight-bold text-success">
                                    Rp <?= number_format($p->total_pendapatan, 0, ',', '.') ?>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('bendahara/slip_gaji/' . $p->id_user . '?bulan=' . $filter_bulan . '&tahun=' . $filter_tahun) ?>" class="btn btn-sm btn-brand shadow-sm">
                                        <i class="fas fa-file-invoice-dollar mr-1"></i> Rincian / Slip
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>