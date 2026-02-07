<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Laporan Keuangan</h1>
        <a href="<?= base_url('bendahara/laporan') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <form action="<?= base_url('bendahara/proses_update') ?>" method="post" id="formLaporan">
        <input type="hidden" name="id_keuangan" value="<?= $header->id_keuangan ?>">
        <input type="hidden" name="id_peminjaman" value="<?= $header->id_peminjaman ?>">

        <!-- INFO EVENT -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning text-white">
                <h6 class="m-0 font-weight-bold">1. Informasi Event (Read Only)</h6>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Event:</strong> <?= $header->nama_event ?><br>
                        <strong>Kode:</strong> <?= $header->kode_transaksi ?>
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Tanggal:</strong> <?= date('d M Y', strtotime($header->tgl_pinjam)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 1: SUMBER PEMASUKAN -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">2. Sumber Pemasukan</h6>
            </div>
            <div class="card-body">

                <!-- A. PEMASUKAN IT -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_it" name="check_it" <?= $header->income_it_active ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold text-primary" for="check_it">A. Pemasukan IT</label>
                    </div>
                    <div id="box_it" style="display: <?= $header->income_it_active ? 'block' : 'none' ?>;">
                        <div class="form-group">
                            <label>Metode</label>
                            <select name="jenis_pemasukan" id="jenis_pemasukan" class="form-control form-control-sm">
                                <option value="detail" <?= $header->jenis_pemasukan == 'detail' ? 'selected' : '' ?>>Detail</option>
                                <option value="global" <?= $header->jenis_pemasukan == 'global' ? 'selected' : '' ?>>Global / Borongan</option>
                            </select>
                        </div>
                        <div id="input_detail_it" class="form-row" style="display: <?= $header->jenis_pemasukan == 'detail' ? 'flex' : 'none' ?>;">
                            <div class="col-md-4"><label>Harga (Rp)</label><input type="text" id="harga_set" name="harga_set" class="form-control hitung-income rupiah-input" value="<?= number_format($header->harga_set, 0, ',', '.') ?>"></div>
                            <div class="col-md-4"><label>Jml Gel</label><input type="number" id="jml_gelanggang" name="jml_gelanggang" class="form-control hitung-income" value="<?= $header->jml_gelanggang ?>"></div>
                            <div class="col-md-4"><label>Hari</label><input type="number" id="jml_hari" name="jml_hari" class="form-control hitung-income" value="<?= $header->jml_hari ?>"></div>
                        </div>
                        <div class="mt-2">
                            <label class="small font-weight-bold">Subtotal IT</label>
                            <?php
                            // Logic: Pastikan nilai awal dihitung dengan benar agar tidak anjlok
                            if ($header->jenis_pemasukan == 'detail') {
                                $sub_it_awal = (float)$header->harga_set * (float)$header->jml_gelanggang * (float)$header->jml_hari;
                            } else {
                                // Jika global, ambil sisa dari total dikurangi komponen lain
                                $sub_it_awal = (float)$header->total_pemasukan - (float)$header->log_total - (float)$header->lain_nominal;
                            }
                            ?>
                            <!-- Class 'hitung-income' PENTING agar bisa diedit manual dan langsung update total -->
                            <input type="text" id="subtotal_it" name="subtotal_it" class="form-control font-weight-bold text-primary rupiah-input hitung-income" <?= $header->jenis_pemasukan == 'detail' ? 'readonly' : '' ?> value="<?= number_format($sub_it_awal, 0, ',', '.') ?>">
                        </div>
                    </div>
                </div>

                <!-- B. LOGISTIK -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_log" name="check_log" <?= $header->income_log_active ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold text-info" for="check_log">B. Pemasukan Logistik</label>
                    </div>
                    <div id="box_log" style="display: <?= $header->income_log_active ? 'block' : 'none' ?>;">
                        <div class="form-group">
                            <label>Metode</label>
                            <?php $is_log_detail = ($header->log_harga > 0 || $header->log_qty > 0); ?>
                            <select name="jenis_logistik" id="jenis_logistik" class="form-control form-control-sm">
                                <option value="detail" <?= $is_log_detail ? 'selected' : '' ?>>Detail</option>
                                <option value="global" <?= !$is_log_detail ? 'selected' : '' ?>>Global / Borongan</option>
                            </select>
                        </div>
                        <div id="input_detail_log" class="form-row" style="display: <?= $is_log_detail ? 'flex' : 'none' ?>;">
                            <div class="col-md-4"><label>Harga (Rp)</label><input type="text" id="log_harga" name="log_harga" class="form-control hitung-income rupiah-input" value="<?= number_format($header->log_harga, 0, ',', '.') ?>"></div>
                            <div class="col-md-4"><label>Qty</label><input type="number" id="log_qty" name="log_qty" class="form-control hitung-income" value="<?= $header->log_qty ?>"></div>
                            <div class="col-md-4"><label>Hari</label><input type="number" id="log_hari" name="log_hari" class="form-control hitung-income" value="<?= $header->log_hari ?>"></div>
                        </div>
                        <div class="mt-2">
                            <label class="small font-weight-bold">Subtotal Logistik</label>
                            <!-- Class 'hitung-income' PENTING -->
                            <input type="text" id="subtotal_log" name="subtotal_log" class="form-control font-weight-bold text-info rupiah-input hitung-income" <?= $is_log_detail ? 'readonly' : '' ?> value="<?= number_format($header->log_total, 0, ',', '.') ?>">
                        </div>
                    </div>
                </div>

                <!-- C. LAINNYA -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_lain" name="check_lain" <?= $header->income_lain_active ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold text-secondary" for="check_lain">C. Pemasukan Lainnya</label>
                    </div>
                    <div id="box_lain" style="display: <?= $header->income_lain_active ? 'block' : 'none' ?>;">
                        <div class="form-group"><input type="text" name="lain_keterangan" class="form-control" placeholder="Keterangan..." value="<?= $header->lain_keterangan ?>"></div>
                        <div class="form-group"><input type="text" id="lain_nominal" name="lain_nominal" class="form-control hitung-income rupiah-input" value="<?= number_format($header->lain_nominal, 0, ',', '.') ?>"></div>
                    </div>
                </div>

                <div class="p-3 mb-3 bg-success text-white rounded shadow-sm">
                    <h5 class="font-weight-bold mb-0">Total: <span id="text_total_pemasukan">Rp <?= number_format($header->total_pemasukan, 0, ',', '.') ?></span></h5>
                    <input type="hidden" id="total_pemasukan_final" name="total_pemasukan_final" value="<?= $header->total_pemasukan ?>">
                </div>
            </div>
        </div>

        <!-- CARD SDM -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Biaya SDM</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-dark text-white text-center">
                            <tr>
                                <th>Nama</th>
                                <th>Honor</th>
                                <th>Hari</th>
                                <th>Tambahan</th>
                                <th>Potongan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_sdm as $s): ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="sdm_id_user[]" value="<?= $s->id_user ?>">
                                        <input type="hidden" name="sdm_peran[]" value="<?= $s->peran ?>">
                                        <b><?= $s->nama_lengkap ?></b><br><small><?= $s->peran ?></small><br>
                                        <small class="text-danger">Hutang Aktif: Rp <?= number_format($s->current_hutang, 0, ',', '.') ?></small>
                                    </td>
                                    <td><input type="text" name="sdm_honor[]" class="form-control form-control-sm hitung-sdm rupiah-input" value="<?= number_format($s->honor_harian, 0, ',', '.') ?>"></td>
                                    <td><input type="number" name="sdm_hari[]" class="form-control form-control-sm hitung-sdm" value="<?= $s->jumlah_hari ?>"></td>
                                    <td>
                                        <input type="text" name="sdm_setting[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Set" value="<?= number_format($s->nominal_setting, 0, ',', '.') ?>">
                                        <input type="text" name="sdm_transport[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Trsp" value="<?= number_format($s->nominal_transport, 0, ',', '.') ?>">
                                        <input type="text" name="sdm_bonus[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Bns" value="<?= number_format($s->nominal_bonus, 0, ',', '.') ?>">
                                        <input type="text" name="sdm_data[]" class="form-control form-control-sm hitung-sdm rupiah-input" placeholder="Data" value="<?= number_format($s->nominal_data, 0, ',', '.') ?>">
                                    </td>
                                    <td><input type="text" name="sdm_potongan[]" class="form-control form-control-sm hitung-sdm text-danger rupiah-input" value="<?= number_format($s->nominal_potongan, 0, ',', '.') ?>"></td>
                                    <td><input type="text" name="sdm_total[]" class="form-control form-control-sm font-weight-bold text-right rupiah-input" readonly value="<?= number_format($s->total_diterima, 0, ',', '.') ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right">Total:</td>
                                <td><input type="text" id="total_biaya_sdm_disp" class="form-control form-control-sm text-right font-weight-bold rupiah-input" readonly value="<?= number_format($header->total_biaya_sdm, 0, ',', '.') ?>"><input type="hidden" name="total_biaya_sdm" id="total_biaya_sdm_val" value="<?= $header->total_biaya_sdm ?>"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- CARD OPS -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Biaya Operasional</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm">
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
                                <td><input type="text" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right rupiah-input" value="<?= number_format($o->nominal, 0, ',', '.') ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">Total:</td>
                            <td><input type="text" id="total_biaya_ops_disp" class="form-control form-control-sm text-right font-weight-bold rupiah-input" readonly value="<?= number_format($header->total_biaya_ops, 0, ',', '.') ?>"><input type="hidden" name="total_biaya_ops" id="total_biaya_ops_val" value="<?= $header->total_biaya_ops ?>"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-success mt-2" onclick="addOpsRow()">+ Item</button>
            </div>
        </div>

        <!-- REKAPITULASI -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-3">Fee Intern (5%)</label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_intern_check" name="fee_intern_check" class="form-control hitung-laba" <?= $header->fee_intern_active ? 'checked' : '' ?>></div>
                    <div class="col-sm-8"><input type="text" id="fee_intern_val" name="fee_intern_val" class="form-control hitung-laba rupiah-input" readonly value="<?= number_format($header->fee_intern_nominal, 0, ',', '.') ?>"></div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">Fee Ekstern (5%)</label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_ekstern_check" name="fee_ekstern_check" class="form-control hitung-laba" <?= $header->fee_ekstern_active ? 'checked' : '' ?>></div>
                    <div class="col-sm-8"><input type="text" id="fee_ekstern_val" name="fee_ekstern_val" class="form-control hitung-laba rupiah-input" readonly value="<?= number_format($header->fee_ekstern_nominal, 0, ',', '.') ?>"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 font-weight-bold">Laba Bersih</label>
                    <div class="col-sm-8">
                        <input type="text" id="laba_bersih_disp" class="form-control font-weight-bold text-danger rupiah-input" style="font-size: 1.5rem;" readonly value="<?= number_format($header->laba_kotor, 0, ',', '.') ?>">
                        <input type="hidden" id="laba_bersih_val" name="laba_bersih_val" value="<?= $header->laba_kotor ?>">
                    </div>
                </div>

                <div class="p-3 mb-3 bg-light border rounded">
                    <div class="form-row mb-2">
                        <div class="col-4"><input type="checkbox" class="hitung-share-trigger" id="share_kas_check" name="share_kas_check" <?= $header->share_kas_active ? 'checked' : '' ?>> Kas PT</div>
                        <div class="col-8"><input type="text" id="share_kas_val" name="share_kas_val" class="form-control font-weight-bold rupiah-input" readonly value="<?= number_format($header->kas_pt_nominal, 0, ',', '.') ?>"></div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-4"><input type="checkbox" class="hitung-share-trigger" id="share_angsuran_check" name="share_angsuran_check" <?= $header->share_angsuran_active ? 'checked' : '' ?>> Angsuran</div>
                        <div class="col-8"><input type="text" id="share_angsuran_val" name="share_angsuran_val" class="form-control font-weight-bold rupiah-input" readonly value="<?= number_format($header->angsuran_nominal, 0, ',', '.') ?>"></div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-4"><input type="checkbox" class="hitung-share-trigger" id="share_royalti_check" name="share_royalti_check" <?= $header->share_royalti_active ? 'checked' : '' ?>> Royalti</div>
                        <div class="col-8"><input type="text" id="share_royalti_val" name="share_royalti_val" class="form-control font-weight-bold rupiah-input" readonly value="<?= number_format($header->royalti_nominal, 0, ',', '.') ?>"></div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-lg">Update Laporan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Ops -->
