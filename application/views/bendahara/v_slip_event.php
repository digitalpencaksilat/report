<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Slip Gaji Event</h1>
        <div>
            <!-- Tombol Download PDF via html2pdf -->
            <button onclick="generatePDF()" class="btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF (A4)
            </button>
            <a href="javascript:window.history.back()" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- AREA INI YANG AKAN DICETAK JADI PDF -->
            <div id="print-area" style="background-color: #ffffff; color: #000000; padding: 40px; position: relative;">

                <!-- KOP SLIP -->
                <div class="row border-bottom pb-4 mb-4 align-items-center" style="border-bottom: 3px double #000 !important;">
                    <div class="col-2 text-center">
                        <img src="<?= base_url('assets/logo/logo.png') ?>" alt="Logo" style="max-height: 80px;">
                    </div>
                    <div class="col-10 text-left">
                        <h3 class="font-weight-bold text-dark mb-0" style="font-family: Arial, sans-serif; font-size: 24px;">DIGITAL PENCAK SILAT</h3>
                        <p class="text-muted mb-0" style="font-size: 14px;">Management & Operational Services</p>
                        <div class="text-dark font-weight-bold mt-1" style="letter-spacing: 2px; font-size: 16px;">SLIP PENDAPATAN HONORARIUM KEGIATAN</div>
                    </div>
                </div>

                <!-- INFO PENERIMA & EVENT (Satu Kolom Kebawah) -->
                <div class="row mb-4">
                    <div class="col-12">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="pl-0 text-muted small font-weight-bold text-uppercase" width="150">Penerima</td>
                                <td class="font-weight-bold text-dark">: <?= $slip->nama_lengkap ?></td>
                            </tr>
                            <tr>
                                <td class="pl-0 text-muted small font-weight-bold text-uppercase">Event</td>
                                <td class="font-weight-bold text-dark">: <?= $slip->nama_event ?></td>
                            </tr>
                            <tr>
                                <td class="pl-0 text-muted small font-weight-bold text-uppercase">Lokasi</td>
                                <td class="text-dark">: <?= $slip->lokasi_event ?></td>
                            </tr>
                            <tr>
                                <td class="pl-0 text-muted small font-weight-bold text-uppercase">Tanggal</td>
                                <td class="text-dark">
                                    <span style="display: inline-block; margin-right: 5px;">:</span><?= date('d/m/Y', strtotime($slip->tgl_pinjam)) ?>
                                    s/d
                                    <?= date('d/m/Y', strtotime($slip->tgl_kembali_rencana)) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TABEL RINCIAN -->
                <!-- Inline style border solid black agar terbaca jelas oleh html2pdf -->
                <table class="table mb-4" style="width: 100%; border-collapse: collapse; border: 2px solid #000;">
                    <thead style="background-color: #f2f2f2;">
                        <tr>
                            <th style="border: 1px solid #000; padding: 10px;">Deskripsi Pendapatan</th>
                            <th class="text-right" style="border: 1px solid #000; padding: 10px;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px;">
                                <span class="font-weight-bold">Honor Pokok (Harian)</span><br>
                                <small class="text-muted">Rate: Rp <?= number_format($slip->honor_harian, 0, ',', '.') ?> x <?= $slip->jumlah_hari ?> Hari</small>
                            </td>
                            <td class="text-right align-middle" style="border: 1px solid #000; padding: 10px;">
                                <?= number_format($slip->honor_harian * $slip->jumlah_hari, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <?php if ($slip->nominal_setting > 0): ?>
                            <tr>
                                <td style="border: 1px solid #000; padding: 10px;">Jasa Setting & Bongkar Pasang</td>
                                <td class="text-right align-middle" style="border: 1px solid #000; padding: 10px;">
                                    <?= number_format($slip->nominal_setting, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($slip->nominal_transport > 0): ?>
                            <tr>
                                <td style="border: 1px solid #000; padding: 10px;">Uang Transport</td>
                                <td class="text-right align-middle" style="border: 1px solid #000; padding: 10px;">
                                    <?= number_format($slip->nominal_transport, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($slip->nominal_data > 0): ?>
                            <tr>
                                <td style="border: 1px solid #000; padding: 10px;">Insentif Pengelolaan Data</td>
                                <td class="text-right align-middle" style="border: 1px solid #000; padding: 10px;">
                                    <?= number_format($slip->nominal_data, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($slip->nominal_bonus > 0): ?>
                            <tr>
                                <td class="text-success font-weight-bold" style="border: 1px solid #000; padding: 10px;">Bonus Kinerja / Lembur</td>
                                <td class="text-right align-middle text-success font-weight-bold" style="border: 1px solid #000; padding: 10px;">
                                    <?= number_format($slip->nominal_bonus, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <!-- [BARU] Menampilkan Potongan Kasbon jika ada -->
                        <?php if (isset($slip->nominal_potongan) && $slip->nominal_potongan > 0): ?>
                            <tr>
                                <td class="text-danger font-weight-bold" style="border: 1px solid #000; padding: 10px;">
                                    Potongan Kasbon (Cicilan Hutang)
                                </td>
                                <td class="text-right align-middle text-danger font-weight-bold" style="border: 1px solid #000; padding: 10px;">
                                    - Rp <?= number_format($slip->nominal_potongan, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #333; color: white;">
                            <td class="font-weight-bold text-right text-uppercase" style="border: 1px solid #000; padding: 10px;">Total Diterima (Net)</td>
                            <td class="font-weight-bold text-right" style="font-size: 1.2rem; border: 1px solid #000; padding: 10px;">
                                Rp <?= number_format($slip->total_diterima, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- FOOTER TTD -->
                <div class="row mt-5 pt-4">
                    <div class="col-6 text-center">
                        <p class="mb-5 text-muted small">Penerima,</p>
                        <br><br>
                        <p class="font-weight-bold text-uppercase border-bottom border-dark d-inline-block px-4 mb-0 text-dark"><?= $slip->nama_lengkap ?></p>
                    </div>
                    <div class="col-6 text-center">
                        <p class="mb-5 text-muted small">Diserahkan Oleh,</p>
                        <br><br>
                        <p class="font-weight-bold text-uppercase border-bottom border-dark d-inline-block px-4 mb-0 text-dark">Finance Dept.</p>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <small class="text-muted" style="font-size: 10px;">Dokumen ini digenerate otomatis oleh sistem pada <?= date('d F Y H:i:s') ?></small>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- LIBRARY HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function generatePDF() {
        const element = document.getElementById('print-area');
        // Ubah nama file menjadi nama event
        const filename = 'Slip_Gaji_<?= $slip->nama_lengkap ?>_<?= $slip->nama_event ?>.pdf';

        const opt = {
            margin: 10, // Margin 10mm
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true
            }, // Scale 2 agar teks tajam
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
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