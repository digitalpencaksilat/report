<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: var(--brand-primary);">
                        <i class="fas fa-calendar-plus mr-2"></i>Buat Laporan Event Baru
                    </h6>
                </div>

                <div class="card-body">
                    <form action="<?= base_url('laporan/proses_tambah') ?>" method="post">

                        <!-- Petugas Utama (Wajib) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Petugas Utama</label>
                            <div class="col-sm-9">
                                <select name="id_operator" class="form-control" required>
                                    <option value="">-- Pilih Operator Utama --</option>
                                    <?php foreach ($list_operator as $op): ?>
                                        <option value="<?= $op->id_user ?>"><?= $op->nama_lengkap ?> (<?= $op->username ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Penanggung jawab utama laporan ini.</small>
                            </div>
                        </div>

                        <!-- Petugas Tambahan (Opsional & Dinamis) -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Petugas Tambahan</label>
                            <div class="col-sm-9">
                                <div id="container-petugas-tambahan">
                                    <!-- Input dinamis akan muncul di sini -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btn-tambah-petugas">
                                    <i class="fas fa-user-plus mr-1"></i> Tambah Petugas Lainnya
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted italic">* Petugas tambahan bersifat opsional jika personil lebih dari satu.</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Nama Event</label>
                            <div class="col-sm-9">
                                <input type="text" name="nama_event" class="form-control" placeholder="Contoh: O2SN Tingkat Kota 2024" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Lokasi</label>
                            <div class="col-sm-9">
                                <input type="text" name="lokasi_event" class="form-control" placeholder="Tempat pelaksanaan..." required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Tgl. Pemakaian</label>
                            <div class="col-sm-9">
                                <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                <small class="text-muted">Tanggal barang mulai diambil/dipakai.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Rencana Kembali</label>
                            <div class="col-sm-9">
                                <input type="date" name="tgl_kembali_rencana" class="form-control" required>
                                <small class="text-muted">Estimasi tanggal barang dikembalikan ke gudang.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold" style="color: #444;">Keterangan</label>
                            <div class="col-sm-9">
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan (Opsional)"></textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="<?= base_url('laporan') ?>" class="btn btn-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-brand px-4">
                                <i class="fas fa-save mr-1"></i> Simpan & Lanjut Pilih Barang
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Script Dinamis Petugas Tambahan -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnTambah = document.getElementById('btn-tambah-petugas');
        const container = document.getElementById('container-petugas-tambahan');

        btnTambah.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'd-flex mb-2 align-items-center animate__animated animate__fadeIn';

            div.innerHTML = `
            <select name="petugas_tambahan[]" class="form-control">
                <option value="">-- Pilih Petugas Tambahan --</option>
                <?php foreach ($list_operator as $op): ?>
                    <option value="<?= $op->id_user ?>"><?= $op->nama_lengkap ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-danger btn-sm ml-2 btn-hapus-petugas">
                <i class="fas fa-times"></i>
            </button>
        `;

            container.appendChild(div);

            // Fungsi Hapus Baris
            div.querySelector('.btn-hapus-petugas').addEventListener('click', function() {
                div.remove();
            });
        });
    });
</script>