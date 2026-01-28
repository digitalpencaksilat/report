<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Report Management</title>

    <!-- Icon -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico'); ?>" type="image/x-icon">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* BRANDING COLORS */
        :root {
            --brand-primary: #C60000;
            --brand-dark: #1a1a1a;
            --brand-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Background pattern optional */
            background-image: linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: white;
            transition: transform 0.3s;
        }

        .card-header-custom {
            background-color: white;
            padding: 40px 30px 20px;
            text-align: center;
            border-bottom: none;
        }

        .logo-img {
            height: 80px;
            width: auto;
            margin-bottom: 15px;
            /* Drop shadow halus untuk logo */
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .app-title {
            color: var(--brand-primary);
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1.2;
        }

        .app-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .card-body-custom {
            padding: 30px 40px 50px;
        }

        .form-floating>.form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .form-floating>.form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 0.2rem rgba(198, 0, 0, 0.15);
        }

        .form-floating>label {
            color: #999;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            padding: 12px;
            border-radius: 50px;
            font-weight: 600;
            border: 2px solid var(--brand-primary);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Oswald', sans-serif;
            width: 100%;
            margin-top: 10px;
        }

        .btn-brand:hover {
            background-color: #a00000;
            border-color: #a00000;
            color: white;
            box-shadow: 0 5px 15px rgba(198, 0, 0, 0.3);
        }

        .back-link {
            text-decoration: none;
            color: #999;
            font-size: 0.85rem;
            display: block;
            text-align: center;
            margin-top: 20px;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--brand-primary);
        }

        .input-group-text {
            border-radius: 10px;
            background-color: transparent;
            border: 1px solid #e0e0e0;
            border-right: none;
            color: var(--brand-primary);
        }

        /* Fix border input group */
        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #e0e0e0;
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--brand-primary);
        }

        .input-group:focus-within .form-control {
            border-color: var(--brand-primary);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="card-header-custom">
                        <!-- Logo -->
                        <img src="<?= base_url('assets/logo/logo.png'); ?>" alt="Logo" class="logo-img">
                        <h4 class="app-title">REPORT MANAGEMENT</h4>
                        <p class="app-subtitle">Digital Pencak Silat Inventory</p>
                    </div>

                    <div class="card-body-custom">

                        <!-- Flashdata Error (Untuk jaga-jaga jika JS mati) -->
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger text-center small rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle me-1"></i> <?= $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/process'); ?>" method="POST" id="loginForm">

                            <div class="mb-4">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Username" required autofocus style="font-size: 0.95rem;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required style="font-size: 0.95rem;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand">
                                Masuk Dashboard <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">Akses terbatas hanya untuk Petugas & Admin</small>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 text-muted small">
                    &copy; <?= date('Y'); ?> Digital Pencak Silat. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Notifikasi GAGAL LOGIN
        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '<?= $this->session->flashdata('error'); ?>',
                confirmButtonColor: '#C60000',
                confirmButtonText: 'Coba Lagi'
            });
        <?php endif; ?>

        // 2. Notifikasi BERHASIL LOGOUT / SweetAlert Generic
        // (Menangkap flashdata swal_icon dari Auth Controller)
        <?php if ($this->session->flashdata('swal_icon')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: '<?= $this->session->flashdata('swal_icon') ?>',
                title: '<?= $this->session->flashdata('swal_title') ?>'
            });
        <?php endif; ?>
    </script>

</body>

</html>