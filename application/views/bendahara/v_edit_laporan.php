<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Laporan Keuangan</h1>
        <a href="<?= base_url('bendahara/laporan') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <form action="<?= base_url('bendahara/proses_update') ?>" method="post" id="formLaporan">
        <input type="hidden" name="id_keuangan" value="<?= $header->id_keuangan ?>">
        <input type="hidden" name="id_peminjaman" value="<?= $header->id_peminjaman ?>">

        <!-- CARD 1: INFORMASI EVENT (READ ONLY) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning text-white">
                <h6 class="m-0 font-weight-bold">1. Informasi Event (Tidak Dapat Diubah)</h6>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="30%">Nama Event</td>
                                <td>: <b><?= $header->nama_event ?></b></td>
                            </tr>
                            <tr>
                                <td>Lokasi</td>
                                <td>: <?= $header->lokasi_event ?></td>
                            </tr>
                            <tr>
                                <td>Kode Transaksi</td>
                                <td>: <?= $header->kode_transaksi ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="30%">Tgl Pinjam</td>
                                <td>: <?= date('d M Y', strtotime($header->tgl_pinjam)) ?></td>
                            </tr>
                            <tr>
                                <td>Tgl Kembali</td>
                                <td>: <?= date('d M Y', strtotime($header->tgl_kembali_realisasi)) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: PEMASUKAN -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">2. Rincian Pemasukan</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Jenis Pemasukan</label>
                    <select name="jenis_pemasukan" id="jenis_pemasukan" class="form-control" required>
                        <option value="global" <?= $header->jenis_pemasukan == 'global' ? 'selected' : '' ?>>Global (Langsung Total)</option>
                        <option value="detail" <?= $header->jenis_pemasukan == 'detail' ? 'selected' : '' ?>>Detail (Hitung per Set/Gelanggang)</option>
                    </select>
                </div>

                <!-- Input Detail -->
                <div id="input_detail" style="display: <?= $header->jenis_pemasukan == 'detail' ? 'block' : 'none' ?>;">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label>Harga per Set (Rp)</label>
                            <input type="number" id="harga_set" name="harga_set" class="form-control hitung-income" value="<?= $header->harga_set ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Jml Gelanggang</label>
                            <input type="number" id="jml_gelanggang" name="jml_gelanggang" class="form-control hitung-income" value="<?= $header->jml_gelanggang ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Jml Hari</label>
                            <input type="number" id="jml_hari" name="jml_hari" class="form-control hitung-income" value="<?= $header->jml_hari ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-success">Total Pemasukan (Rp)</label>
                    <input type="number" id="total_pemasukan" name="total_pemasukan_final" class="form-control font-weight-bold text-success" style="font-size: 1.5rem;" value="<?= $header->total_pemasukan ?>" <?= $header->jenis_pemasukan == 'detail' ? 'readonly' : '' ?>>
                </div>
            </div>
        </div>

        <!-- CARD 3: BIAYA SDM -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">3. Biaya SDM / Personil</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm" id="tabel_sdm">
                    <thead class="bg-dark text-white text-center">
                        <tr>
                            <th width="20%">Nama Personil</th>
                            <th width="15%">Honor/Hari</th>
                            <th width="5%">Hari</th>
                            <th width="30%">Tambahan (Set/Trans/Bonus/Data)</th>
                            <th width="15%" class="bg-danger">Pot. Kasbon</th>
                            <th width="15%">Total Terima</th>
                        </tr>
                    </thead>
                    <tbody id="body_sdm">
                        <?php foreach ($detail_sdm as $i => $s): ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="sdm_id_user[]" value="<?= $s->id_user ?>">
                                    <input type="hidden" name="sdm_peran[]" value="<?= $s->peran ?>">
                                    <b><?= $s->nama_lengkap ?></b><br>
                                    <small class="text-muted"><?= $s->peran ?></small><br>
                                    <small class="text-danger font-italic">Hutang saat ini: Rp <?= number_format($s->current_hutang, 0, ',', '.') ?></small>
                                </td>
                                <td><input type="number" name="sdm_honor[]" class="form-control form-control-sm hitung-sdm" value="<?= $s->honor_harian ?>"></td>
                                <td><input type="number" name="sdm_hari[]" class="form-control form-control-sm hitung-sdm" value="<?= $s->jumlah_hari ?>"></td>
                                <td>
                                    <div class="row no-gutters mb-1">
                                        <div class="col"><input type="number" name="sdm_setting[]" class="form-control form-control-sm hitung-sdm" placeholder="Setting" value="<?= $s->nominal_setting ?>"></div>
                                    </div>
                                    <div class="row no-gutters mb-1">
                                        <div class="col"><input type="number" name="sdm_transport[]" class="form-control form-control-sm hitung-sdm" placeholder="Transport" value="<?= $s->nominal_transport ?>"></div>
                                    </div>
                                    <div class="row no-gutters mb-1">
                                        <div class="col"><input type="number" name="sdm_bonus[]" class="form-control form-control-sm hitung-sdm" placeholder="Bonus" value="<?= $s->nominal_bonus ?>"></div>
                                    </div>
                                    <div class="row no-gutters">
                                        <div class="col"><input type="number" name="sdm_data[]" class="form-control form-control-sm hitung-sdm" placeholder="Data" value="<?= $s->nominal_data ?>"></div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" name="sdm_potongan[]" class="form-control form-control-sm hitung-sdm border-danger text-danger" value="<?= $s->nominal_potongan ?>">
                                    <small class="text-muted d-block text-center mt-1">Maks: <?= number_format($s->current_hutang + $s->nominal_potongan, 0, ',', '.') ?></small>
                                </td>
                                <td><input type="number" name="sdm_total[]" class="form-control form-control-sm font-weight-bold text-right" readonly value="<?= $s->total_diterima ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="5" class="text-right">Total Biaya SDM:</td>
                            <td><input type="text" id="total_biaya_sdm_disp" class="form-control form-control-sm text-right font-weight-bold" readonly value="Rp <?= number_format($header->total_biaya_sdm, 0, ',', '.') ?>">
                                <input type="hidden" name="total_biaya_sdm" id="total_biaya_sdm_val" value="<?= $header->total_biaya_sdm ?>">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- CARD 4: OPERASIONAL -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">4. Biaya Operasional</h6>
                <button type="button" class="btn btn-sm btn-success" onclick="addOpsRow()"><i class="fas fa-plus"></i> Tambah Item</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th width="20%">Nominal (Rp)</th>
                            <th width="5%">#</th>
                        </tr>
                    </thead>
                    <tbody id="body_ops">
                        <?php foreach ($detail_ops as $o): ?>
                            <tr>
                                <td>
                                    <select name="ops_kategori[]" class="form-control form-control-sm">
                                        <?php foreach ($kategori_ops as $k): ?>
                                            <option value="<?= $k->id_kategori ?>" <?= $o->id_kategori == $k->id_kategori ? 'selected' : '' ?>><?= $k->nama_kategori ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="ops_keterangan[]" class="form-control form-control-sm" value="<?= $o->keterangan ?>"></td>
                                <td><input type="number" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right" value="<?= $o->nominal ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right font-weight-bold">Total Operasional:</td>
                            <td>
                                <input type="text" id="total_biaya_ops_disp" class="form-control form-control-sm text-right font-weight-bold" readonly value="Rp <?= number_format($header->total_biaya_ops, 0, ',', '.') ?>">
                                <input type="hidden" name="total_biaya_ops" id="total_biaya_ops_val" value="<?= $header->total_biaya_ops ?>">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- CARD 5: REKAPITULASI & BAGI HASIL -->
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5. Rekapitulasi & Bagi Hasil</h6>
            </div>
            <div class="card-body">
                <!-- Fee Marketing -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fee Marketing Internal</label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_intern_check" name="fee_intern_check" class="form-control" <?= $header->fee_intern_active ? 'checked' : '' ?>></div>
                    <div class="col-sm-8"><input type="number" id="fee_intern_val" name="fee_intern_val" class="form-control hitung-laba" placeholder="Nominal" value="<?= $header->fee_intern_nominal ?>" <?= $header->fee_intern_active ? '' : 'disabled' ?>></div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fee Marketing Eksternal</label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_ekstern_check" name="fee_ekstern_check" class="form-control" <?= $header->fee_ekstern_active ? 'checked' : '' ?>></div>
                    <div class="col-sm-8"><input type="number" id="fee_ekstern_val" name="fee_ekstern_val" class="form-control hitung-laba" placeholder="Nominal" value="<?= $header->fee_ekstern_nominal ?>" <?= $header->fee_ekstern_active ? '' : 'disabled' ?>></div>
                </div>
                <hr>

                <!-- Laba Bersih -->
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label font-weight-bold text-uppercase">Laba Bersih (Net Profit)</label>
                    <div class="col-sm-8">
                        <input type="text" id="laba_bersih_disp" class="form-control font-weight-bold text-primary" style="font-size: 1.2rem;" readonly value="Rp <?= number_format($header->laba_kotor, 0, ',', '.') ?>">
                        <input type="hidden" id="laba_bersih_val" name="laba_bersih_val" value="<?= $header->laba_kotor ?>">
                    </div>
                </div>

                <!-- Pembagian Hasil -->
                <div class="alert alert-secondary">
                    <h6 class="font-weight-bold">Alokasi Pembagian Hasil (33.3% Rules)</h6>
                    <div class="form-row">
                        <div class="col">
                            <label>Kas PT</label>
                            <input type="number" id="share_kas" name="share_kas_val" class="form-control font-weight-bold" readonly value="<?= $header->kas_pt_nominal ?>">
                        </div>
                        <div class="col">
                            <label>Angsuran</label>
                            <input type="number" id="share_angsuran" name="share_angsuran_val" class="form-control font-weight-bold" readonly value="<?= $header->angsuran_nominal ?>">
                        </div>
                        <div class="col">
                            <label>Royalti</label>
                            <input type="number" id="share_royalti" name="share_royalti_val" class="form-control font-weight-bold" readonly value="<?= $header->royalti_nominal ?>">
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TEMPLATE ROW OPS (HIDDEN) -->
<table style="display: none;">
    <tr id="template_ops_row">
        <td>
            <select name="ops_kategori[]" class="form-control form-control-sm">
                <?php foreach ($kategori_ops as $k): ?>
                    <option value="<?= $k->id_kategori ?>"><?= $k->nama_kategori ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="text" name="ops_keterangan[]" class="form-control form-control-sm" placeholder="Detail item..."></td>
        <td><input type="number" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right" value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
    </tr>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        // --- LOGIKA HITUNG PEMASUKAN ---
        $('#jenis_pemasukan').change(function() {
            if ($(this).val() == 'detail') {
                $('#input_detail').show();
                $('#total_pemasukan').prop('readonly', true);
                calculateIncomeDetail();
            } else {
                $('#input_detail').hide();
                $('#total_pemasukan').prop('readonly', false).val(0);
            }
            calculateAll();
        });

        $('.hitung-income').on('input', function() {
            calculateIncomeDetail();
            calculateAll();
        });

        function calculateIncomeDetail() {
            let set = parseFloat($('#harga_set').val()) || 0;
            let glg = parseFloat($('#jml_gelanggang').val()) || 0;
            let hari = parseFloat($('#jml_hari').val()) || 0;
            $('#total_pemasukan').val(set * glg * hari);
        }

        // --- LOGIKA HITUNG SDM ---
        $(document).on('input', '.hitung-sdm', function() {
            let row = $(this).closest('tr');
            let honor = parseFloat(row.find('input[name="sdm_honor[]"]').val()) || 0;
            let hari = parseFloat(row.find('input[name="sdm_hari[]"]').val()) || 0;
            let set = parseFloat(row.find('input[name="sdm_setting[]"]').val()) || 0;
            let trans = parseFloat(row.find('input[name="sdm_transport[]"]').val()) || 0;
            let bonus = parseFloat(row.find('input[name="sdm_bonus[]"]').val()) || 0;
            let data = parseFloat(row.find('input[name="sdm_data[]"]').val()) || 0;
            let pot = parseFloat(row.find('input[name="sdm_potongan[]"]').val()) || 0;

            let total = (honor * hari) + set + trans + bonus + data - pot;
            row.find('input[name="sdm_total[]"]').val(total);

            calculateTotalSDM();
            calculateAll();
        });

        function calculateTotalSDM() {
            let total = 0;
            $('input[name="sdm_total[]"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_biaya_sdm_disp').val('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            $('#total_biaya_sdm_val').val(total);
        }

        // --- LOGIKA HITUNG OPS ---
        window.addOpsRow = function() {
            let row = $('#template_ops_row').clone().removeAttr('id');
            $('#body_ops').append(row);
        }

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            calculateTotalOps();
            calculateAll();
        });

        $(document).on('input', '.hitung-ops', function() {
            calculateTotalOps();
            calculateAll();
        });

        function calculateTotalOps() {
            let total = 0;
            $('input[name="ops_nominal[]"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_biaya_ops_disp').val('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            $('#total_biaya_ops_val').val(total);
        }

        // --- LOGIKA HITUNG LABA & BAGI HASIL ---
        $('#fee_intern_check').change(function() {
            $('#fee_intern_val').prop('disabled', !this.checked).val(this.checked ? '' : 0);
            calculateAll();
        });
        $('#fee_ekstern_check').change(function() {
            $('#fee_ekstern_val').prop('disabled', !this.checked).val(this.checked ? '' : 0);
            calculateAll();
        });
        $('.hitung-laba').on('input', function() {
            calculateAll();
        });
        $('#total_pemasukan').on('input', function() {
            calculateAll();
        });

        function calculateAll() {
            let pemasukan = parseFloat($('#total_pemasukan').val()) || 0;
            let sdm = parseFloat($('#total_biaya_sdm_val').val()) || 0;
            let ops = parseFloat($('#total_biaya_ops_val').val()) || 0;
            let fee_in = parseFloat($('#fee_intern_val').val()) || 0;
            let fee_out = parseFloat($('#fee_ekstern_val').val()) || 0;

            let laba = pemasukan - sdm - ops - fee_in - fee_out;

            $('#laba_bersih_disp').val('Rp ' + new Intl.NumberFormat('id-ID').format(laba));
            $('#laba_bersih_val').val(laba);

            // Bagi Hasil (Bulatkan ke bawah agar aman)
            if (laba > 0) {
                let share = Math.floor(laba / 3);
                $('#share_kas').val(share);
                $('#share_angsuran').val(share);
                $('#share_royalti').val(laba - (share * 2)); // Sisa masuk royalti
            } else {
                $('#share_kas').val(0);
                $('#share_angsuran').val(0);
                $('#share_royalti').val(0);
            }
        }
    });
</script>