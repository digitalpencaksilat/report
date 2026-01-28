<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Live Tracking Barang</h1>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
            <i class="fas fa-sync-alt mr-1"></i> Refresh Data
        </button>
    </div>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-xl-12 col-md-12">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Info Monitoring</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($barang_keluar) ?> Item Sedang Diluar
                            </div>
                            <small class="text-muted">Data ini menampilkan barang yang status laporannya masih <b>Draft</b> atau <b>Dipakai/Locked</b>.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dolly-flatbed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white border-bottom-warning">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-map-marked-alt mr-2"></i>Lokasi Barang Saat Ini
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>Barang</th>
                            <th class="text-center">Qty Keluar</th>
                            <th>Sedang di Event</th>
                            <th>Tanggal Pinjam</th>
                            <th>Penanggung Jawab</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barang_keluar as $row): ?>
                            <tr>
                                <td class="align-middle">
                                    <b><?= $row->nama_barang ?></b><br>
                                    <small class="text-muted"><?= $row->kode_barang ?></small>
                                </td>
                                <td class="align-middle text-center font-weight-bold text-danger">
                                    <?= $row->qty_pinjam ?>
                                </td>
                                <td class="align-middle">
                                    <?= $row->nama_event ?>
                                </td>
                                <td class="align-middle">
                                    <?= date('d M Y', strtotime($row->tgl_pinjam)) ?><br>
                                    <small class="text-muted">s/d <?= date('d M Y', strtotime($row->tgl_kembali_rencana)) ?></small>
                                </td>
                                <td class="align-middle">
                                    <i class="fas fa-user-circle mr-1 text-gray-400"></i><?= $row->pj_nama ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?php if ($row->status == 'draft'): ?>
                                        <span class="badge badge-info">Draft</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-white">Sedang Dipakai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="<?= base_url('laporan/detail/' . $row->id_peminjaman) ?>" class="btn btn-sm btn-primary btn-circle" title="Lihat Laporan">
                                        <i class="fas fa-eye"></i>
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