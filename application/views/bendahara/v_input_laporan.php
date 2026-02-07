<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Input Laporan Keuangan Baru</h1>
        <a href="<?= base_url('bendahara/laporan') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <form action="<?= base_url('bendahara/proses_simpan') ?>" method="post" id="formLaporan">

        <!-- Pilihan Event (Select2) -->
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">Pilih Event yang Belum Dilaporkan:</label>
                    <select name="id_peminjaman" id="id_peminjaman" class="form-control" required>
                        <option value="">-- Pilih Event --</option>
                        <?php foreach ($events as $e): ?>
                            <option value="<?= $e->id_peminjaman ?>"><?= date('d/m/y', strtotime($e->tgl_pinjam)) ?> - <?= $e->nama_event ?> (<?= $e->kode_transaksi ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Info Event Preview -->
                <div id="event_info" class="p-3 mb-3 bg-info text-white rounded shadow-sm" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Lokasi:</strong> <span id="info_lokasi"></span><br>
                            <strong>Operator:</strong> <span id="info_operator"></span>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="badge badge-light" id="info_kode"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 1: SUMBER PEMASUKAN (DINAMIS) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-coins mr-2"></i>1. Sumber Pemasukan</h6>
            </div>
            <div class="card-body">

                <!-- A. PEMASUKAN IT -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_it" name="check_it" checked>
                        <label class="custom-control-label font-weight-bold text-primary" for="check_it">A. Pemasukan IT (Gelanggang)</label>
                    </div>

                    <div id="box_it">
                        <div class="form-group">
                            <label>Metode Hitung</label>
                            <select name="jenis_pemasukan" id="jenis_pemasukan" class="form-control form-control-sm">
                                <option value="detail">Detail (Harga x Gelanggang x Hari)</option>
                                <option value="global">Global / Borongan (Langsung Total)</option>
                            </select>
                        </div>
                        <!-- Input Detail IT -->
                        <div id="input_detail_it" class="form-row">
                            <div class="col-md-4 mb-2">
                                <label>Harga (Rp)</label>
                                <input type="text" id="harga_set" name="harga_set" class="form-control hitung-income rupiah-input" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Jml Gelanggang</label>
                                <input type="number" id="jml_gelanggang" name="jml_gelanggang" class="form-control hitung-income" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Jml Hari</label>
                                <input type="number" id="jml_hari" name="jml_hari" class="form-control hitung-income" placeholder="0">
                            </div>
                        </div>
                        <!-- Subtotal IT -->
                        <div class="form-group mt-2">
                            <label class="small font-weight-bold">Subtotal IT (Rp)</label>
                            <!-- Class 'hitung-income' wajib ada untuk trigger hitung manual -->
                            <input type="text" id="subtotal_it" name="subtotal_it" class="form-control font-weight-bold text-primary rupiah-input hitung-income" readonly value="0">
                        </div>
                    </div>
                </div>

                <!-- B. PEMASUKAN LOGISTIK -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_log" name="check_log">
                        <label class="custom-control-label font-weight-bold text-info" for="check_log">B. Pemasukan Logistik</label>
                    </div>

                    <div id="box_log" style="display: none;">
                        <div class="form-group">
                            <label>Metode Hitung</label>
                            <select name="jenis_logistik" id="jenis_logistik" class="form-control form-control-sm">
                                <option value="detail">Detail (Harga x Qty x Hari)</option>
                                <option value="global">Global / Borongan (Langsung Total)</option>
                            </select>
                        </div>

                        <div id="input_detail_log" class="form-row">
                            <div class="col-md-4 mb-2">
                                <label>Harga Sewa (Rp)</label>
                                <input type="text" id="log_harga" name="log_harga" class="form-control hitung-income rupiah-input" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Qty / Paket</label>
                                <input type="number" id="log_qty" name="log_qty" class="form-control hitung-income" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Jml Hari</label>
                                <input type="number" id="log_hari" name="log_hari" class="form-control hitung-income" placeholder="0">
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="small font-weight-bold">Subtotal Logistik (Rp)</label>
                            <input type="text" id="subtotal_log" name="subtotal_log" class="form-control font-weight-bold text-info rupiah-input hitung-income" readonly value="0">
                        </div>
                    </div>
                </div>

                <!-- C. PEMASUKAN LAINNYA -->
                <div class="form-group border rounded p-3 mb-3 bg-light">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input hitung-income-trigger" id="check_lain" name="check_lain">
                        <label class="custom-control-label font-weight-bold text-secondary" for="check_lain">C. Pemasukan Lainnya (Opsional)</label>
                    </div>

                    <div id="box_lain" style="display: none;">
                        <div class="form-group">
                            <label>Keterangan Sumber Dana</label>
                            <input type="text" name="lain_keterangan" class="form-control" placeholder="Contoh: Sponsor, Donasi, Penjualan Merchandise">
                        </div>
                        <div class="form-group">
                            <label>Nominal (Rp)</label>
                            <input type="text" id="lain_nominal" name="lain_nominal" class="form-control hitung-income rupiah-input" placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- TOTAL AKHIR PEMASUKAN -->
                <div class="p-3 mb-3 bg-success text-white rounded shadow-sm">
                    <h5 class="font-weight-bold mb-0">Total Seluruh Pemasukan: <span id="text_total_pemasukan">Rp 0</span></h5>
                    <input type="hidden" id="total_pemasukan_final" name="total_pemasukan_final" value="0">
                </div>
            </div>
        </div>

        <!-- CARD 2: BIAYA SDM (PERSONIL) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">2. Biaya SDM / Personil</h6>
            </div>
            <div class="card-body">
                <div class="p-2 mb-3 bg-warning text-dark rounded small">
                    <i class="fas fa-info-circle"></i> Personil akan muncul otomatis setelah memilih Event diatas.
                </div>
                <div class="table-responsive">
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
                            <!-- Diisi via AJAX -->
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">Total Biaya SDM:</td>
                                <td>
                                    <input type="text" id="total_biaya_sdm_disp" class="form-control form-control-sm text-right font-weight-bold rupiah-input" readonly value="0">
                                    <input type="hidden" name="total_biaya_sdm" id="total_biaya_sdm_val" value="0">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- CARD 3: BIAYA OPERASIONAL -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">3. Biaya Operasional</h6>
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
                        <tr>
                            <td>
                                <select name="ops_kategori[]" class="form-control form-control-sm">
                                    <?php foreach ($kategori_ops as $k): ?>
                                        <option value="<?= $k->id_kategori ?>"><?= $k->nama_kategori ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="ops_keterangan[]" class="form-control form-control-sm" placeholder="Detail item..."></td>
                            <td><input type="text" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right rupiah-input" value="0"></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right font-weight-bold">Total Operasional:</td>
                            <td>
                                <input type="text" id="total_biaya_ops_disp" class="form-control form-control-sm text-right font-weight-bold rupiah-input" readonly value="0">
                                <input type="hidden" name="total_biaya_ops" id="total_biaya_ops_val" value="0">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- CARD 4: REKAPITULASI & BAGI HASIL -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">4. Rekapitulasi & Bagi Hasil (Net Profit)</h6>
            </div>
            <div class="card-body">

                <!-- Fee Marketing -->
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fee Marketing Internal <span class="badge badge-info ml-1">5%</span></label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_intern_check" name="fee_intern_check" class="form-control hitung-laba"></div>
                    <div class="col-sm-8"><input type="text" id="fee_intern_val" name="fee_intern_val" class="form-control hitung-laba rupiah-input" placeholder="Otomatis 5%" readonly></div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Fee Marketing Eksternal <span class="badge badge-info ml-1">5%</span></label>
                    <div class="col-sm-1"><input type="checkbox" id="fee_ekstern_check" name="fee_ekstern_check" class="form-control hitung-laba"></div>
                    <div class="col-sm-8"><input type="text" id="fee_ekstern_val" name="fee_ekstern_val" class="form-control hitung-laba rupiah-input" placeholder="Otomatis 5%" readonly></div>
                </div>
                <hr>

                <!-- Laba Bersih -->
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label font-weight-bold text-uppercase">Laba Bersih (Net Profit)</label>
                    <div class="col-sm-8">
                        <input type="text" id="laba_bersih_disp" class="form-control font-weight-bold text-danger rupiah-input" style="font-size: 1.5rem;" readonly value="0">
                        <input type="hidden" id="laba_bersih_val" name="laba_bersih_val" value="0">
                    </div>
                </div>

                <!-- Pembagian Hasil -->
                <div class="p-3 mb-3 bg-light border rounded text-dark">
                    <h6 class="font-weight-bold">Alokasi Pembagian Hasil (Pilih yang Aktif)</h6>

                    <div class="form-row align-items-center mb-2">
                        <div class="col-md-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input hitung-share-trigger" id="share_kas_check" name="share_kas_check" checked>
                                <label class="custom-control-label font-weight-bold" for="share_kas_check">1. Kas PT</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="share_kas_val" name="share_kas_val" class="form-control font-weight-bold rupiah-input" readonly value="0">
                        </div>
                    </div>

                    <div class="form-row align-items-center mb-2">
                        <div class="col-md-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input hitung-share-trigger" id="share_angsuran_check" name="share_angsuran_check" checked>
                                <label class="custom-control-label font-weight-bold" for="share_angsuran_check">2. Angsuran</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="share_angsuran_val" name="share_angsuran_val" class="form-control font-weight-bold rupiah-input" readonly value="0">
                        </div>
                    </div>

                    <div class="form-row align-items-center mb-2">
                        <div class="col-md-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input hitung-share-trigger" id="share_royalti_check" name="share_royalti_check" checked>
                                <label class="custom-control-label font-weight-bold" for="share_royalti_check">3. Royalti</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="share_royalti_val" name="share_royalti_val" class="form-control font-weight-bold rupiah-input" readonly value="0">
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow"><i class="fas fa-save"></i> Simpan Laporan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template Row untuk Tambah Ops -->
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
        <td><input type="text" name="ops_nominal[]" class="form-control form-control-sm hitung-ops text-right rupiah-input" value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>
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

        // 1. EVENT FORMAT (Hanya Keyup agar kursor nyaman)
        $(document).on('keyup', '.rupiah-input', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        // 2. EVENT HITUNG (Lebih Agresif: keyup, input, change)
        // Menambahkan event 'input' memastikan kalkulasi berjalan saat copy-paste atau saat value berubah
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

        // AJAX Load Event
        $('#id_peminjaman').change(function() {
            let id = $(this).val();
            if (id) {
                $.ajax({
                    url: '<?= base_url("bendahara/get_event_details_ajax") ?>',
                    type: 'POST',
                    data: {
                        id_peminjaman: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'success') {
                            $('#event_info').show();
                            $('#info_lokasi').text(res.event.lokasi_event);
                            $('#info_kode').text(res.event.kode_transaksi);
                            let html = '';
                            if (res.personil.length > 0) {
                                res.personil.forEach(function(p) {
                                    $('#info_operator').text(p.nama);
                                    html += `<tr>
                                    <td>
                                        <input type="hidden" name="sdm_id_user[]" value="${p.id}">
                                        <input type="hidden" name="sdm_peran[]" value="${p.peran}">
                                        <b>${p.nama}</b><br><small class="text-muted">${p.peran}</small><br>
                                        <small class="text-danger font-italic">Hutang: Rp ${formatRupiah(p.hutang)}</small>
                                    </td>
                                    <td><input type="text" name="sdm_honor[]" class="form-control form-control-sm hitung-sdm rupiah-input" value="0"></td>
                                    <td><input type="number" name="sdm_hari[]" class="form-control form-control-sm hitung-sdm" value="1"></td>
                                    <td>
                                        <input type="text" name="sdm_setting[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Set" value="0">
                                        <input type="text" name="sdm_transport[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Trsp" value="0">
                                        <input type="text" name="sdm_bonus[]" class="form-control form-control-sm hitung-sdm rupiah-input mb-1" placeholder="Bonus" value="0">
                                        <input type="text" name="sdm_data[]" class="form-control form-control-sm hitung-sdm rupiah-input" placeholder="Data" value="0">
                                    </td>
                                    <td><input type="text" name="sdm_potongan[]" class="form-control form-control-sm hitung-sdm border-danger text-danger rupiah-input" value="0"><small class="d-block text-center">Maks: ${formatRupiah(p.hutang)}</small></td>
                                    <td><input type="text" name="sdm_total[]" class="form-control form-control-sm font-weight-bold text-right rupiah-input" readonly value="0"></td>
                                </tr>`;
                                });
                            }
                            $('#body_sdm').html(html);
                        }
                    }
                });
            } else {
                $('#event_info').hide();
                $('#body_sdm').html('');
            }
        });

        $('.hitung-income-trigger').change(function() {
            $('#box_it').toggle($('#check_it').is(':checked'));
            $('#box_log').toggle($('#check_log').is(':checked'));
            $('#box_lain').toggle($('#check_lain').is(':checked'));
            calculateIncome();
        });

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
                $('#subtotal_it').val(0);
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
                $('#subtotal_log').val(0);
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