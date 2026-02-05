<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">User Manager</h1>
        <button class="btn btn-brand shadow-sm" data-toggle="modal" data-target="#modalTambahUser">
            <i class="fas fa-user-plus mr-1"></i> Tambah User
        </button>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-dark">Daftar Pengguna Sistem</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($users as $u): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle"><?= $u->nama_lengkap ?></td>
                                <td class="align-middle"><?= $u->username ?></td>
                                <td class="align-middle">
                                    <?php if ($u->role == 'admin'): ?>
                                        <span class="badge badge-primary">ADMIN</span>
                                    <?php elseif ($u->role == 'bendahara'): ?>
                                        <span class="badge badge-success">BENDAHARA</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">OPERATOR</span>
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
                                            data-title="Hapus User?"
                                            data-message="Akses user ini akan dicabut permanen."
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- MODAL EDIT USER -->
                            <div class="modal fade" id="modalEdit<?= $u->id_user ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title font-weight-bold">Edit User</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="<?= base_url('users/proses_edit') ?>" method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_user" value="<?= $u->id_user ?>">
                                                <div class="form-group">
                                                    <label>Nama Lengkap</label>
                                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= $u->nama_lengkap ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Role / Jabatan</label>
                                                    <select name="role" class="form-control" required>
                                                        <option value="operator" <?= ($u->role == 'operator') ? 'selected' : '' ?>>Operator</option>
                                                        <option value="admin" <?= ($u->role == 'admin') ? 'selected' : '' ?>>Admin</option>
                                                        <!-- [UPDATE] Opsi Bendahara -->
                                                        <option value="bendahara" <?= ($u->role == 'bendahara') ? 'selected' : '' ?>>Bendahara</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Password Baru (Opsional)</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
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
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">Tambah User Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('users/proses_tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Jabatan</label>
                        <select name="role" class="form-control" required>
                            <option value="operator">Operator</option>
                            <option value="admin">Admin</option>
                            <!-- [UPDATE] Opsi Bendahara -->
                            <option value="bendahara">Bendahara</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>