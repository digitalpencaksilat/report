<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Daftar Laporan Keuangan</h1>
        <a href="<?= base_url('bendahara/buat_laporan') ?>" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Laporan Baru
        </a>
    </div>

    <!-- Tabel Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Laporan Event</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Laporan</th>
                            <th>Nama Event</th>
                            <th>Kode Transaksi</th>
                            <th>Pemasukan</th>
                            <th>Laba Bersih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data laporan keuangan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1;
                            foreach ($laporan as $l): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($l->tgl_laporan)) ?></td>
                                    <td>
                                        <b><?= $l->nama_event ?></b><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= $l->lokasi_event ?></small>
                                    </td>
                                    <td class="text-center"><span class="badge badge-secondary"><?= $l->kode_transaksi ?></span></td>
                                    <td class="text-right">Rp <?= number_format($l->total_pemasukan, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($l->laba_kotor, 0, ',', '.') ?></td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <!-- Tombol Lihat Detail -->
                                        <a href="<?= base_url('bendahara/detail/' . $l->id_keuangan) ?>" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>

                                        <!-- [BARU] Tombol Edit -->
                                        <a href="<?= base_url('bendahara/edit_laporan/' . $l->id_keuangan) ?>" class="btn btn-warning btn-sm" title="Edit Data"><i class="fas fa-edit"></i></a>

                                        <!-- Tombol Hapus -->
                                        <a href="<?= base_url('bendahara/hapus_laporan/' . $l->id_keuangan) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus? Data kasbon & buku kas terkait akan dihapus juga.')" title="Hapus"><i class="fas fa-trash"></i></a>
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

<script>
    // Inisialisasi DataTable jika belum ada di footer global
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        }
    });
</script>