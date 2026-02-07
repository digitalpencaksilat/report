<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800" style="font-weight: 700;">Manajemen Kasbon</h1>
        <!-- Tombol Tambah (Merah) -->
        <button class="btn btn-sm btn-danger shadow-sm" data-toggle="modal" data-target="#modalAjukan">
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
        <!-- Header Merah -->
        <div class="card-header py-3 bg-danger">
            <h6 class="m-0 font-weight-bold text-white">Daftar Riwayat Kasbon</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
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
                                        <?php elseif ($k->status == 'rejected'): ?>
                                            <span class="badge badge-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <!-- Tombol Detail -->
                                        <a href="<?= base_url('bendahara/detail_kasbon/' . $k->id_kasbon) ?>" class="btn btn-sm btn-secondary mb-1" title="Lihat History Pembayaran">
                                            <i class="fas fa-history"></i>
                                        </a>

                                        <?php if ($k->status == 'pending'): ?>
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
            <!-- Action TETAP ajukan_kasbon -->
            <form action="<?= base_url('bendahara/ajukan_kasbon') ?>" method="post">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Input Kasbon Karyawan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Karyawan</label>
                        <select name="id_user" class="form-control" required>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u->id_user ?>"><?= $u->nama_lengkap ?> (<?= ucfirst($u->role) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Input Nominal dengan Format Rupiah -->
                    <div class="form-group">
                        <label class="font-weight-bold">Nominal Pinjaman</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" name="nominal" class="form-control rupiah-input font-weight-bold" placeholder="0" required>
                        </div>
                    </div>

                    <!-- [BARU] Input Tanggal Manual -->
                    <div class="form-group">
                        <label class="font-weight-bold text-danger">Tanggal Peminjaman (Fisik Uang Keluar)</label>
                        <input type="date" name="tanggal_pengajuan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        <small class="text-muted">Sesuaikan dengan tanggal asli uang diberikan.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan / Keperluan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bayar Tunai -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Action TETAP bayar_kasbon_tunai -->
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

                    <!-- [BARU] Input Tanggal Bayar Manual -->
                    <div class="form-group">
                        <label class="font-weight-bold text-info">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-success">Nominal Setor Tunai</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">Rp</span>
                            </div>
                            <input type="text" name="nominal_bayar" id="bayar_nominal" class="form-control rupiah-input font-weight-bold" required>
                        </div>
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
    $(document).ready(function() {
        // Script Format Rupiah
        $('.rupiah-input').on('keyup', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        // Script Modal Bayar
        $(document).on('click', '.btn-bayar', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var sisa = $(this).data('sisa');

            $('#bayar_id').val(id);
            $('#bayar_nama').val(nama);
            $('#bayar_sisa_disp').val('Rp ' + formatRupiah(sisa.toString()));
            // Kita tidak set max attribute secara strict karena format rupiah, validasi di backend saja
        });
    });
</script>