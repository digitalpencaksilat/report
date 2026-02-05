<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Input Laporan Keuangan</h1>
        <a href="<?= base_url('bendahara/kategori') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?= base_url('bendahara/proses_simpan') ?>" method="post" id="formKeuangan">

        <!-- BAGIAN 1: PILIH EVENT -->
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">1. Identitas Event</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">Pilih Event</label>
                    <select name="id_peminjaman" id="selectEvent" class="form-control" required>
                        <option value="">-- Pilih Event --</option>
                        <?php foreach ($events as $ev): ?>
                            <option value="<?= $ev->id_peminjaman ?>">
                                [<?= $ev->kode_transaksi ?>] <?= $ev->nama_event ?> (<?= $ev->lokasi_event ?>) - Status: <?= ucfirst($ev->status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Menampilkan semua event (Draft/Berjalan/Selesai) yang belum dibuat laporan keuangannya.</small>
                </div>

                <div id="eventInfo" class="p-3 mb-3 bg-info text-white rounded d-none shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Lokasi:</strong> <span id="infoLokasi">-</span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <strong><i class="fas fa-calendar mr-1"></i> Tanggal:</strong> <span id="infoTanggal">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: PEMASUKAN -->
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">2. Pemasukan (Income)</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="font-weight-bold">Metode Hitung:</label><br>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="typeLumpsum" name="jenis_pemasukan" class="custom-control-input" value="lumpsum" checked>
                        <label class="custom-control-label" for="typeLumpsum">Total Langsung (Borongan)</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="typeDetail" name="jenis_pemasukan" class="custom-control-input" value="detail">
                        <label class="custom-control-label" for="typeDetail">Detail (Set x Gelanggang x Hari)</label>
                    </div>
                </div>

                <!-- Input Lumpsum -->
                <div id="boxLumpsum">
                    <div class="form-group">
                        <label>Total Pemasukan (Rp)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" id="inpTotalLumpsum" class="form-control font-weight-bold text-success rupiah-input" placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Input Detail -->
                <div id="boxDetail" class="d-none">
                    <div class="form-row">
                        <div class="col-md-4 mb-2">
                            <label>Harga Sewa per Set</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="harga_set" id="inpHargaSet" class="form-control input-calc rupiah-input" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Jumlah Gelanggang</label>
                            <input type="number" name="jml_gelanggang" id="inpGelanggang" class="form-control input-calc" placeholder="1">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Jumlah Hari Event</label>
                            <input type="number" name="jml_hari" id="inpHari" class="form-control input-calc" placeholder="1">
                        </div>
                    </div>
                </div>

                <div class="p-3 mt-3 bg-success text-white text-right rounded shadow-sm">
                    Total Pemasukan: <h3 class="font-weight-bold mb-0">Rp <span id="displayTotalPemasukan">0</span></h3>
                    <input type="hidden" name="total_pemasukan_final" id="valTotalPemasukan" value="0">
                </div>
            </div>
        </div>

        <!-- BAGIAN 3: HONOR OPERATOR (SDM) -->
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">3. Honorarium & Potongan Kasbon</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning small mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Kolom <b>"Potongan Kasbon"</b> akan muncul/aktif jika personil memiliki hutang.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="min-width: 1000px;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="vertical-align: middle; width: 15%;">Personil</th>
                                <th style="width: 20%;">Honor Pokok</th>
                                <th style="width: 20%;">Tambahan 1</th>
                                <th style="width: 20%;">Tambahan 2</th>
                                <!-- [BARU] Kolom Potongan -->
                                <th style="width: 15%;" class="bg-danger text-white">Potongan Kasbon</th>
                                <th style="vertical-align: middle; width: 10%;">Total Terima</th>
                            </tr>
                        </thead>
                        <tbody id="tableSDM">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Silakan pilih event terlebih dahulu untuk memuat data personil.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="5" class="text-right align-middle pr-3">Total Payroll SDM:</td>
                                <td class="align-middle text-right">
                                    Rp <span id="displayTotalSDM">0</span>
                                    <input type="hidden" name="total_biaya_sdm" id="valTotalSDM" value="0">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- BAGIAN 4: BIAYA OPERASIONAL -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger">4. Biaya Operasional</h6>
                <button type="button" class="btn btn-sm btn-danger" id="btnAddOps">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th width="30%">Nominal (Rp)</th>
                            <th width="5%">#</th>
                        </tr>
                    </thead>
                    <tbody id="tableOps">
                        <!-- Dynamic Rows Here -->
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td colspan="2" class="text-right">Total Operasional:</td>
                            <td colspan="2">Rp <span id="displayTotalOps">0</span>
                                <input type="hidden" name="total_biaya_ops" id="valTotalOps" value="0">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- BAGIAN 5: SUMMARY & PROFIT SHARING -->
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">5. Hasil Akhir & Pembagian</h6>
            </div>
            <div class="card-body">

                <!-- Fee Marketing -->
                <div class="form-group border-bottom pb-3">
                    <label class="font-weight-bold">Potongan Fee Marketing (Dari Total Pemasukan):</label>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input fee-check" id="checkFeeIntern" name="fee_intern_check">
                                <label class="custom-control-label" for="checkFeeIntern">Marketing Internal (5%)</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="dispFeeIntern" readonly value="Rp 0">
                            <input type="hidden" name="fee_intern_val" id="valFeeIntern" value="0">
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input fee-check" id="checkFeeEkstern" name="fee_ekstern_check">
                                <label class="custom-control-label" for="checkFeeEkstern">Marketing Eksternal (5%)</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="dispFeeEkstern" readonly value="Rp 0">
                            <input type="hidden" name="fee_ekstern_val" id="valFeeEkstern" value="0">
                        </div>
                    </div>
                </div>

                <div class="p-4 mb-4 bg-dark text-white text-center rounded shadow-sm">
                    <h5 class="mb-0 text-white-50">LABA BERSIH (NET PROFIT)</h5>
                    <h1 class="font-weight-bold text-success mb-0">Rp <span id="displayLabaBersih">0</span></h1>
                    <input type="hidden" name="laba_bersih_val" id="valLabaBersih" value="0">
                </div>

                <!-- Bagi Hasil (Split 3) -->
                <div class="row text-center mt-4">
                    <div class="col-md-4">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                <h6 class="text-muted font-weight-bold">Kas PT (33.3%)</h6>
                                <h4 class="font-weight-bold text-dark">Rp <span id="dispKasPT">0</span></h4>
                                <input type="hidden" name="share_kas_val" id="valKasPT" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                <h6 class="text-muted font-weight-bold">Angsuran (33.3%)</h6>
                                <h4 class="font-weight-bold text-dark">Rp <span id="dispAngsuran">0</span></h4>
                                <input type="hidden" name="share_angsuran_val" id="valAngsuran" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary mb-3">
                            <div class="card-body">
                                <h6 class="text-muted font-weight-bold">Royalti (33.3%)</h6>
                                <h4 class="font-weight-bold text-dark">Rp <span id="dispRoyalti">0</span></h4>
                                <input type="hidden" name="share_royalti_val" id="valRoyalti" value="0">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold" id="btnSimpan">
                    <i class="fas fa-save mr-2"></i> SIMPAN LAPORAN KEUANGAN
                </button>
            </div>
        </div>

    </form>
</div>

<!-- JAVASCRIPT LOGIC -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        // --- HELPER FUNCTIONS ---

        function parseRupiah(str) {
            if (!str) return 0;
            return parseInt(str.toString().replace(/\./g, '')) || 0;
        }

        function formatRupiahText(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // --- LOGIC AUTO FORMAT INPUT ---
        $(document).on('input', '.rupiah-input', function() {
            let rawValue = $(this).val().replace(/\D/g, '');
            if (rawValue === "") {
                $(this).val("");
            } else {
                let formatted = formatRupiahText(rawValue);
                $(this).val(formatted);
            }
            calculateAll();
        });


        // --- 1. HANDLING PILIH EVENT (AJAX) ---
        $('#selectEvent').change(function() {
            const id_peminjaman = $(this).val();
            const tbody = $('#tableSDM');

            if (id_peminjaman) {
                $.ajax({
                    url: '<?= base_url("bendahara/get_event_details_ajax") ?>',
                    type: 'POST',
                    data: {
                        id_peminjaman: id_peminjaman
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#eventInfo').removeClass('d-none');
                            $('#infoLokasi').text(response.event.lokasi_event);
                            $('#infoTanggal').text(response.event.tgl_pinjam);

                            tbody.empty();
                            if (response.personil.length > 0) {
                                response.personil.forEach(function(p, index) {

                                    // [LOGIKA BARU] Cek Hutang
                                    let hutangBadge = '';
                                    let potongInput = '';

                                    if (p.hutang > 0) {
                                        hutangBadge = `<br><span class="badge badge-danger">Hutang: Rp ${formatRupiahText(p.hutang)}</span>`;

                                        // Input untuk potongan kasbon
                                        potongInput = `
                                        <div class="input-group input-group-sm mt-1">
                                            <div class="input-group-prepend"><span class="input-group-text bg-danger text-white" style="font-size: 0.8em;">Potong</span></div>
                                            <input type="text" name="sdm_potongan[]" class="form-control sdm-calc rupiah-input text-danger font-weight-bold" 
                                                placeholder="0" 
                                                data-hutang="${p.hutang}">
                                        </div>
                                    `;
                                    } else {
                                        potongInput = `<input type="hidden" name="sdm_potongan[]" value="0">
                                                   <span class="text-muted small font-italic">Tidak ada kasbon</span>`;
                                    }

                                    let row = `
                                    <tr>
                                        <td class="align-middle">
                                            <b>${p.nama}</b><br>
                                            <span class="badge badge-secondary">${p.peran}</span>
                                            ${hutangBadge}
                                            <input type="hidden" name="sdm_id_user[]" value="${p.id}">
                                            <input type="hidden" name="sdm_peran[]" value="${p.peran}">
                                        </td>
                                        <td class="align-middle">
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Rate</span></div>
                                                <input type="text" name="sdm_honor[]" class="form-control sdm-calc rupiah-input" placeholder="0">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Hari</span></div>
                                                <input type="number" name="sdm_hari[]" class="form-control sdm-calc" placeholder="1" value="1">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Set.</span></div>
                                                <input type="text" name="sdm_setting[]" class="form-control sdm-calc rupiah-input" placeholder="0">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Trpt</span></div>
                                                <input type="text" name="sdm_transport[]" class="form-control sdm-calc rupiah-input" placeholder="0">
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Bns.</span></div>
                                                <input type="text" name="sdm_bonus[]" class="form-control sdm-calc rupiah-input" placeholder="0">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend"><span class="input-group-text" style="width: 45px;">Data</span></div>
                                                <input type="text" name="sdm_data[]" class="form-control sdm-calc rupiah-input" placeholder="0">
                                            </div>
                                        </td>
                                        <!-- [KOLOM BARU] Input Potongan -->
                                        <td class="align-middle text-center bg-light border-danger">
                                            ${potongInput}
                                        </td>
                                        <td class="align-middle text-right font-weight-bold">
                                            Rp <span class="sdm-subtotal">0</span>
                                            <input type="hidden" name="sdm_total[]" class="sdm-subtotal-val" value="0">
                                        </td>
                                    </tr>
                                `;
                                    tbody.append(row);
                                });
                            } else {
                                tbody.html('<tr><td colspan="6" class="text-center">Tidak ada data personil.</td></tr>');
                            }
                        }
                    }
                });
            } else {
                $('#eventInfo').addClass('d-none');
                tbody.html('<tr><td colspan="6" class="text-center text-muted">Silakan pilih event terlebih dahulu.</td></tr>');
            }
        });

        // --- 2. HANDLING PEMASUKAN ---
        $('input[name="jenis_pemasukan"]').change(function() {
            if ($(this).val() == 'lumpsum') {
                $('#boxLumpsum').removeClass('d-none');
                $('#boxDetail').addClass('d-none');
                $('.input-calc').val('');
            } else {
                $('#boxLumpsum').addClass('d-none');
                $('#boxDetail').removeClass('d-none');
                $('#inpTotalLumpsum').val('');
            }
            calculateAll();
        });

        $('#inpTotalLumpsum, .input-calc').on('keyup change', function() {
            calculateAll();
        });

        function calcPemasukan() {
            let total = 0;
            let type = $('input[name="jenis_pemasukan"]:checked').val();

            if (type == 'lumpsum') {
                total = parseRupiah($('#inpTotalLumpsum').val());
            } else {
                let harga = parseRupiah($('#inpHargaSet').val());
                let gelanggang = parseFloat($('#inpGelanggang').val()) || 0;
                let hari = parseFloat($('#inpHari').val()) || 0;
                total = harga * gelanggang * hari;
            }

            $('#displayTotalPemasukan').text(formatRupiahText(total));
            $('#valTotalPemasukan').val(total);
            return total;
        }


        // --- 3. HANDLING SDM (Event Delegation) ---
        $(document).on('keyup change', '.sdm-calc', function() {
            let row = $(this).closest('tr');

            // Komponen Utama
            let rate = parseRupiah(row.find('input[name="sdm_honor[]"]').val());
            let hari = parseFloat(row.find('input[name="sdm_hari[]"]').val()) || 0;
            let honorPokok = rate * hari;

            // Komponen Tambahan
            let setting = parseRupiah(row.find('input[name="sdm_setting[]"]').val());
            let transport = parseRupiah(row.find('input[name="sdm_transport[]"]').val());
            let bonus = parseRupiah(row.find('input[name="sdm_bonus[]"]').val());
            let data = parseRupiah(row.find('input[name="sdm_data[]"]').val());

            let totalBruto = honorPokok + setting + transport + bonus + data;

            // [LOGIKA POTONGAN]
            let potongan = 0;
            let potongInput = row.find('input[name="sdm_potongan[]"]');
            if (potongInput.length > 0) {
                potongan = parseRupiah(potongInput.val());
                let maxHutang = parseFloat(potongInput.data('hutang')) || 0;

                // Validasi 1: Tidak boleh > Hutang
                if (potongan > maxHutang) {
                    alert('Potongan tidak boleh melebihi sisa hutang (Rp ' + formatRupiahText(maxHutang) + ')');
                    potongan = maxHutang;
                    potongInput.val(formatRupiahText(potongan));
                }
                // Validasi 2: Tidak boleh > Total Pendapatan (Minus)
                if (potongan > totalBruto) {
                    alert('Potongan tidak boleh melebihi total pendapatan (Rp ' + formatRupiahText(totalBruto) + ')');
                    potongan = totalBruto;
                    potongInput.val(formatRupiahText(potongan));
                }
            }

            let totalTerima = totalBruto - potongan;

            row.find('.sdm-subtotal').text(formatRupiahText(totalTerima));
            row.find('.sdm-subtotal-val').val(totalTerima);

            calculateAll();
        });

        function calcSDM() {
            let total = 0;
            $('.sdm-subtotal-val').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#displayTotalSDM').text(formatRupiahText(total));
            $('#valTotalSDM').val(total);
            return total;
        }


        // --- 4. HANDLING OPS DYNAMIC ROW ---
        $('#btnAddOps').click(function() {
            let row = `
            <tr>
                <td>
                    <select name="ops_kategori[]" class="form-control" required>
                        <?php foreach ($kategori_ops as $k): ?>
                            <option value="<?= $k->id_kategori ?>"><?= $k->nama_kategori ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="text" name="ops_keterangan[]" class="form-control" placeholder="Ket."></td>
                <td>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                        <input type="text" name="ops_nominal[]" class="form-control ops-calc rupiah-input" placeholder="0">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-ops"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
            $('#tableOps').append(row);
        });

        $(document).on('click', '.btn-remove-ops', function() {
            $(this).closest('tr').remove();
            calculateAll();
        });

        $(document).on('keyup change', '.ops-calc', function() {
            calculateAll();
        });

        function calcOps() {
            let total = 0;
            $('.ops-calc').each(function() {
                total += parseRupiah($(this).val());
            });
            $('#displayTotalOps').text(formatRupiahText(total));
            $('#valTotalOps').val(total);
            return total;
        }


        // --- 5. MASTER CALCULATION (Fee, Laba, Split) ---
        $('.fee-check').change(function() {
            calculateAll();
        });

        function calculateAll() {
            let pemasukan = calcPemasukan();
            let sdm = calcSDM();
            let ops = calcOps();

            let feeIntern = $('#checkFeeIntern').is(':checked') ? (pemasukan * 0.05) : 0;
            let feeEkstern = $('#checkFeeEkstern').is(':checked') ? (pemasukan * 0.05) : 0;

            $('#dispFeeIntern').val(formatRupiahText(feeIntern));
            $('#valFeeIntern').val(feeIntern);
            $('#dispFeeEkstern').val(formatRupiahText(feeEkstern));
            $('#valFeeEkstern').val(feeEkstern);

            let totalPengeluaran = sdm + ops + feeIntern + feeEkstern;
            let labaBersih = pemasukan - totalPengeluaran;

            $('#displayLabaBersih').text(formatRupiahText(labaBersih));
            $('#valLabaBersih').val(labaBersih);

            if (labaBersih < 0) {
                $('#displayLabaBersih').addClass('text-danger').removeClass('text-success');
            } else {
                $('#displayLabaBersih').addClass('text-success').removeClass('text-danger');
            }

            let kasPT = 0;
            let angsuran = 0;
            let royalti = 0;

            if (labaBersih > 0) {
                let splitValue = Math.floor(labaBersih / 3);
                kasPT = splitValue;
                angsuran = splitValue;
                royalti = splitValue;
            }

            $('#dispKasPT').text(formatRupiahText(kasPT));
            $('#valKasPT').val(kasPT);
            $('#dispAngsuran').text(formatRupiahText(angsuran));
            $('#valAngsuran').val(angsuran);
            $('#dispRoyalti').text(formatRupiahText(royalti));
            $('#valRoyalti').val(royalti);
        }

        // --- 6. PRE-SUBMIT CLEANER ---
        $('#formKeuangan').on('submit', function() {
            $('.rupiah-input').each(function() {
                let raw = $(this).val();
                let clean = raw.replace(/\./g, '');
                $(this).val(clean);
            });
            return true;
        });

    });
</script>