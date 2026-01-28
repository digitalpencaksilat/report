<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Kinerja Operator</h1>
        <p class="mb-0 text-muted">Monitoring jam terbang dan beban tugas personil.</p>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-cog mr-2"></i>Daftar Petugas Lapangan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <!-- Kolom Email DIHAPUS -->
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($operators as $op): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-dark"><?= $op->nama_lengkap ?></span>
                                </td>
                                <td class="align-middle"><?= $op->username ?></td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('riwayat/user/' . $op->id_user) ?>" class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-calendar-alt mr-1"></i> Lihat Riwayat Tugas
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>