<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Laporan Event & Kejuaraan</h1>

        <!-- [UPDATE] Tombol Tambah HANYA UNTUK ADMIN -->
        <?php if ($this->session->userdata('role') == 'admin'): ?>
            <a href="<?= base_url('laporan/buat_baru') ?>" class="btn shadow-sm text-white" style="background-color: #aa1818; border-color: #aa1818;">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Laporan Baru
            </a>
        <?php endif; ?>
    </div>

    <!-- Flashdata Notification -->
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #fff; border-bottom: 2px solid #aa1818;">
            <h6 class="m-0 font-weight-bold" style="color: #aa1818;"><i class="fas fa-clipboard-list mr-1"></i> Data Pemakaian Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead style="background-color: #aa1818; color: white;">
                        <tr>
                            <th style="border-top: none;">Tanggal</th>
                            <th style="border-top: none;">Nama Event</th>
                            <th style="border-top: none;">Operator Bertugas</th>
                            <th style="border-top: none;" class="text-center">Status</th>
                            <th style="border-top: none;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporan as $row): ?>

                            <?php
                            // Styling baris berdasarkan status
                            $rowClass = '';
                            if ($row->status == 'selesai') $rowClass = 'bg-success-light';
                            elseif ($row->is_locked == 1) $rowClass = 'bg-light text-muted';
                            ?>

                            <tr class="<?= $rowClass ?>" style="<?= ($row->status == 'selesai') ? 'background-color: #e8f5e9;' : '' ?>">
                                <td class="align-middle border-left-danger"><?= date('d M Y', strtotime($row->tgl_pinjam)) ?></td>
                                <td class="align-middle">
                                    <b class="text-dark"><?= $row->nama_event ?></b>
                                    <br><small class="text-muted"><i class="fas fa-hashtag mr-1"></i><?= $row->kode_transaksi ?></small>
                                </td>

                                <!-- [UPDATE] Kolom Operator dengan Badge Petugas Tambahan -->
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-lg mr-2 text-gray-400"></i>
                                        <div>
                                            <div class="font-weight-bold text-dark"><?= $row->nama_operator ?></div>
                                            <?php
                                            // Hitung Petugas Tambahan
                                            if (!empty($row->petugas_tambahan)) {
                                                $tambahan = json_decode($row->petugas_tambahan);
                                                $count = is_array($tambahan) ? count($tambahan) : 0;
                                                if ($count > 0) {
                                                    echo "<small class='badge badge-light border text-muted mt-1'><i class='fas fa-users mr-1'></i>+ $count Pendamping</small>";
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle text-center">
                                    <?php if ($row->status == 'selesai'): ?>
                                        <span class="badge badge-success px-3 py-2 shadow-sm" style="border-radius: 20px;"><i class="fas fa-flag-checkered mr-1"></i> Selesai</span>
                                    <?php elseif ($row->is_locked == 1): ?>
                                        <span class="badge badge-secondary px-3 py-2 shadow-sm" style="border-radius: 20px; background-color: #5a5c69;"><i class="fas fa-lock mr-1"></i> Locked</span>
                                    <?php else: ?>
                                        <span class="badge badge-info px-3 py-2 shadow-sm" style="border-radius: 20px; background-color: #36b9cc;"><i class="fas fa-pen mr-1"></i> Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center text-nowrap">
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('laporan/detail/' . $row->id_peminjaman) ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- [UPDATE] LOGIC BUTTON KHUSUS ADMIN -->
                                        <?php if ($this->session->userdata('role') == 'admin'): ?>

                                            <!-- Tombol Lock (Jika belum locked) -->
                                            <?php if ($row->is_locked == 0): ?>
                                                <a href="<?= base_url('laporan/lock_action/' . $row->id_peminjaman) ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin LOCK laporan ini?')"
                                                    title="Kunci Laporan"
                                                    style="background-color: #e74a3b;">
                                                    <i class="fas fa-lock"></i>
                                                </a>
                                            <?php endif; ?>

                                            <!-- [BARU] Tombol Hapus Laporan -->
                                            <a href="<?= base_url('laporan/hapus_laporan/' . $row->id_peminjaman) ?>"
                                                class="btn btn-sm btn-dark btn-delete"
                                                title="Hapus Laporan & Restore Stok">
                                                <i class="fas fa-trash"></i>
                                            </a>

                                        <?php endif; ?>

                                        <!-- LOGIC BUTTON KHUSUS OPERATOR -->
                                        <?php if ($this->session->userdata('role') == 'operator'): ?>
                                            <?php if ($row->is_locked == 0): ?>
                                                <a href="<?= base_url('laporan/detail/' . $row->id_peminjaman) ?>" class="btn btn-sm btn-warning text-white" title="Edit Data" style="background-color: #f6c23e;">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>