<table style="display: none;">
    <tr id="template_ops_row">
        <td><select name="ops_kategori[]" class="form-control form-control-sm"><?php foreach ($kategori_ops as $k): ?><option value="<?= $k->id_kategori ?>"><?= $k->nama_kategori ?></option><?php endforeach; ?></select></td>
        <td><input type="text" name="ops_keterangan[]" class="form-control form-control-sm"></td>
        <td><input type="text" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right rupiah-input" value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">x</button></td>
    </tr>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatRupiah(angka, prefix) {
        if (typeof angka !== 'string') angka = angka.toString();
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

    function cleanRupiah(str) {
        if (!str) return 0;
        return parseFloat(str.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    $(document).ready(function() {

        // 1. Initial Format on Load
        $('.rupiah-input').each(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        // 2. Trigger Perhitungan Awal (Sangat Penting untuk Edit)
        // Urutan: Income -> SDM -> Ops -> Fee & Profit
        calculateIncome();
        calculateTotalSDM();
        calculateOps();
        // Panggil ulang fees untuk memastikan sinkronisasi akhir
        calculateFees();

        // 3. Event Listeners Agresif (keyup, input, change)
        $(document).on('keyup input change', '.hitung-income', function() {
            calculateIncome();
        });

        $(document).on('keyup input change', '.hitung-sdm', function() {
            calculateSDMRow($(this));
        });

        $(document).on('keyup input change', '.hitung-ops', function() {
            calculateOps();
        });

        $(document).on('keyup input change', '.hitung-laba', function() {
            calculateFees();
        });

        // Untuk format rupiah saat ngetik (hanya keyup agar kursor nyaman)
        $(document).on('keyup', '.rupiah-input', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('.hitung-income-trigger').change(function() {
            $('#box_it').toggle($('#check_it').is(':checked'));
            $('#box_log').toggle($('#check_log').is(':checked'));
            $('#box_lain').toggle($('#check_lain').is(':checked'));
            calculateIncome();
        });

        // Toggle Jenis IT
        $('#jenis_pemasukan').change(function() {
            if ($(this).val() == 'detail') {
                $('#input_detail_it').css('display', 'flex');
                $('#subtotal_it').prop('readonly', true);
            } else {
                $('#input_detail_it').hide();
                $('#subtotal_it').prop('readonly', false).val(0);
            }
            calculateIncome();
        });

        // Toggle Jenis Logistik
        $('#jenis_logistik').change(function() {
            if ($(this).val() == 'detail') {
                $('#input_detail_log').css('display', 'flex');
                $('#subtotal_log').prop('readonly', true);
            } else {
                $('#input_detail_log').hide();
                $('#subtotal_log').prop('readonly', false).val(0);
            }
            calculateIncome();
        });

        function calculateIncome() {
            let valIT = 0;
            if ($('#check_it').is(':checked')) {
                if ($('#jenis_pemasukan').val() == 'detail') {
                    let h = cleanRupiah($('#harga_set').val());
                    let g = parseFloat($('#jml_gelanggang').val()) || 0;
                    let d = parseFloat($('#jml_hari').val()) || 0;
                    valIT = h * g * d;
                    $('#subtotal_it').val(formatRupiah(valIT));
                } else {
                    valIT = cleanRupiah($('#subtotal_it').val());
                }
            } else {
                // Jika tidak dicentang, anggap 0 tapi jangan ubah field input user
                valIT = 0;
            }

            let valLog = 0;
            if ($('#check_log').is(':checked')) {
                if ($('#jenis_logistik').val() == 'detail') {
                    let lh = cleanRupiah($('#log_harga').val());
                    let lq = parseFloat($('#log_qty').val()) || 0;
                    let ld = parseFloat($('#log_hari').val()) || 0;
                    valLog = lh * lq * ld;
                    $('#subtotal_log').val(formatRupiah(valLog));
                } else {
                    valLog = cleanRupiah($('#subtotal_log').val());
                }
            } else {
                valLog = 0;
            }

            let valLain = 0;
            if ($('#check_lain').is(':checked')) {
                valLain = cleanRupiah($('#lain_nominal').val());
            }

            let total = valIT + valLog + valLain;
            $('#text_total_pemasukan').text('Rp ' + formatRupiah(total));
            $('#total_pemasukan_final').val(total);

            calculateFees();
        }

        function calculateSDMRow(el) {
            let row = el.closest('tr');
            let honor = cleanRupiah(row.find('input[name="sdm_honor[]"]').val());
            let hari = parseFloat(row.find('input[name="sdm_hari[]"]').val()) || 0;
            let set = cleanRupiah(row.find('input[name="sdm_setting[]"]').val());
            let trans = cleanRupiah(row.find('input[name="sdm_transport[]"]').val());
            let bonus = cleanRupiah(row.find('input[name="sdm_bonus[]"]').val());
            let data = cleanRupiah(row.find('input[name="sdm_data[]"]').val());
            let pot = cleanRupiah(row.find('input[name="sdm_potongan[]"]').val());
            let total = (honor * hari) + set + trans + bonus + data - pot;
            row.find('input[name="sdm_total[]"]').val(formatRupiah(total));
            calculateTotalSDM();
        }

        function calculateTotalSDM() {
            let totalAll = 0;
            $('input[name="sdm_total[]"]').each(function() {
                totalAll += cleanRupiah($(this).val());
            });
            $('#total_biaya_sdm_disp').val(formatRupiah(totalAll));
            $('#total_biaya_sdm_val').val(totalAll);
            calculateNetProfit();
        }

        window.addOpsRow = function() {
            $('#body_ops').append($('#template_ops_row').clone().removeAttr('id'));
        }
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            calculateOps();
        });

        function calculateOps() {
            let total = 0;
            $('input[name="ops_nominal[]"]').each(function() {
                total += cleanRupiah($(this).val());
            });
            $('#total_biaya_ops_disp').val(formatRupiah(total));
            $('#total_biaya_ops_val').val(total);
            calculateNetProfit();
        }

        $('#fee_intern_check, #fee_ekstern_check').change(function() {
            calculateFees();
        });

        function calculateFees() {
            let totalIncome = parseFloat($('#total_pemasukan_final').val()) || 0;

            // Fee selalu dihitung ulang 5% dari total income saat ini
            if ($('#fee_intern_check').is(':checked')) {
                $('#fee_intern_val').val(formatRupiah(Math.floor(totalIncome * 0.05)));
            } else {
                $('#fee_intern_val').val(0);
            }
            if ($('#fee_ekstern_check').is(':checked')) {
                $('#fee_ekstern_val').val(formatRupiah(Math.floor(totalIncome * 0.05)));
            } else {
                $('#fee_ekstern_val').val(0);
            }
            calculateNetProfit();
        }

        $('.hitung-share-trigger').change(function() {
            calculateNetProfit();
        });

        function calculateNetProfit() {
            let pemasukan = parseFloat($('#total_pemasukan_final').val()) || 0;
            let sdm = parseFloat($('#total_biaya_sdm_val').val()) || 0;
            let ops = parseFloat($('#total_biaya_ops_val').val()) || 0;
            let fee_in = cleanRupiah($('#fee_intern_val').val());
            let fee_out = cleanRupiah($('#fee_ekstern_val').val());
            let laba = pemasukan - sdm - ops - fee_in - fee_out;

            $('#laba_bersih_disp').val(formatRupiah(laba));
            $('#laba_bersih_val').val(laba);

            let shareKas = $('#share_kas_check').is(':checked');
            let shareAng = $('#share_angsuran_check').is(':checked');
            let shareRoy = $('#share_royalti_check').is(':checked');
            let activeCount = (shareKas ? 1 : 0) + (shareAng ? 1 : 0) + (shareRoy ? 1 : 0);

            $('#share_kas_val').val(0);
            $('#share_angsuran_val').val(0);
            $('#share_royalti_val').val(0);

            if (laba > 0 && activeCount > 0) {
                let shareAmount = Math.floor(laba / activeCount);
                let sisa = laba - (shareAmount * activeCount);
                if (shareKas) $('#share_kas_val').val(formatRupiah(shareAmount));
                if (shareAng) $('#share_angsuran_val').val(formatRupiah(shareAmount));
                if (shareRoy) $('#share_royalti_val').val(formatRupiah(shareAmount + sisa));
            }
        }

        $('#formLaporan').submit(function() {
            $('.rupiah-input').each(function() {
                $(this).val(cleanRupiah($(this).val()));
            });
            return true;
        });
    });
</script>