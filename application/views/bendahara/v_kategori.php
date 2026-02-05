<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Master Keuangan</h1>
    </div>

    <div class="row">
        <!-- Form Tambah -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Kategori Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('bendahara/tambah_kategori') ?>" method="post">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Konsumsi, Tol, Parkir..." required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Daftar Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">No</th>
                                    <th>Nama Kategori</th>
                                    <th class="text-center" width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($kategori as $k): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= $k->nama_kategori ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('bendahara/hapus_kategori/' . $k->id_kategori) ?>"
                                                class="btn btn-sm btn-danger btn-circle btn-delete"
                                                data-title="Hapus Kategori?"
                                                data-message="Pastikan kategori ini tidak sedang dipakai di laporan keuangan.">
                                                <i class="fas fa-trash"></i>
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
    </div>
</div>