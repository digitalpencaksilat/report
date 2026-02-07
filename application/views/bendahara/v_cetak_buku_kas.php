<!-- Container Fluid (Bootstrap standar dashboard) -->
<div class="container-fluid">

    <!-- TOOLBAR (Hanya tampil di Web, tidak masuk PDF) -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 d-print-none">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Preview Laporan</h1>

        <div>
            <a href="<?= base_url('bendahara/buku_kas') ?>" class="btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button onclick="downloadPDF()" class="btn btn-sm btn-warning shadow-sm font-weight-bold">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </button>
        </div>
    </div>

    <!-- AREA PREVIEW KERTAS (Ini yang akan dicetak) -->
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <!-- 
                ID "print-area".
                Padding dikurangi jadi 20px (kanan kiri) 10px (atas bawah) agar lebih hemat tempat.
            -->
            <div id="print-area" style="background-color: #ffffff; color: #000000; padding: 20px 30px; position: relative; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 100%;">

                <!-- STYLE KHUSUS PDF -->
                <style>
                    /* Reset Style dasar untuk PDF */
                    #print-area {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }

                    /* Tabel Layout Header (Pengganti Bootstrap Row) */
                    .layout-table {
                        width: 100%;
                        border: none;
                        border-collapse: collapse;
                        margin-bottom: 10px;
                    }

                    .layout-table td {
                        vertical-align: middle;
                        padding: 0;
                        border: none;
                    }

                    /* Tabel Data Transaksi */
                    .table-cetak {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 11px;
                        margin-top: 10px;
                    }

                    .table-cetak th,
                    .table-cetak td {
                        border: 1px solid #000;
                        padding: 5px 6px;
                    }

                    .table-cetak th {
                        background-color: #e3e3e3;
                        text-align: center;
                        font-weight: bold;
                        text-transform: uppercase;
                    }

                    /* Helper Classes */
                    .text-right {
                        text-align: right;
                    }

                    .text-center {
                        text-align: center;
                    }

                    .font-weight-bold {
                        font-weight: bold;
                    }

                    .text-uppercase {
                        text-transform: uppercase;
                    }

                    .mb-0 {
                        margin-bottom: 0;
                    }

                    .mt-1 {
                        margin-top: 5px;
                    }

                    .border-bottom-double {
                        border-bottom: 3px double #000;
                        padding-bottom: 10px;
                        margin-bottom: 15px;
                    }

                    /* Ringkasan Box */
                    .summary-box {
                        border: 1px solid #000;
                        padding: 10px;
                        background-color: #f9f9f9;
                        margin-bottom: 15px;
                    }

                    /* PAGE BREAK CONTROL */
                    /* Header tabel diulang di tiap halaman baru */
                    thead {
                        display: table-header-group;
                    }

                    /* Footer tabel (total) diulang (opsional) atau ditaruh di akhir */
                    tfoot {
                        display: table-row-group;
                    }

                    /* Baris jangan terpotong di tengah text */
                    tr {
                        page-break-inside: avoid;
                    }
                </style>

                <!-- 1. KOP LAPORAN (Menggunakan Tabel Layout agar stabil di PDF) -->
                <div class="border-bottom-double">
                    <table class="layout-table">
                        <tr>
                            <td width="12%" class="text-center">
                                <img src="<?= base_url('assets/logo/logo.png') ?>" alt="Logo" style="height: 70px;" onerror="this.style.display='none'">
                            </td>
                            <td width="88%" style="padding-left: 10px;">
                                <h3 class="font-weight-bold text-uppercase mb-0" style="font-size: 20px; color: #000;">DIGITAL PENCAK SILAT</h3>
                                <p class="mb-0" style="font-size: 12px; color: #444;">Management & Operational Services</p>
                                <div class="font-weight-bold mt-1" style="font-size: 16px; letter-spacing: 1px;">BUKU KAS UMUM (GENERAL LEDGER)</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 2. INFO PERIODE -->
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
                ?>
                <table class="layout-table" style="width: auto; margin-bottom: 15px;">
                    <tr>
                        <td class="font-weight-bold" style="width: 120px;">Periode Laporan</td>
                        <td>: <?= $filter_bulan ? $bln[$filter_bulan] : 'Semua Bulan' ?> <?= $filter_tahun ?></td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Dicetak Oleh</td>
                        <td>: <?= $this->session->userdata('nama_lengkap') ?> (<?= ucfirst($this->session->userdata('role')) ?>)</td>
                    </tr>
                </table>

                <!-- 3. RINGKASAN SALDO (Compact Table) -->
                <div class="summary-box">
                    <table class="layout-table text-center" style="margin-bottom: 0;">
                        <tr>
                            <td width="25%" style="border-right: 1px solid #ccc;">
                                <span style="font-size: 10px; color: #555;">Uang Fisik (Real)</span><br>
                                <strong style="font-size: 13px;">Rp <?= isset($saldo_akhir) ? number_format($saldo_akhir, 0, ',', '.') : '-' ?></strong>
                            </td>
                            <td width="25%" style="border-right: 1px solid #ccc;">
                                <span style="font-size: 10px; color: #555;">Total Masuk</span><br>
                                <?php
                                $t_masuk = 0;
                                if (!empty($kas)) {
                                    foreach ($kas as $k) {
                                        if ($k->jenis == 'masuk') $t_masuk += $k->nominal;
                                    }
                                }
                                ?>
                                <strong style="font-size: 13px;">Rp <?= number_format($t_masuk, 0, ',', '.') ?></strong>
                            </td>
                            <td width="25%" style="border-right: 1px solid #ccc;">
                                <span style="font-size: 10px; color: #555;">Total Keluar</span><br>
                                <?php
                                $t_keluar = 0;
                                if (!empty($kas)) {
                                    foreach ($kas as $k) {
                                        if ($k->jenis == 'keluar') $t_keluar += $k->nominal;
                                    }
                                }
                                ?>
                                <strong style="font-size: 13px;">Rp <?= number_format($t_keluar, 0, ',', '.') ?></strong>
                            </td>
                            <td width="25%">
                                <span style="font-size: 10px; color: #555;">Surplus/Defisit</span><br>
                                <strong style="font-size: 13px;">Rp <?= number_format($t_masuk - $t_keluar, 0, ',', '.') ?></strong>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 4. TABEL TRANSAKSI -->
                <table class="table-cetak">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th>Keterangan Transaksi</th>
                            <th width="15%">Kategori</th>
                            <th width="15%">Masuk (Rp)</th>
                            <th width="15%">Keluar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;

                        if (empty($kas)):
                        ?>
                            <tr>
                                <td colspan="6" class="text-center font-italic" style="padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kas as $k): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($k->tanggal)) ?></td>
                                    <td>
                                        <b><?= $k->keterangan ?></b><br>
                                        <i style="font-size: 9px; color: #555;">Ref: <?= $k->sumber_auto ?></i>
                                    </td>
                                    <td class="text-center"><?= $k->kategori ?></td>
                                    <td class="text-right">
                                        <?= ($k->jenis == 'masuk') ? number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-right">
                                        <?= ($k->jenis == 'keluar') ? number_format($k->nominal, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <!-- TOTAL AKHIR TABEL -->
                        <tr style="background-color: #f0f0f0;">
                            <td colspan="4" class="text-right font-weight-bold">TOTAL PERIODE INI</td>
                            <td class="text-right font-weight-bold">Rp <?= number_format($t_masuk, 0, ',', '.') ?></td>
                            <td class="text-right font-weight-bold">Rp <?= number_format($t_keluar, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="background-color: #e8e8e8;">
                            <td colspan="4" class="text-right font-weight-bold">SALDO AKHIR (FISIK)</td>
                            <td colspan="2" class="text-center font-weight-bold" style="font-size: 1.1rem;">
                                Rp <?= isset($saldo_akhir) ? number_format($saldo_akhir, 0, ',', '.') : '-' ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- 5. TANDA TANGAN (Menggunakan Tabel Layout agar tidak berantakan di PDF) -->
                <!-- margin-top besar agar memberi jarak, page-break-inside: avoid mencegah TTD terpotong -->
                <table class="layout-table" style="margin-top: 50px; page-break-inside: avoid;">
                    <tr>
                        <td width="33%" class="text-center">
                            <p class="mb-0">Mengetahui,</p>
                            <br><br><br><br>
                            <p class="font-weight-bold text-uppercase border-bottom d-inline-block px-4" style="border-bottom: 1px solid #000;">Pimpinan</p>
                        </td>
                        <td width="33%"></td>
                        <td width="33%" class="text-center">
                            <p class="mb-0">Dibuat Oleh,</p>
                            <br><br><br><br>
                            <p class="font-weight-bold text-uppercase border-bottom d-inline-block px-4" style="border-bottom: 1px solid #000;">Bendahara</p>
                        </td>
                    </tr>
                </table>

                <div class="text-center mt-3">
                    <small style="font-size: 9px; color: #888;">Dicetak otomatis pada <?= date('d F Y H:i:s') ?></small>
                </div>

            </div> <!-- END ID print-area -->
        </div>
    </div>
</div>

<!-- Library HTML2PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPDF() {
        const element = document.getElementById('print-area');
        const filename = 'Buku_Kas_Umum_<?= $filter_bulan ? $bln[$filter_bulan] : 'All' ?>_<?= $filter_tahun ?>.pdf';

        const opt = {
            // Margin [Top, Left, Bottom, Right] dalam mm.
            margin: [10, 10, 10, 10],

            filename: filename,
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'landscape'
            },

            // [PENTING] PERBAIKAN PAGEBREAK
            // 'css' mode respects 'page-break-...' properties.
            // Hapus 'avoid-all' agar konten tidak dipaksa pindah ke halaman baru secara utuh.
            pagebreak: {
                mode: ['css', 'legacy']
            }
        };

        const btn = document.querySelector('.btn-warning');
        const oldText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;

        html2pdf().set(opt).from(element).save().then(function() {
            btn.innerHTML = oldText;
            btn.disabled = false;
        });
    }
</script>