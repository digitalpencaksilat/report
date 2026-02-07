<div class="container-fluid">
    <!-- TOOLBAR ATAS -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Buku Kas Umum</h1>

        <!-- Action Buttons -->
        <div>
            <!-- Link ke Halaman Cetak (Membuka Tab Baru) -->
            <?php
            $link_cetak = base_url('bendahara/cetak_buku_kas?tahun=' . $filter_tahun);
            if ($filter_bulan) $link_cetak .= '&bulan=' . $filter_bulan;
            ?>
            <a href="<?= $link_cetak ?>" target="_blank" class="btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Preview & Download PDF
            </a>

            <!-- Tombol Tambah Manual -->
            <button class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalTambahKas">
                <i class="fas fa-plus mr-1"></i> Transaksi Manual
            </button>
        </div>
    </div>

    <!-- INFO CARDS (Rincian Saldo) -->
    <div class="row mb-4">
        <!-- 1. Saldo Real (Fisik) -->
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Uang Fisik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_real, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 2. Kas PT -->
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kas PT (Available)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_kas_pt_now, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-building fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 3. Angsuran -->
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Dana Angsuran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_angsuran_now, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-piggy-bank fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 4. Royalti -->
        <div class="col-xl-3 col-md-6 mb-2">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dana Royalti</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_royalti_now, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-crown fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER PERIODE -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body py-3">
            <form action="" method="get" class="form-inline">
                <label class="mr-2 font-weight-bold">Filter:</label>
                <select name="bulan" class="form-control form-control-sm mr-2">
                    <option value="">Semua Bulan</option>
                    <?php
                    $bln = [
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
                    foreach ($bln as $k => $v) {
                        $sel = ($k == $filter_bulan) ? 'selected' : '';
                        echo "<option value='$k' $sel>$v</option>";
                    }
                    ?>
                </select>
                <select name="tahun" class="form-control form-control-sm mr-2">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= ($y == $filter_tahun) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Tampilkan</button>
            </form>
        </div>
    </div>

    <!-- TABEL TRANSAKSI (TAMPILAN WEB) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th>Keterangan</th>
                            <th>Kategori</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $total_masuk = 0;
                        $total_keluar = 0;

                        if (empty($kas)):
                        ?>
                            <tr>
                                <td colspan="7" class="text-center font-italic py-3">Tidak ada data transaksi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kas as $k):
                                if ($k->jenis == 'masuk') $total_masuk += $k->nominal;
                                else $total_keluar += $k->nominal;
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($k->tanggal)) ?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= $k->keterangan ?></div>
                                        <small class="text-muted">Ref: <?= $k->sumber_auto ?></small>
                                    </td>
                                    <td><span class="badge badge-light border"><?= $k->kategori ?></span></td>
                                    <td class="text-right text-success">
                                        <?= ($k->jenis == 'masuk') ? number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-right text-danger">
                                        <?= ($k->jenis == 'keluar') ? number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($k->sumber_auto == 'manual'): ?>
                                            <button class="btn btn-sm btn-info btn-edit py-0 px-2" data-id="<?= $k->id_kas ?>" data-toggle="modal" data-target="#modalTambahKas">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= base_url('bendahara/hapus_kas/' . $k->id_kas) ?>" class="btn btn-sm btn-danger py-0 px-2" onclick="return confirm('Hapus?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary py-0 px-2" disabled><i class="fas fa-lock"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="4" class="text-right">Total Periode Ini</td>
                            <td class="text-right text-success">Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
                            <td class="text-right text-danger">Rp <?= number_format($total_keluar, 0, ',', '.') ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kas -->
<div class="modal fade" id="modalTambahKas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('bendahara/tambah_kas') ?>" method="post">
                <input type="hidden" name="id_kas" id="id_kas">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Catat Transaksi Manual</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="jenis" id="jenis" class="form-control" required>
                            <option value="masuk">Pemasukan (Uang Masuk)</option>
                            <option value="keluar">Pengeluaran (Uang Keluar)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" id="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Lain-lain">Lain-lain</option>
                            <!-- [UPDATE] Opsi khusus untuk mengurangi saldo Angsuran & Royalti -->
                            <option value="Pembayaran Angsuran" class="font-weight-bold text-danger">Pembayaran Angsuran</option>
                            <option value="Pembayaran Royalti" class="font-weight-bold text-danger">Pembayaran Royalti</option>

                            <option disabled>------------------------</option>
                            <?php foreach ($kategori_ops as $kat): ?>
                                <option value="<?= $kat->nama_kategori ?>"><?= $kat->nama_kategori ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih "Pembayaran Angsuran/Royalti" jika ingin mengurangi saldo dana tersebut.</small>
                    </div>
                    <div class="form-group">
                        <label>Nominal (Rp)</label>
                        <input type="number" name="nominal" id="nominal" class="form-control" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Edit Modal Logic
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $('#modalTitle').text('Edit Transaksi Manual');
        $.ajax({
            url: '<?= base_url('bendahara/get_kas_ajax') ?>',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $('#id_kas').val(data.id_kas);
                $('#tanggal').val(data.tanggal);
                $('#jenis').val(data.jenis);
                $('#kategori').val(data.kategori);
                $('#nominal').val(data.nominal);
                $('#keterangan').val(data.keterangan);
            }
        });
    });

    $('#modalTambahKas').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Catat Transaksi Manual');
        $('form')[0].reset();
        $('#id_kas').val('');
    });
</script>