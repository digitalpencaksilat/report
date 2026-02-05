<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Manajemen Kasbon</h1>
        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalAjukan">
            <i class="fas fa-plus mr-1"></i> Input Kasbon Baru
        </button>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('warning') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Tgl Ajuan</th>
                            <th>Nama Karyawan</th>
                            <th class="text-right">Nominal Pinjam</th>
                            <th class="text-right">Sisa Tagihan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kasbon)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted font-italic">Belum ada data pengajuan kasbon.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kasbon as $k): ?>
                                <tr>
                                    <td class="align-middle"><?= date('d/m/Y', strtotime($k->tanggal_pengajuan)) ?></td>
                                    <td class="align-middle">
                                        <b><?= $k->nama_lengkap ?></b><br>
                                        <small class="text-muted"><?= $k->keterangan ?></small>
                                    </td>
                                    <td class="align-middle text-right text-primary font-weight-bold">
                                        Rp <?= number_format($k->nominal_pinjaman, 0, ',', '.') ?>
                                    </td>
                                    <td class="align-middle text-right text-danger font-weight-bold">
                                        Rp <?= number_format($k->sisa_tagihan, 0, ',', '.') ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($k->status == 'pending'): ?>
                                            <span class="badge badge-warning">Menunggu ACC</span>
                                        <?php elseif ($k->status == 'active'): ?>
                                            <span class="badge badge-primary">Aktif (Belum Lunas)</span>
                                        <?php elseif ($k->status == 'lunas'): ?>
                                            <span class="badge badge-success">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <!-- Tombol Detail (History Pembayaran) -->
                                        <a href="<?= base_url('bendahara/detail_kasbon/' . $k->id_kasbon) ?>" class="btn btn-sm btn-secondary mb-1" title="Lihat History Pembayaran">
                                            <i class="fas fa-history"></i>
                                        </a>

                                        <?php if ($k->status == 'pending'): ?>
                                            <!-- Tombol Approval -->
                                            <a href="<?= base_url('bendahara/aksi_kasbon/' . $k->id_kasbon . '/acc') ?>" class="btn btn-sm btn-success mb-1" onclick="return confirm('Setujui dan Cairkan Dana?')"><i class="fas fa-check"></i></a>
                                            <a href="<?= base_url('bendahara/aksi_kasbon/' . $k->id_kasbon . '/tolak') ?>" class="btn btn-sm btn-danger mb-1"><i class="fas fa-times"></i></a>

                                        <?php elseif ($k->status == 'active'): ?>
                                            <!-- Tombol Bayar Tunai -->
                                            <button class="btn btn-sm btn-info btn-bayar mb-1"
                                                data-id="<?= $k->id_kasbon ?>"
                                                data-nama="<?= $k->nama_lengkap ?>"
                                                data-sisa="<?= $k->sisa_tagihan ?>"
                                                data-toggle="modal" data-target="#modalBayar" title="Bayar Tunai">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal Ajukan (Manual Bendahara) -->
<div class="modal fade" id="modalAjukan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('bendahara/ajukan_kasbon') ?>" method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Input Kasbon Karyawan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <select name="id_user" class="form-control" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u->id_user ?>"><?= $u->nama_lengkap ?> (<?= ucfirst($u->role) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nominal Pinjaman</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Contoh: 500000" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Keperluan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bayar Tunai -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('bendahara/bayar_kasbon_tunai') ?>" method="post">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Bayar Kasbon (Tunai)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kasbon" id="bayar_id">
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <input type="text" id="bayar_nama" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Sisa Tagihan Saat Ini</label>
                        <input type="text" id="bayar_sisa_disp" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-success">Nominal Setor Tunai</label>
                        <input type="number" name="nominal_bayar" id="bayar_nominal" class="form-control font-weight-bold" required>
                        <small class="text-muted">Masukkan jumlah uang yang diterima dari karyawan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Proses Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.btn-bayar', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var sisa = $(this).data('sisa');

        $('#bayar_id').val(id);
        $('#bayar_nama').val(nama);
        $('#bayar_sisa_disp').val('Rp ' + new Intl.NumberFormat('id-ID').format(sisa));
        $('#bayar_nominal').attr('max', sisa); // Max bayar = sisa utang
    });
</script>