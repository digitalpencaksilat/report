<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Detail Laporan Keuangan</h1>
        <div>
            <!-- Tombol Download PDF via html2pdf -->
            <button onclick="generatePDF()" class="btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Download Laporan (PDF)
            </button>
            <a href="<?= base_url('bendahara/laporan') ?>" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">

            <!-- ========================================================= -->
            <!-- AREA INI YANG AKAN DICETAK JADI PDF -->
            <!-- ========================================================= -->
            <div id="print-area" style="background-color: #ffffff; color: #000000; padding: 40px; position: relative;">

                <!-- KOP LAPORAN -->
                <div class="row border-bottom pb-3 mb-4 align-items-center" style="border-bottom: 3px double #000 !important;">
                    <div class="col-2 text-center">
                        <!-- Pastikan path logo sesuai -->
                        <img src="<?= base_url('assets/logo/logo.png') ?>" alt="Logo" style="max-height: 80px;" onerror="this.style.display='none'">
                    </div>
                    <div class="col-10 text-left">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-family: Arial, sans-serif; font-size: 24px;">DIGITAL PENCAK SILAT</h3>
                        <p class="text-muted mb-0" style="font-size: 14px;">Management & Operational Services</p>
                        <div class="text-dark font-weight-bold mt-1" style="letter-spacing: 2px; font-size: 16px;">LAPORAN KEUANGAN KEGIATAN</div>
                    </div>
                </div>

                <!-- INFO HEADER EVENT -->
                <table class="table table-borderless table-sm mb-4">
                    <tr>
                        <td width="15%" class="font-weight-bold">Nama Event</td>
                        <td width="35%">: <?= $header->nama_event ?></td>
                        <td width="15%" class="font-weight-bold">Tanggal Event</td>
                        <td width="35%">: <?= date('d M Y', strtotime($header->tgl_pinjam)) ?> s/d <?= date('d M Y', strtotime($header->tgl_kembali_rencana)) ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Lokasi</td>
                        <td>: <?= $header->lokasi_event ?></td>
                        <td class="font-weight-bold">Kode Transaksi</td>
                        <td>: <?= $header->kode_transaksi ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Tgl Laporan</td>
                        <td>: <?= date('d M Y', strtotime($header->tgl_laporan)) ?></td>
                        <td class="font-weight-bold">Status</td>
                        <td>: Finalized</td>
                    </tr>
                </table>

                <!-- 1. PEMASUKAN (Updated Logic: IT, Logistik, Lainnya) -->
                <div class="mb-4">
                    <h5 class="font-weight-bold text-uppercase border-bottom border-dark pb-2 mb-3">I. Pemasukan (Income)</h5>
                    <table class="table table-bordered mb-0" style="border: 1px solid #000;">
                        <thead style="background-color: #f0f0f0;">
                            <tr>
                                <th style="border: 1px solid #000;">Keterangan / Sumber</th>
                                <th style="border: 1px solid #000;" class="text-right">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- A. IT / GELANGGANG -->
                            <?php if ($header->income_it_active): ?>
                                <tr>
                                    <td style="border: 1px solid #000;">
                                        <b>Pemasukan IT (Gelanggang)</b>
                                        <?php if ($header->jenis_pemasukan == 'detail'): ?>
                                            <br><small class="text-muted">Rincian: <?= number_format($header->harga_set, 0, ',', '.') ?> (Set) x <?= $header->jml_gelanggang ?> (Glg) x <?= $header->jml_hari ?> (Hari)</small>
                                        <?php else: ?>
                                            <br><small class="text-muted">Metode: Borongan / Global</small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="border: 1px solid #000;" class="text-right">
                                        <?php
                                        $sub_it = ($header->jenis_pemasukan == 'detail')
                                            ? ($header->harga_set * $header->jml_gelanggang * $header->jml_hari)
                                            : ($header->total_pemasukan - $header->log_total - $header->lain_nominal);
                                        echo number_format($sub_it, 0, ',', '.');
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- B. LOGISTIK -->
                            <?php if ($header->income_log_active): ?>
                                <tr>
                                    <td style="border: 1px solid #000;">
                                        <b>Pemasukan Logistik</b>
                                        <?php if ($header->log_harga > 0 || $header->log_qty > 0): ?>
                                            <br><small class="text-muted">Rincian: <?= number_format($header->log_harga, 0, ',', '.') ?> x <?= $header->log_qty ?> Pkt x <?= $header->log_hari ?> Hari</small>
                                        <?php else: ?>
                                            <br><small class="text-muted">Metode: Borongan / Global</small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="border: 1px solid #000;" class="text-right">
                                        <?= number_format($header->log_total, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- C. LAINNYA -->
                            <?php if ($header->income_lain_active): ?>
                                <tr>
                                    <td style="border: 1px solid #000;">
                                        <b>Pemasukan Lainnya</b><br>
                                        <small class="text-muted"><?= $header->lain_keterangan ?></small>
                                    </td>
                                    <td style="border: 1px solid #000;" class="text-right">
                                        <?= number_format($header->lain_nominal, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot style="background-color: #f0f0f0;">
                            <tr>
                                <td class="text-right font-weight-bold" style="border: 1px solid #000;">TOTAL PEMASUKAN</td>
                                <td class="text-right font-weight-bold" style="border: 1px solid #000;">
                                    Rp <?= number_format($header->total_pemasukan, 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 2. PENGELUARAN SDM (DETAIL) -->
                <div class="mb-4">
                    <h5 class="font-weight-bold text-uppercase border-bottom border-dark pb-2 mb-3">II. Biaya SDM (Payroll)</h5>
                    <table class="table table-bordered table-sm mb-0" style="border: 1px solid #000; font-size: 11px;">
                        <thead style="background-color: #f0f0f0; text-align: center;">
                            <tr>
                                <th style="border: 1px solid #000; vertical-align: middle;">Nama Personil</th>
                                <th style="border: 1px solid #000; vertical-align: middle;">Honor (Pokok)</th>
                                <!-- Kolom Rincian Tambahan -->
                                <th style="border: 1px solid #000; vertical-align: middle;">Setting</th>
                                <th style="border: 1px solid #000; vertical-align: middle;">Transp.</th>
                                <th style="border: 1px solid #000; vertical-align: middle;">Bonus</th>
                                <th style="border: 1px solid #000; vertical-align: middle;">Data</th>
                                <th style="border: 1px solid #000; vertical-align: middle; color: red;">Pot. Kasbon</th>
                                <th style="border: 1px solid #000; vertical-align: middle;">Total Terima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_potongan_all = 0;
                            foreach ($detail_sdm as $sdm):
                                $honor_pokok = $sdm->honor_harian * $sdm->jumlah_hari;
                                $total_potongan_all += $sdm->nominal_potongan;
                            ?>
                                <tr>
                                    <td style="border: 1px solid #000;">
                                        <b><?= $sdm->nama_lengkap ?></b><br>
                                        <i style="font-size: 10px;"><?= $sdm->peran ?></i>
                                    </td>
                                    <td style="border: 1px solid #000;" class="text-right">
                                        <?= number_format($honor_pokok, 0, ',', '.') ?>
                                    </td>
                                    <!-- Rincian Tambahan -->
                                    <td style="border: 1px solid #000;" class="text-right"><?= ($sdm->nominal_setting > 0) ? number_format($sdm->nominal_setting, 0, ',', '.') : '-' ?></td>
                                    <td style="border: 1px solid #000;" class="text-right"><?= ($sdm->nominal_transport > 0) ? number_format($sdm->nominal_transport, 0, ',', '.') : '-' ?></td>
                                    <td style="border: 1px solid #000;" class="text-right"><?= ($sdm->nominal_bonus > 0) ? number_format($sdm->nominal_bonus, 0, ',', '.') : '-' ?></td>
                                    <td style="border: 1px solid #000;" class="text-right"><?= ($sdm->nominal_data > 0) ? number_format($sdm->nominal_data, 0, ',', '.') : '-' ?></td>

                                    <!-- Potongan -->
                                    <td style="border: 1px solid #000; color: red;" class="text-right">
                                        <?= ($sdm->nominal_potongan > 0) ? '- ' . number_format($sdm->nominal_potongan, 0, ',', '.') : '-' ?>
                                    </td>

                                    <td style="border: 1px solid #000;" class="text-right font-weight-bold">
                                        <?= number_format($sdm->total_diterima, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot style="background-color: #f0f0f0;">
                            <tr>
                                <td colspan="7" class="text-right font-weight-bold" style="border: 1px solid #000;">Subtotal Biaya SDM:</td>
                                <td class="text-right font-weight-bold" style="border: 1px solid #000;">Rp <?= number_format($header->total_biaya_sdm, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 3. BIAYA OPERASIONAL -->
                <div class="mb-4">
                    <h5 class="font-weight-bold text-uppercase border-bottom border-dark pb-2 mb-3">III. Biaya Operasional</h5>
                    <table class="table table-bordered table-sm mb-0" style="border: 1px solid #000;">
                        <thead style="background-color: #f0f0f0;">
                            <tr>
                                <th style="border: 1px solid #000;">Kategori</th>
                                <th style="border: 1px solid #000;">Keterangan</th>
                                <th style="border: 1px solid #000;" class="text-right">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detail_ops)): ?>
                                <tr>
                                    <td colspan="3" class="text-center" style="border: 1px solid #000;">- Tidak ada pengeluaran operasional -</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($detail_ops as $ops): ?>
                                    <tr>
                                        <td style="border: 1px solid #000;"><?= $ops->nama_kategori ?></td>
                                        <td style="border: 1px solid #000;"><?= $ops->keterangan ?></td>
                                        <td style="border: 1px solid #000;" class="text-right"><?= number_format($ops->nominal, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot style="background-color: #f0f0f0;">
                            <tr>
                                <td colspan="2" class="text-right font-weight-bold" style="border: 1px solid #000;">Subtotal Operasional:</td>
                                <td class="text-right font-weight-bold" style="border: 1px solid #000;">Rp <?= number_format($header->total_biaya_ops, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 4. RINGKASAN AKHIR -->
                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered table-sm" style="border: 1px solid #000;">
                            <tr style="background-color: #333; color: white;">
                                <td colspan="2" class="font-weight-bold text-center">RINGKASAN LABA RUGI</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000;">Total Pemasukan</td>
                                <td class="text-right" style="border: 1px solid #000;"><?= number_format($header->total_pemasukan, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000;">(-) Total Biaya SDM</td>
                                <td class="text-right text-danger" style="border: 1px solid #000;"><?= number_format($header->total_biaya_sdm, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000;">(-) Total Operasional</td>
                                <td class="text-right text-danger" style="border: 1px solid #000;"><?= number_format($header->total_biaya_ops, 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($header->fee_intern_active): ?>
                                <tr>
                                    <td style="border: 1px solid #000;">(-) Fee Marketing Internal</td>
                                    <td class="text-right text-danger" style="border: 1px solid #000;"><?= number_format($header->fee_intern_nominal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($header->fee_ekstern_active): ?>
                                <tr>
                                    <td style="border: 1px solid #000;">(-) Fee Marketing Eksternal</td>
                                    <td class="text-right text-danger" style="border: 1px solid #000;"><?= number_format($header->fee_ekstern_nominal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr style="background-color: #f0f0f0;">
                                <td class="font-weight-bold" style="border: 1px solid #000;">LABA BERSIH (NET)</td>
                                <td class="text-right font-weight-bold" style="border: 1px solid #000; font-size: 1.1rem;">
                                    Rp <?= number_format($header->laba_kotor, 0, ',', '.') ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-6">
                        <?php if ($header->laba_kotor > 0): ?>
                            <table class="table table-bordered table-sm" style="border: 1px solid #000;">
                                <tr style="background-color: #333; color: white;">
                                    <td colspan="2" class="font-weight-bold text-center">PEMBAGIAN HASIL</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000;">Kas PT</td>
                                    <td class="text-right font-weight-bold" style="border: 1px solid #000;">
                                        <?= $header->share_kas_active ? number_format($header->kas_pt_nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000;">Angsuran</td>
                                    <td class="text-right font-weight-bold" style="border: 1px solid #000;">
                                        <?= $header->share_angsuran_active ? number_format($header->angsuran_nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000;">Royalti</td>
                                    <td class="text-right font-weight-bold" style="border: 1px solid #000;">
                                        <?= $header->share_royalti_active ? number_format($header->royalti_nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TTD -->
                <div class="row mt-5 pt-5">
                    <div class="col-6 text-center">
                        <p class="mb-5">Mengetahui,</p>
                        <br>
                        <p class="font-weight-bold text-uppercase border-bottom border-dark d-inline-block px-4" style="border-bottom: 1px solid #000 !important">Pimpinan / Direktur</p>
                    </div>
                    <div class="col-6 text-center">
                        <p class="mb-5">Dibuat Oleh,</p>
                        <br>
                        <p class="font-weight-bold text-uppercase border-bottom border-dark d-inline-block px-4" style="border-bottom: 1px solid #000 !important">Bendahara</p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted" style="font-size: 10px;">Laporan ini dicetak otomatis pada <?= date('d F Y H:i:s') ?></small>
                </div>

            </div>
            <!-- END PRINT AREA -->

        </div>
    </div>
</div>

<!-- LIBRARY HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function generatePDF() {
        const element = document.getElementById('print-area');
        // Nama file menggunakan nama event
        const filename = 'Laporan_Keuangan_<?= str_replace(' ', '_', $header->nama_event) ?>.pdf';

        const opt = {
            margin: 10,
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true
            },
            // Orientasi Landscape
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'landscape'
            }
        };

        const btn = document.querySelector('.btn-primary');
        const oldText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.disabled = true;

        html2pdf().set(opt).from(element).save().then(function() {
            btn.innerHTML = oldText;
            btn.disabled = false;
        });
    }
</script>