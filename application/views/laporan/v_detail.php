<div class="container-fluid">

    <!-- Header & Navigasi -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transaksi</h1>
        <a href="<?= base_url('laporan') ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Notifikasi Flashdata -->
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
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- Kiri: Informasi Event -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom-primary">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Event</h6>

                    <!-- LOGIK STATUS & TOMBOL ADMIN -->
                    <?php if ($laporan->status == 'selesai'): ?>
                        <span class="badge badge-success px-3 py-2"><i class="fas fa-flag-checkered mr-1"></i> SELESAI</span>

                    <?php elseif ($laporan->is_locked == 1): ?>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-secondary px-2 py-1 mr-2"><i class="fas fa-lock"></i> LOCKED</span>

                            <?php if ($this->session->userdata('role') == 'admin'): ?>
                                <a href="<?= base_url('laporan/unlock_action/' . $laporan->id_peminjaman) ?>"
                                    class="btn btn-sm btn-warning shadow-sm mr-1"
                                    onclick="return confirm('Buka kunci? Data pengembalian mungkin akan tidak sinkron jika diedit ulang.')"
                                    title="Buka Kunci">
                                    <i class="fas fa-lock-open"></i>
                                </a>

                                <a href="<?= base_url('laporan/finalize_action/' . $laporan->id_peminjaman) ?>"
                                    class="btn btn-sm btn-success shadow-sm"
                                    onclick="return confirm('Tandai transaksi ini SELESAI? Pastikan semua barang fisik sudah diterima kembali.')"
                                    title="Tandai Selesai">
                                    <i class="fas fa-check-double"></i> Selesai
                                </a>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-info px-2 py-1 mr-2"><i class="fas fa-pen"></i> DRAFT</span>

                            <?php if ($this->session->userdata('role') == 'admin'): ?>
                                <a href="<?= base_url('laporan/lock_action/' . $laporan->id_peminjaman) ?>"
                                    class="btn btn-sm btn-danger shadow-sm"
                                    onclick="return confirm('Kunci laporan? Setelah ini menu PENGEMBALIAN akan aktif.')">
                                    <i class="fas fa-lock"></i> Lock & Jalan
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted font-weight-bold">NAMA KEJUARAAN / EVENT</label>
                        <h5 class="font-weight-bold text-dark"><?= $laporan->nama_event ?></h5>
                        <p class="text-muted mb-0"><i class="fas fa-map-marker-alt mr-1"></i> <?= $laporan->lokasi_event ?></p>
                    </div>

                    <!-- [UPDATE] Informasi Personil Bertugas dengan Tombol Edit -->
                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="small text-muted font-weight-bold mb-0">PERSONIL BERTUGAS</label>

                            <!-- Tombol Edit Petugas (Hanya Admin & Jika belum selesai) -->
                            <?php if ($this->session->userdata('role') == 'admin' && $laporan->status != 'selesai'): ?>
                                <button class="btn btn-xs btn-outline-primary" data-toggle="modal" data-target="#modalEditPetugas">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user-shield mr-2 text-primary"></i>
                            <span class="font-weight-bold text-dark"><?= $laporan->nama_operator ?></span>
                            <span class="badge badge-primary ml-2">Utama</span>
                        </div>

                        <!-- List Petugas Tambahan -->
                        <?php if (!empty($list_petugas_tambahan)): ?>
                            <hr class="my-2 border-white">
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1 font-italic">Petugas Pendamping:</small>
                                <?php foreach ($list_petugas_tambahan as $pt): ?>
                                    <div class="d-flex align-items-center mb-1 ml-3">
                                        <i class="fas fa-user mr-2 text-secondary small"></i>
                                        <span class="small text-dark font-weight-bold"><?= $pt->nama_lengkap ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-muted font-weight-bold">TGL PINJAM</label>
                            <p class="font-weight-bold text-dark"><?= date('d M Y', strtotime($laporan->tgl_pinjam)) ?></p>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted font-weight-bold">RENCANA KEMBALI</label>
                            <p class="font-weight-bold text-danger"><?= date('d M Y', strtotime($laporan->tgl_kembali_rencana)) ?></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted font-weight-bold">KODE TRANSAKSI</label><br>
                        <span class="badge badge-light border px-2 py-1"><?= $laporan->kode_transaksi ?></span>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted font-weight-bold">KETERANGAN</label>
                        <p class="small text-dark mb-0 font-italic"><?= $laporan->keterangan ? nl2br($laporan->keterangan) : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan: Daftar Barang & Form Input -->
        <div class="col-xl-8 col-lg-7">

            <!-- FORM INPUT BARANG (Hanya Muncul Jika Belum Locked) -->
            <?php if ($laporan->is_locked == 0): ?>
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-1"></i> Tambah Item Barang</h6>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url('laporan/tambah_item_action') ?>" method="post" class="form-row align-items-end">
                            <input type="hidden" name="id_peminjaman" value="<?= $laporan->id_peminjaman ?>">

                            <div class="form-group col-md-7 mb-2">
                                <label class="small font-weight-bold">Pilih Barang</label>
                                <select name="id_barang" class="form-control" required>
                                    <option value="">-- Cari Barang Inventory --</option>
                                    <?php foreach ($semua_barang as $brg): ?>
                                        <option value="<?= $brg->id_barang ?>">
                                            <?= $brg->nama_barang ?> (Sisa: <?= $brg->stok_tersedia ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group col-md-3 mb-2">
                                <label class="small font-weight-bold">Jumlah</label>
                                <input type="number" name="qty" class="form-control" placeholder="0" min="1" required>
                            </div>

                            <div class="form-group col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fas fa-save"></i> Add</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ALERT MODE PENGEMBALIAN (Jika Locked tapi belum Selesai) -->
            <?php elseif ($laporan->status != 'selesai'): ?>
                <div class="alert alert-info border-left-info shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle bg-info text-white">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Mode Pengembalian Aktif</h6>
                            <span class="small">Barang sedang digunakan. Silakan update kolom <b>"Qty Kembali"</b> dan <b>"Kondisi"</b> di tabel bawah saat barang dikembalikan ke gudang.</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TABEL LIST BARANG -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white d-flex justify-content-between border-bottom-danger">
                    <h6 class="m-0 font-weight-bold text-dark">Daftar Barang Inventaris</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="pl-4">Nama Barang</th>
                                    <th class="text-center">Dipinjam</th>

                                    <?php if ($laporan->is_locked == 1): ?>
                                        <th class="text-center" width="15%">Kembali</th>
                                        <th class="text-center" width="20%">Kondisi</th>
                                        <?php if ($laporan->status != 'selesai'): ?>
                                            <th class="text-center">Update</th>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <th class="text-center">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($detail)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 text-gray-300"></i><br>
                                            Belum ada barang yang ditambahkan ke laporan ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($detail as $d): ?>
                                        <tr>
                                            <td class="pl-4 align-middle">
                                                <span class="font-weight-bold text-dark"><?= $d->nama_barang ?></span><br>
                                                <small class="text-muted font-monospace"><?= $d->kode_barang ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border px-2 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                                    <?= $d->qty_pinjam ?>
                                                </span>
                                            </td>

                                            <!-- LOGIK TAMPILAN SAAT LOCKED (Mode Pengembalian) -->
                                            <?php if ($laporan->is_locked == 1): ?>

                                                <?php if ($laporan->status != 'selesai'): ?>
                                                    <!-- FORM UPDATE PENGEMBALIAN -->
                                                    <form action="<?= base_url('laporan/proses_pengembalian_item') ?>" method="post">
                                                        <input type="hidden" name="id_peminjaman" value="<?= $laporan->id_peminjaman ?>">
                                                        <input type="hidden" name="id_detail" value="<?= $d->id_detail ?>">

                                                        <td class="align-middle">
                                                            <input type="number" name="qty_kembali"
                                                                class="form-control form-control-sm text-center font-weight-bold 
                                                               <?= ($d->qty_kembali == $d->qty_pinjam) ? 'text-success border-success' : 'text-danger border-danger' ?>"
                                                                value="<?= $d->qty_kembali ?>"
                                                                min="0" max="<?= $d->qty_pinjam ?>">
                                                        </td>
                                                        <td class="align-middle">
                                                            <select name="kondisi_kembali" class="form-control form-control-sm">
                                                                <option value="baik" <?= ($d->kondisi_kembali == 'baik') ? 'selected' : '' ?>>Baik</option>
                                                                <option value="rusak" <?= ($d->kondisi_kembali == 'rusak') ? 'selected' : '' ?>>Rusak</option>
                                                                <option value="hilang" <?= ($d->kondisi_kembali == 'hilang') ? 'selected' : '' ?>>Hilang</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <button type="submit" class="btn btn-sm btn-primary btn-circle shadow-sm" title="Simpan Perubahan">
                                                                <i class="fas fa-save"></i>
                                                            </button>
                                                        </td>
                                                    </form>

                                                <?php else: ?>
                                                    <!-- TAMPILAN READONLY SAAT SELESAI -->
                                                    <td class="text-center align-middle">
                                                        <span class="font-weight-bold <?= ($d->qty_kembali == $d->qty_pinjam) ? 'text-success' : 'text-danger' ?>">
                                                            <?= $d->qty_kembali ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php
                                                        $badgeClass = 'badge-success';
                                                        if ($d->kondisi_kembali == 'rusak') $badgeClass = 'badge-warning';
                                                        if ($d->kondisi_kembali == 'hilang') $badgeClass = 'badge-danger';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($d->kondisi_kembali) ?></span>
                                                    </td>
                                                <?php endif; ?>

                                                <!-- LOGIK TAMPILAN SAAT DRAFT (Edit Mode) -->
                                            <?php else: ?>
                                                <td class="text-center align-middle text-nowrap">
                                                    <!-- Tombol Edit Modal -->
                                                    <button class="btn btn-sm btn-warning btn-circle mr-1"
                                                        data-toggle="modal"
                                                        data-target="#editModal<?= $d->id_detail ?>"
                                                        title="Edit Jumlah">
                                                        <i class="fas fa-pen"></i>
                                                    </button>

                                                    <!-- Tombol Hapus -->
                                                    <a href="<?= base_url('laporan/hapus_item/' . $d->id_detail . '/' . $laporan->id_peminjaman) ?>"
                                                        class="btn btn-sm btn-danger btn-circle"
                                                        onclick="return confirm('Hapus item ini dari daftar?')"
                                                        title="Hapus Item">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <!-- MODAL EDIT QTY -->
                                                    <div class="modal fade" id="editModal<?= $d->id_detail ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-warning text-white">
                                                                    <h6 class="modal-title font-weight-bold">Edit Jumlah</h6>
                                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <form action="<?= base_url('laporan/update_item_action') ?>" method="post">
                                                                    <div class="modal-body text-left">
                                                                        <input type="hidden" name="id_peminjaman" value="<?= $laporan->id_peminjaman ?>">
                                                                        <input type="hidden" name="id_detail" value="<?= $d->id_detail ?>">
                                                                        <div class="form-group">
                                                                            <label class="small font-weight-bold">Jumlah Baru</label>
                                                                            <input type="number" name="qty_edit" class="form-control" value="<?= $d->qty_pinjam ?>" min="1" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer p-2">
                                                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- [BARU] MODAL EDIT PETUGAS -->
<?php if ($this->session->userdata('role') == 'admin'): ?>
    <div class="modal fade" id="modalEditPetugas" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--brand-primary); color: white;">
                    <h5 class="modal-title font-weight-bold">Edit Personil Bertugas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('laporan/update_petugas_action') ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id_peminjaman" value="<?= $laporan->id_peminjaman ?>">

                        <!-- Petugas Utama -->
                        <div class="form-group">
                            <label class="font-weight-bold">Petugas Utama</label>
                            <select name="id_operator" class="form-control" required>
                                <option value="">-- Pilih Operator --</option>
                                <?php foreach ($list_operator as $op): ?>
                                    <option value="<?= $op->id_user ?>" <?= ($op->id_user == $laporan->id_operator) ? 'selected' : '' ?>>
                                        <?= $op->nama_lengkap ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Penanggung jawab utama laporan.</small>
                        </div>

                        <!-- Petugas Tambahan (Dinamis) -->
                        <div class="form-group">
                            <label class="font-weight-bold">Petugas Tambahan</label>
                            <div id="edit-petugas-container">
                                <!-- Loop Data Existing -->
                                <?php
                                if (!empty($laporan->petugas_tambahan)):
                                    $ids = json_decode($laporan->petugas_tambahan);
                                    if (is_array($ids)):
                                        foreach ($ids as $existing_id):
                                ?>
                                            <div class="d-flex mb-2 align-items-center petugas-row">
                                                <select name="petugas_tambahan[]" class="form-control">
                                                    <?php foreach ($list_operator as $op): ?>
                                                        <option value="<?= $op->id_user ?>" <?= ($op->id_user == $existing_id) ? 'selected' : '' ?>>
                                                            <?= $op->nama_lengkap ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="button" class="btn btn-danger btn-sm ml-2 btn-hapus-row"><i class="fas fa-times"></i></button>
                                            </div>
                                <?php
                                        endforeach;
                                    endif;
                                endif;
                                ?>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-tambah-edit-petugas">
                                <i class="fas fa-plus mr-1"></i> Tambah Personil
                            </button>
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

    <!-- Script JS untuk Modal Edit Petugas -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnTambah = document.getElementById('btn-tambah-edit-petugas');
            const container = document.getElementById('edit-petugas-container');

            // Buat string opsi dropdown dari PHP untuk dipakai di JS
            let optionsHtml = '<option value="">-- Pilih Petugas --</option>';
            <?php foreach ($list_operator as $op): ?>
                optionsHtml += '<option value="<?= $op->id_user ?>"><?= addslashes($op->nama_lengkap) ?></option>';
            <?php endforeach; ?>

            if (btnTambah) {
                btnTambah.addEventListener('click', function() {
                    const div = document.createElement('div');
                    div.className = 'd-flex mb-2 align-items-center petugas-row';

                    div.innerHTML = `
                <select name="petugas_tambahan[]" class="form-control" required>
                    ${optionsHtml}
                </select>
                <button type="button" class="btn btn-danger btn-sm ml-2 btn-hapus-row">
                    <i class="fas fa-times"></i>
                </button>
            `;
                    container.appendChild(div);
                });
            }

            // Event Delegation untuk tombol hapus (baik yang existing maupun baru)
            if (container) {
                container.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-hapus-row')) {
                        e.target.closest('.petugas-row').remove();
                    }
                });
            }
        });
    </script>
<?php endif; ?>