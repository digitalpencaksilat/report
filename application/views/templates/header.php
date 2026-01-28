<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Inventory Manager' ?></title>

    <!-- Favicon Website -->
    <link rel="shortcut icon" href="<?= base_url('assets/logo/logo.ico') ?>" type="image/x-icon">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">

    <!-- Google Fonts: Oswald & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* --- BRAND VARIABLES & TYPOGRAPHY --- */
        :root {
            --brand-primary: #C60000;
            /* Merah Silat */
            --brand-secondary: #FFD700;
            /* Emas */
            --brand-dark: #1a1a1a;
            /* Hitam Pekat */
            --brand-light: #f4f6f9;
            /* Abu Terang */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--brand-light);
            color: #333;
            overflow-x: hidden;
            /* Prevent horizontal scroll */
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .sidebar-brand {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }

        /* --- NAVBAR STYLING --- */
        .navbar-custom {
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 10px 1rem;
            border-bottom: 3px solid var(--brand-primary);
            z-index: 1030;
            /* Ensure navbar stays on top */
        }

        .navbar-brand {
            color: var(--brand-primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            min-height: 100vh;
            background: #ffffff;
            color: #333;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #eaeaea;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-header small {
            color: #888 !important;
        }

        .sidebar a {
            color: #555;
            padding: 15px 25px;
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .sidebar a:hover {
            background: #fff5f5;
            color: var(--brand-primary);
            border-left: 4px solid var(--brand-secondary);
            padding-left: 30px;
        }

        .sidebar a.active {
            background: var(--brand-primary);
            color: white;
            border-left: 4px solid var(--brand-secondary);
            box-shadow: 0 4px 15px rgba(198, 0, 0, 0.3);
        }

        .sidebar i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        /* --- MOBILE RESPONSIVE SIDEBAR (FLOATING) --- */
        @media (max-width: 768px) {

            /* Sidebar hidden by default on mobile */
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                /* Hide off-canvas */
                height: 100vh;
                width: 250px;
                z-index: 9999;
                overflow-y: auto;
                display: block !important;
                /* Override d-none */
            }

            /* Class to show sidebar */
            .sidebar.show {
                left: 0;
            }

            /* Overlay background when sidebar is active */
            #sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9998;
                top: 0;
                left: 0;
            }

            #sidebar-overlay.active {
                display: block;
            }
        }

        /* --- COMPONENTS CUSTOM --- */
        .main-content {
            padding: 30px;
            min-height: 100vh;
        }

        /* Card Custom */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: white;
            transition: transform 0.3s;
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--brand-light);
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.5px;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 20px;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            border: 2px solid var(--brand-primary);
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: transparent;
            color: var(--brand-primary);
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* --- DATATABLES CUSTOM --- */
        .table thead th {
            background-color: var(--brand-primary);
            color: white;
            border: none;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 400;
        }

        .page-item.active .page-link {
            background-color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: white !important;
        }

        .page-link {
            color: var(--brand-primary);
        }
    </style>
</head>

<body>

    <!-- Overlay untuk Mobile Sidebar -->
    <div id="sidebar-overlay"></div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand navbar-light navbar-custom sticky-top">

        <!-- [MOBILE ONLY] Tombol Hamburger untuk Sidebar -->
        <button class="btn btn-link text-dark d-md-none mr-3" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- [DESKTOP ONLY] Brand/Logo (Disembunyikan di Mobile) -->
        <a class="navbar-brand d-none d-md-flex" href="#">
            <img src="<?= base_url('assets/logo/logo.png') ?>" alt="Logo" style="height: 40px; margin-right: 10px;">
            REPORT MANAGEMENT
        </a>

        <!-- User Info & Logout (Selalu Muncul di Kanan) -->
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item text-dark mr-3 text-right">
                <small class="text-muted d-block" style="line-height: 1;">Login sebagai</small>
                <b style="color: var(--brand-primary); font-family: 'Oswald'; font-size: 1.1rem;">
                    <?= strtoupper($this->session->userdata('role')) ?>
                </b>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-danger" id="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span class="d-none d-sm-inline">Keluar</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar Navigation -->
            <div class="col-md-2 sidebar p-0 d-none d-md-block" id="accordionSidebar">
                <div class="sidebar-header d-flex align-items-center justify-content-between">
                    <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 2px;">Navigasi</small>
                    <!-- Tombol Close di dalam sidebar (Mobile Only) -->
                    <button class="btn btn-link d-md-none text-danger p-0" id="sidebarClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <?php $segment = $this->uri->segment(1); ?>

                <a href="<?= base_url('dashboard') ?>" class="<?= ($segment == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                <?php if ($this->session->userdata('role') == 'admin'): ?>
                    <a href="<?= base_url('barang') ?>" class="<?= ($segment == 'barang') ? 'active' : '' ?>">
                        <i class="fas fa-box-open"></i> Master Barang
                    </a>

                    <a href="<?= base_url('tracking') ?>" class="<?= ($segment == 'tracking') ? 'active' : '' ?>">
                        <i class="fas fa-map-marked-alt"></i> Tracking Barang
                    </a>

                    <!-- [BARU] Menu Riwayat Tugas Operator -->
                    <a href="<?= base_url('riwayat') ?>" class="<?= ($segment == 'riwayat') ? 'active' : '' ?>">
                        <i class="fas fa-user-clock"></i> Riwayat Tugas
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('laporan') ?>" class="<?= ($segment == 'laporan') ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Laporan Event
                </a>

                <?php if ($this->session->userdata('role') == 'admin'): ?>
                    <a href="<?= base_url('users') ?>" class="<?= ($segment == 'users') ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i> User Manager
                    </a>
                <?php endif; ?>

            </div>

            <!-- Main Content Start -->
            <div class="col-md-10 main-content">

                <!-- Script Sederhana untuk Toggle Sidebar Mobile -->
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const toggleBtn = document.getElementById('sidebarToggle');
                        const closeBtn = document.getElementById('sidebarClose');
                        const sidebar = document.getElementById('accordionSidebar');
                        const overlay = document.getElementById('sidebar-overlay');

                        // Fungsi Buka Sidebar
                        if (toggleBtn) {
                            toggleBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                sidebar.classList.add('show');
                                overlay.classList.add('active');
                            });
                        }

                        // Fungsi Tutup Sidebar (Tombol X)
                        if (closeBtn) {
                            closeBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                sidebar.classList.remove('show');
                                overlay.classList.remove('active');
                            });
                        }

                        // Fungsi Tutup Sidebar (Klik Overlay)
                        if (overlay) {
                            overlay.addEventListener('click', function() {
                                sidebar.classList.remove('show');
                                overlay.classList.remove('active');
                            });
                        }
                    });
                </script>