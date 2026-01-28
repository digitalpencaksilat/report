</div> <!-- End Main Content Col -->
</div> <!-- End Row -->
</div> <!-- End Container Fluid -->

<!-- Core Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        $('#dataTable').DataTable({
            "language": {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "search": "Cari Data:",
                "zeroRecords": "Tidak ditemukan data yang sesuai"
            }
        });

        // 2. Auto Close Alert Biasa (Bootstrap)
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 4000);

        // 3. Style tambahan locking visual
        $('.locked-row').css('background-color', '#f8f9fa');
        $('.locked-row').css('color', '#6c757d');

        // --- SWEET ALERT LOGIC ---

        // A. Konfirmasi Logout
        $(document).on('click', '#btn-logout', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C60000',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // B. Global Delete Confirmation (Untuk Semua Tombol Hapus)
        // Cukup tambahkan class="btn-delete" pada tombol <a>
        // Opsional: tambahkan data-title="..." dan data-message="..." untuk pesan custom
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            const title = $(this).data('title') || 'Yakin ingin menghapus?';
            const message = $(this).data('message') || "Data yang dihapus tidak dapat dikembalikan.";

            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b', // Merah Danger
                cancelButtonColor: '#858796', // Abu Batal
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // C. Notifikasi Flashdata (Login/Logout/Success/Error)
        <?php if ($this->session->flashdata('swal_icon')): ?>
            Swal.fire({
                icon: '<?= $this->session->flashdata('swal_icon') ?>',
                title: '<?= $this->session->flashdata('swal_title') ?>',
                text: '<?= $this->session->flashdata('swal_text') ?>',
                showConfirmButton: false,
                timer: 2000
            });
        <?php endif; ?>
    });
</script>
</body>

</html>