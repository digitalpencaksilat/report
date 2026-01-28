<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Riwayat Pemakaian</h1>
            <p class="mb-0 text-muted">Tracking log untuk barang: <b class="text-primary"><?= $nama_barang ?></b></p>
        </div>
        <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Master
        </a>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Timeline Penggunaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Tanggal Event</th>
                            <th>Nama Event / Lokasi</th>
                            <th>PJ (Operator)</th>
                            <th class="text-center">Qty Dipakai</th>
                            <th class="text-center">Qty Kembali</th>
                            <th class="text-center">Kondisi Balik</th>
                            <th class="text-center">Status Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-3x mb-3 text-gray-300"></i><br>
                                    Barang ini belum pernah dipakai dalam event apapun.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $r): ?>
                                <tr>
                                    <td class="align-middle">
                                        <?= date('d M Y', strtotime($r->tgl_pinjam)) ?>
                                    </td>
                                    <td class="align-middle">
                                        <b><?= $r->nama_event ?></b><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= $r->lokasi_event ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <?= $r->pj_nama ?>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <?= $r->qty_pinjam ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($r->qty_kembali == $r->qty_pinjam): ?>
                                            <span class="text-success font-weight-bold"><?= $r->qty_kembali ?></span>
                                        <?php else: ?>
                                            <span class="text-danger font-weight-bold"><?= $r->qty_kembali ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($r->kondisi_kembali): ?>
                                            <?php if ($r->kondisi_kembali == 'baik'): ?>
                                                <span class="badge badge-success">Baik</span>
                                            <?php elseif ($r->kondisi_kembali == 'rusak'): ?>
                                                <span class="badge badge-warning">Rusak</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Hilang</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($r->status == 'selesai'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Sedang Berjalan</span>
                                        <?php endif; ?>
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