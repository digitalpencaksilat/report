<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Dashboard Keuangan</h1>

        <!-- Tombol Quick Action -->
        <div>
            <a href="<?= base_url('bendahara/buat_laporan') ?>" class="btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-file-invoice mr-1"></i> Laporan Event Baru
            </a>
            <a href="<?= base_url('bendahara/buku_kas') ?>" class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-wallet mr-1"></i> Buka Buku Kas
            </a>
        </div>
    </div>

    <!-- ROW 1: STATUS KAS UTAMA (BRANKAS & POS-POS) -->
    <div class="row mb-4">
        <!-- 1. TOTAL UANG FISIK (REAL) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="background-color: #e8f5e9;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Brankas (Fisik)</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_real, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. POS DANA KAS PT (AVAILABLE) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pos Kas PT (Available)</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_kas_pt_now, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. POS DANA ANGSURAN (RESERVED) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pos Dana Angsuran</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_angsuran_now, 0, ',', '.') ?></div>
                            <small class="text-danger font-weight-bold" style="font-size: 9px;">*Kat: "Pembayaran Angsuran"</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. POS DANA ROYALTI (RESERVED) - [BARU] -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Pos Dana Royalti</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_royalti_now, 0, ',', '.') ?></div>
                            <small class="text-danger font-weight-bold" style="font-size: 9px;">*Kat: "Pembayaran Royalti"</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: ASET & OPERASIONAL -->
    <div class="row mb-4">
        <!-- Piutang Karyawan -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Aset Piutang (Kasbon Karyawan)</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($piutang_karyawan, 0, ',', '.') ?></div>
                            <small class="text-muted">Total pinjaman yang belum lunas</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Pending -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Event Belum Dilaporkan</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $event_pending ?> <span style="font-size:1rem">Event</span></div>
                            <small class="text-muted">Segera input laporan keuangan</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: STATISTIK AKUMULASI (SUMBER DANA) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Informasi Akumulasi Profit (Sumber Dana)</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-2 border-right">
                            <div class="text-muted small text-uppercase font-weight-bold">Total Omset Bruto</div>
                            <div class="h5 font-weight-bold text-dark">Rp <?= number_format($omset_bruto, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-md-3 mb-2 border-right">
                            <div class="text-muted small text-uppercase font-weight-bold">Total Share Kas PT</div>
                            <div class="h5 font-weight-bold text-primary">Rp <?= number_format($total_kas_pt_alloc, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-md-3 mb-2 border-right">
                            <div class="text-muted small text-uppercase font-weight-bold">Total Share Angsuran</div>
                            <div class="h5 font-weight-bold text-warning">Rp <?= number_format($total_angsuran_alloc, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-muted small text-uppercase font-weight-bold">Total Share Royalti</div>
                            <div class="h5 font-weight-bold text-secondary">Rp <?= number_format($total_royalti_alloc, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: CHART & RECENT -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Arus Kas 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">5 Transaksi Terakhir</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_trx)): ?>
                        <p class="text-muted text-center my-4">Belum ada transaksi.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_trx as $rx): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 font-weight-bold <?= $rx->jenis == 'masuk' ? 'text-success' : 'text-danger' ?>">
                                            <?= $rx->jenis == 'masuk' ? '+' : '-' ?> Rp <?= number_format($rx->nominal, 0, ',', '.') ?>
                                        </h6>
                                        <small class="text-muted"><?= date('d M', strtotime($rx->tanggal)) ?></small>
                                    </div>
                                    <p class="mb-1 small text-dark"><?= $rx->kategori ?></p>
                                    <small class="text-muted font-italic"><?= substr($rx->keterangan, 0, 40) ?>...</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="<?= base_url('bendahara/buku_kas') ?>" class="small font-weight-bold">Lihat Semua Transaksi &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- CHART.JS LIBRARY -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartLabels = <?= !empty($chart_data) ? json_encode(array_column($chart_data, 'nama_bulan')) : '[]' ?>;
    const chartMasuk = <?= !empty($chart_data) ? json_encode(array_column($chart_data, 'total_masuk')) : '[]' ?>;
    const chartKeluar = <?= !empty($chart_data) ? json_encode(array_column($chart_data, 'total_keluar')) : '[]' ?>;

    const ctx = document.getElementById('myAreaChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                    label: 'Pemasukan',
                    data: chartMasuk,
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Pengeluaran',
                    data: chartKeluar,
                    backgroundColor: 'rgba(231, 74, 59, 0.7)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });
</script>