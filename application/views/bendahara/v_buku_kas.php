<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Buku Kas Umum</h1>
        <button class="btn btn-sm btn-danger shadow-sm" onclick="openModalTambah()" style="background-color: #C60000; border-color: #C60000;">
            <i class="fas fa-plus mr-1"></i> Catat Transaksi
        </button>
    </div>

    <!-- INFO SALDO -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Saldo Kas (Realtime)</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <form method="get" action="" class="form-inline w-100">
                        <label class="mr-2 font-weight-bold">Filter:</label>
                        <select name="bulan" class="form-control form-control-sm mr-2">
                            <option value="">-- Semua Bulan --</option>
                            <?php
                            $bln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
                            foreach ($bln as $k => $v) {
                                $sel = ($filter_bulan == $k) ? 'selected' : '';
                                echo "<option value='$k' $sel>$v</option>";
                            }
                            ?>
                        </select>
                        <select name="tahun" class="form-control form-control-sm mr-2">
                            <?php for ($y = date('Y'); $y >= 2023; $y--) {
                                $sel = ($filter_tahun == $y) ? 'selected' : '';
                                echo "<option value='$y' $sel>$y</option>";
                            } ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL KAS -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Transaksi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white text-center">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori / Uraian</th>
                            <th>Keterangan Detail</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kas)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kas as $k): ?>
                                <tr>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($k->tanggal)) ?></td>
                                    <td>
                                        <?php if ($k->jenis == 'masuk'): ?>
                                            <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Masuk</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Keluar</span>
                                        <?php endif; ?>
                                        <br><b><?= $k->kategori ?></b>
                                    </td>
                                    <td>
                                        <?= $k->keterangan ?>
                                        <?php if ($k->sumber_auto != 'manual'): ?>
                                            <br><small class="text-info font-italic"><i class="fas fa-robot"></i> Auto by System (<?= ucfirst($k->sumber_auto) ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        <?= ($k->jenis == 'masuk') ? 'Rp ' . number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-right text-danger font-weight-bold">
                                        <?= ($k->jenis == 'keluar') ? 'Rp ' . number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($k->sumber_auto == 'manual'): ?>
                                            <!-- Tombol Edit -->
                                            <button onclick="editKas(<?= $k->id_kas ?>)" class="btn btn-sm btn-warning mb-1" title="Edit"><i class="fas fa-edit"></i></button>
                                            <!-- Tombol Hapus -->
                                            <a href="<?= base_url('bendahara/hapus_kas/' . $k->id_kas) ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Hapus transaksi manual ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                        <?php else: ?>
                                            <span class="text-muted" title="Transaksi otomatis tidak bisa diedit"><i class="fas fa-lock"></i></span>
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

<!-- Modal Input Manual -->
<div class="modal fade" id="modalInput" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('bendahara/tambah_kas') ?>" method="post" id="formKas">
                <!-- ID KAS (Untuk Edit, kosong jika tambah baru) -->
                <input type="hidden" name="id_kas" id="id_kas">

                <!-- Header Tema Brand Primary (Merah) -->
                <div class="modal-header text-white" style="background-color: #C60000;">
                    <h5 class="modal-title" style="font-family: 'Oswald', sans-serif;" id="modalTitle">Input Transaksi Kas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Transaksi</label>
                        <select name="jenis" id="jenis" class="form-control" required>
                            <option value="keluar">Pengeluaran (Uang Keluar)</option>
                            <option value="masuk">Pemasukan (Uang Masuk)</option>
                        </select>
                    </div>

                    <!-- Kategori Dropdown dengan Opsi Default -->
                    <div class="form-group">
                        <label class="font-weight-bold">Kategori</label>
                        <select name="kategori" id="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <optgroup label="Pengeluaran Wajib">
                                <option value="Pembayaran Angsuran">Pembayaran Angsuran</option>
                                <option value="Pembayaran Royalti">Pembayaran Royalti</option>
                            </optgroup>
                            <optgroup label="Operasional Lainnya">
                                <?php foreach ($kategori_ops as $k): ?>
                                    <option value="<?= $k->nama_kategori ?>"><?= $k->nama_kategori ?></option>
                                <?php endforeach; ?>
                                <option value="Lainnya">Lainnya (Manual Input di Keterangan)</option>
                            </optgroup>
                        </select>
                        <small class="text-muted">Pilih "Pembayaran Angsuran" atau "Royalti" jika ingin mengurangi saldo pos tersebut.</small>
                    </div>

                    <!-- Input Nominal Auto Format -->
                    <div class="form-group">
                        <label class="font-weight-bold">Nominal (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" id="nominal_display" class="form-control rupiah-input font-weight-bold" placeholder="0" required style="font-size: 1.2rem;">
                            <input type="hidden" name="nominal" id="nominal_db">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Lengkap</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Detail transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" style="background-color: #C60000; border-color: #C60000;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Fungsi Format Rupiah
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    // Reset modal untuk input baru
    function openModalTambah() {
        $('#formKas')[0].reset();
        $('#id_kas').val('');
        $('#modalTitle').text('Input Transaksi Kas');
        $('#nominal_display').val('');
        $('#nominal_db').val('');
        $('#modalInput').modal('show');
    }

    // Isi modal untuk edit data
    function editKas(id) {
        $.ajax({
            url: '<?= base_url("bendahara/get_kas_ajax") ?>',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                $('#id_kas').val(data.id_kas);
                $('#tanggal').val(data.tanggal);
                $('#jenis').val(data.jenis);

                // Set Kategori (Handle jika kategori tidak ada di opsi)
                if ($("#kategori option[value='" + data.kategori + "']").length > 0) {
                    $('#kategori').val(data.kategori);
                } else {
                    $('#kategori').val('Lainnya');
                    // Tambahkan nama kategori asli ke keterangan jika belum ada
                    if (!data.keterangan.includes(data.kategori)) {
                        $('#keterangan').val('[' + data.kategori + '] ' + data.keterangan);
                    } else {
                        $('#keterangan').val(data.keterangan);
                    }
                }

                $('#nominal_db').val(data.nominal);
                $('#nominal_display').val(formatRupiah(data.nominal));
                $('#keterangan').val(data.keterangan);

                $('#modalTitle').text('Edit Transaksi Kas');
                $('#modalInput').modal('show');
            }
        });
    }

    $(document).ready(function() {
        // Format saat mengetik
        $('.rupiah-input').on('keyup', function() {
            $(this).val(formatRupiah($(this).val()));
            let cleanVal = $(this).val().replace(/\./g, '');
            $('#nominal_db').val(cleanVal);
        });

        // Validasi sebelum submit
        $('#formKas').on('submit', function() {
            let cleanVal = $('#nominal_display').val().replace(/\./g, '');
            $('#nominal_db').val(cleanVal);
            return true;
        });
    });
</script>