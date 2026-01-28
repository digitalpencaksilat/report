<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">User Manager</h1>

        <button class="btn btn-brand shadow-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i> Tambah User
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
                <i class="fas fa-users-cog mr-1"></i> Daftar Pengguna Sistem
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead style="background-color: var(--brand-primary); color: white;">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($users as $u): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle font-weight-bold text-dark"><?= $u->nama_lengkap ?></td>
                                <td class="align-middle"><?= $u->username ?></td>
                                <td class="align-middle">
                                    <?php if ($u->role == 'admin'): ?>
                                        <span class="badge badge-primary"><i class="fas fa-user-shield mr-1"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fas fa-user mr-1"></i> Operator</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-sm btn-warning btn-circle"
                                        title="Edit"
                                        data-toggle="modal"
                                        data-target="#modalEdit<?= $u->id_user ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    <?php if ($u->id_user != $this->session->userdata('id_user')): ?>
                                        <a href="<?= base_url('users/hapus/' . $u->id_user) ?>"
                                            class="btn btn-sm btn-danger btn-circle btn-delete"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- MODAL EDIT -->
                            <div class="modal fade" id="modalEdit<?= $u->id_user ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: var(--brand-primary); color: white;">
                                            <h5 class="modal-title font-weight-bold">Edit User</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="<?= base_url('users/proses_edit') ?>" method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_user" value="<?= $u->id_user ?>">

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Nama Lengkap</label>
                                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= $u->nama_lengkap ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Username</label>
                                                    <input type="text" name="username" class="form-control" value="<?= $u->username ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Role</label>
                                                    <select name="role" class="form-control" required>
                                                        <option value="admin" <?= ($u->role == 'admin') ? 'selected' : '' ?>>Admin</option>
                                                        <option value="operator" <?= ($u->role == 'operator') ? 'selected' : '' ?>>Operator</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Password Baru</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--brand-primary); color: white;">
                <h5 class="modal-title font-weight-bold">Tambah User Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('users/proses_tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Asep Suhendar" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username untuk login" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>