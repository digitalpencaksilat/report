<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Master Inventory</h1>

        <!-- Tombol Tambah Barang (Modal Trigger) -->
        <button class="btn btn-brand shadow-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Barang
        </button>
    </div>

    <!-- Notifikasi -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 5px solid #1cc88a !important;">
            <i class="fas fa-check-circle mr-1"></i> <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 5px solid #e74a3b !important;">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3" style="background-color: #fff; border-bottom: 2px solid var(--brand-primary);">
            <h6 class="m-0 font-weight-bold" style="color: var(--brand-primary);">
                <i class="fas fa-boxes mr-1"></i> Daftar Aset & Peralatan
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <!-- Header Merah -->
                    <thead style="background-color: var(--brand-primary); color: white;">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok Total</th>
                            <th class="text-center">Tersedia</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($barang as $b): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="font-weight-bold text-dark align-middle"><?= $b->kode_barang ?></td>
                                <td class="align-middle"><?= $b->nama_barang ?></td>
                                <td class="align-middle"><span class="badge badge-light border"><?= $b->kategori ?></span></td>
                                <td class="text-center font-weight-bold align-middle"><?= $b->stok_total ?></td>
                                <td class="text-center align-middle">
                                    <?php if ($b->stok_tersedia <= 0): ?>
                                        <span class="badge badge-danger">Habis</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?= $b->stok_tersedia ?></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Kolom Aksi -->
                                <td class="text-center align-middle text-nowrap">
                                    <div class="d-inline-flex">

                                        <!-- [BARU] Tombol History (Tracking) -->
                                        <a href="<?= base_url('barang/history/' . $b->id_barang) ?>"
                                            class="btn btn-sm btn-info btn-circle mr-1"
                                            title="Lihat Riwayat Pemakaian">
                                            <i class="fas fa-history"></i>
                                        </a>

                                        <!-- Tombol Edit (Modal) -->
                                        <button class="btn btn-sm btn-warning btn-circle mr-1"
                                            title="Edit"
                                            data-toggle="modal"
                                            data-target="#modalEdit<?= $b->id_barang ?>">
                                            <i class="fas fa-pen"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <a href="<?= base_url('barang/hapus/' . $b->id_barang) ?>"
                                            class="btn btn-sm btn-danger btn-circle btn-delete"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL EDIT (Looping di dalam foreach) -->
                            <div class="modal fade" id="modalEdit<?= $b->id_barang ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: var(--brand-primary); color: white;">
                                            <h5 class="modal-title font-weight-bold">Edit Barang</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="<?= base_url('barang/proses_edit') ?>" method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_barang" value="<?= $b->id_barang ?>">

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Kode Barang</label>
                                                    <!-- Readonly agar kode tidak berubah sembarangan -->
                                                    <input type="text" name="kode_barang" class="form-control" value="<?= $b->kode_barang ?>" readonly>
                                                    <small class="text-muted">Kode barang digenerate otomatis dan tidak dapat diubah.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Nama Barang</label>
                                                    <input type="text" name="nama_barang" class="form-control" value="<?= $b->nama_barang ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Kategori</label>
                                                    <select name="kategori" class="form-control" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        <?php foreach ($list_kategori as $kat): ?>
                                                            <option value="<?= $kat->nama_kategori ?>" <?= ($b->kategori == $kat->nama_kategori) ? 'selected' : '' ?>>
                                                                <?= $kat->nama_kategori ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Stok Total Fisik</label>
                                                    <input type="number" name="stok_total" class="form-control" value="<?= $b->stok_total ?>" min="0" required>
                                                    <small class="text-muted">Jika diubah, stok tersedia akan otomatis menyesuaikan.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-brand">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL EDIT -->

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH BARANG -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--brand-primary); color: white;">
                <h5 class="modal-title font-weight-bold">Tambah Barang Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('barang/proses_tambah') ?>" method="post">
                <div class="modal-body">

                    <div class="form-group">
                        <label class="font-weight-bold">Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori (Kode Otomatis) --</option>
                            <?php foreach ($list_kategori as $kat): ?>
                                <option value="<?= $kat->nama_kategori ?>"><?= $kat->nama_kategori ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Kode barang akan digenerate berdasarkan kategori (misal: Laptop -> LPT-001).</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Asus ROG Zephyrus" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Stok Awal</label>
                        <input type="number" name="stok_total" class="form-control" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>