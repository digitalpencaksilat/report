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
            --sidebar-width: 300px;
            --sidebar-mini-width: 80px;
            /* Lebar saat tidak di-hover */
            --navbar-height: 70px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--brand-light);
            color: #333;
            overflow-x: hidden;
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
            padding: 0 1rem;
            height: var(--navbar-height);
            border-bottom: 3px solid var(--brand-primary);
            z-index: 1030;
        }

        .navbar-brand {
            color: var(--brand-primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }

        /* --- SIDEBAR STYLING (HOVER EFFECT) --- */
        .sidebar-container {
            position: relative;
            /* Container untuk layout */
        }

        .sidebar {
            background: #ffffff;
            color: #333;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            border-right: 1px solid #eaeaea;
            transition: width 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
            /* Sembunyikan teks saat ciut */
            white-space: nowrap;
            /* Cegah teks turun baris */
            z-index: 1040;
            /* Di atas konten utama */
        }

        /* Desktop Sidebar Logic */
        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                top: var(--navbar-height);
                left: 0;
                height: calc(100vh - var(--navbar-height));
                width: var(--sidebar-mini-width);
                /* Default Lebar Kecil (80px) */
            }

            /* Saat di-HOVER, Melebar jadi 250px */
            .sidebar:hover {
                width: var(--sidebar-width);
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            }

            /* Logika Teks Menu */
            .sidebar .nav-text {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease-in-out;
                margin-left: 10px;
            }

            .sidebar:hover .nav-text {
                opacity: 1;
                visibility: visible;
                transition-delay: 0.1s;
                /* Delay sedikit agar smooth */
            }

            /* Main Content Margin */
            .main-content {
                margin-left: var(--sidebar-mini-width);
                /* Margin kiri tetap 80px */
                width: calc(100% - var(--sidebar-mini-width));
                padding: 30px;
                min-height: calc(100vh - var(--navbar-height));
                transition: margin-left 0.3s;
            }
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Teks Header Sidebar (Menu Navigasi) */
        .sidebar-header .header-text {
            display: none;
            font-size: 0.8rem;
            font-weight: bold;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-left: 10px;
        }

        .sidebar:hover .sidebar-header .header-text {
            display: block;
        }

        .sidebar-header i {
            font-size: 1.2rem;
            color: #aaa;
        }


        /* --- MENU LINKS STYLING --- */
        .sidebar a {
            color: #555;
            padding: 15px 0;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.2s;
            font-weight: 500;
            height: 55px;
            /* Tinggi fix per menu */
        }

        /* Ikon Menu */
        .sidebar a i {
            min-width: var(--sidebar-mini-width);
            /* Lebar area ikon fix 80px */
            text-align: center;
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background: #fff5f5;
            color: var(--brand-primary);
            border-left: 4px solid var(--brand-secondary);
        }

        .sidebar a.active {
            background: var(--brand-primary);
            color: white;
            border-left: 4px solid var(--brand-secondary);
        }

        /* Heading Kategori (Finance, Personil, dll) */
        .sidebar-heading {
            padding: 15px 0;
            text-align: center;
            font-size: 0.7rem;
            font-weight: bold;
            color: #aaa;
            text-transform: uppercase;
            border-top: 1px solid #f8f9fa;
        }

        /* Sembunyikan heading saat kecil, tampilkan saat hover */
        .sidebar .sidebar-heading span {
            display: none;
        }

        .sidebar:hover .sidebar-heading span {
            display: inline;
        }

        .sidebar:hover .sidebar-heading {
            text-align: left;
            padding-left: 25px;
        }


        /* --- MOBILE RESPONSIVE --- */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                height: 100vh;
                width: 260px;
                z-index: 9999;
                display: block;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar .nav-text {
                opacity: 1;
                visibility: visible;
                /* Di mobile selalu visible */
            }

            .sidebar-header .header-text {
                display: inline-block;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

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

        /* Card Custom */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background: white;
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--brand-light);
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.5px;
        }

        /* DataTables Fix */
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <!-- Overlay untuk Mobile Sidebar -->
    <div id="sidebar-overlay"></div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand navbar-light navbar-custom sticky-top">

        <!-- [MOBILE ONLY] Tombol Hamburger -->
        <button class="btn btn-link text-dark d-md-none mr-3" id="sidebarToggleMobile">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- Brand/Logo -->
        <a class="navbar-brand" href="#">
            <img src="<?= base_url('assets/logo/logo.png') ?>" alt="Logo" style="height: 40px; margin-right: 10px;">
            <span class="d-none d-sm-inline">REPORT MGMT</span>
        </a>

        <!-- User Info -->
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item text-dark mr-3 text-right">
                <small class="text-muted d-block" style="font-size: 0.7rem;">Login sebagai</small>
                <b style="color: var(--brand-primary); font-family: 'Oswald'; font-size: 1rem;">
                    <?= strtoupper($this->session->userdata('role')) ?>
                </b>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-sm btn-outline-danger" id="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <span class="d-none d-sm-inline ml-1">Keluar</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Wrapper Layout -->
    <div class="sidebar-container">

        <!-- Sidebar Navigation -->
        <!-- Tidak lagi menggunakan col-md-2, tapi custom CSS 'sidebar' -->
        <div class="sidebar" id="accordionSidebar">

            <!-- Header Sidebar (Ikon Menu) -->
            <div class="sidebar-header">
                <i class="fas fa-th-large"></i>
                <span class="header-text">NAVIGASI</span>
                <!-- Tombol Close (Mobile) -->
                <button class="btn btn-link d-md-none text-danger ml-auto p-0 mr-3" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <?php
            $segment1 = $this->uri->segment(1);
            $segment2 = $this->uri->segment(2);
            $role = $this->session->userdata('role');
            ?>

            <!-- MENU ITEMS -->
            <!-- Dashboard -->
            <?php if ($role == 'bendahara'): ?>
                <a href="<?= base_url('bendahara') ?>" class="<?= ($segment1 == 'bendahara' && ($segment2 == '' || $segment2 == 'index')) ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
                </a>
            <?php else: ?>
                <a href="<?= base_url('dashboard') ?>" class="<?= ($segment1 == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
                </a>
            <?php endif; ?>

            <!-- ADMIN MENU -->
            <?php if ($role == 'admin'): ?>
                <a href="<?= base_url('barang') ?>" class="<?= ($segment1 == 'barang') ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i> <span class="nav-text">Master Barang</span>
                </a>
                <a href="<?= base_url('tracking') ?>" class="<?= ($segment1 == 'tracking') ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i> <span class="nav-text">Tracking Barang</span>
                </a>
                <a href="<?= base_url('riwayat') ?>" class="<?= ($segment1 == 'riwayat') ? 'active' : '' ?>">
                    <i class="fas fa-user-clock"></i> <span class="nav-text">Riwayat Tugas</span>
                </a>
            <?php endif; ?>

            <!-- FINANCE MENU -->
            <?php if ($role == 'admin' || $role == 'bendahara'): ?>
                <!-- Heading -->
                <div class="sidebar-heading">
                    <i class="fas fa-ellipsis-h"></i> <span>FINANCE</span>
                </div>

                <a href="<?= base_url('bendahara/laporan') ?>" class="<?= ($segment1 == 'bendahara' && ($segment2 == 'laporan' || $segment2 == 'buat_laporan' || $segment2 == 'detail')) ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i> <span class="nav-text">Laporan Keuangan</span>
                </a>
                <a href="<?= base_url('bendahara/payroll') ?>" class="<?= ($segment1 == 'bendahara' && ($segment2 == 'payroll' || $segment2 == 'slip_gaji')) ? 'active' : '' ?>">
                    <i class="fas fa-money-check-alt"></i> <span class="nav-text">Payroll Operator</span>
                </a>
                <a href="<?= base_url('bendahara/kasbon') ?>" class="<?= ($segment1 == 'bendahara' && $segment2 == 'kasbon') ? 'active' : '' ?>">
                    <i class="fas fa-hand-holding-usd"></i> <span class="nav-text">Manajemen Kasbon</span>
                </a>
                <a href="<?= base_url('bendahara/buku_kas') ?>" class="<?= ($segment1 == 'bendahara' && $segment2 == 'buku_kas') ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> <span class="nav-text">Buku Kas Umum</span>
                </a>
                <a href="<?= base_url('bendahara/kategori') ?>" class="<?= ($segment1 == 'bendahara' && $segment2 == 'kategori') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> <span class="nav-text">Kategori Pengeluaran</span>
                </a>
            <?php endif; ?>

            <!-- OPERATOR MENU -->
            <?php if ($role == 'operator'): ?>
                <div class="sidebar-heading">
                    <i class="fas fa-ellipsis-h"></i> <span>PERSONIL</span>
                </div>
                <a href="<?= base_url('gaji') ?>" class="<?= ($segment1 == 'gaji') ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i> <span class="nav-text">Gaji Saya</span>
                </a>
            <?php endif; ?>

            <!-- OPERASIONAL (ADMIN & OPERATOR) -->
            <?php if ($role != 'bendahara'): ?>
                <div class="sidebar-heading">
                    <i class="fas fa-ellipsis-h"></i> <span>OPERASIONAL</span>
                </div>
                <a href="<?= base_url('laporan') ?>" class="<?= ($segment1 == 'laporan') ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> <span class="nav-text">Laporan Event</span>
                </a>
            <?php endif; ?>

            <!-- USER MANAGER -->
            <?php if ($role == 'admin'): ?>
                <div class="sidebar-heading">
                    <i class="fas fa-ellipsis-h"></i> <span>SYSTEM</span>
                </div>
                <a href="<?= base_url('users') ?>" class="<?= ($segment1 == 'users') ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> <span class="nav-text">User Manager</span>
                </a>
            <?php endif; ?>

        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content">

            <!-- Script JS untuk Mobile Sidebar -->
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const sidebar = document.getElementById('accordionSidebar');
                    const overlay = document.getElementById('sidebar-overlay');
                    const toggleMobile = document.getElementById('sidebarToggleMobile');
                    const closeBtn = document.getElementById('sidebarClose');

                    // Logic Mobile Only
                    if (toggleMobile) {
                        toggleMobile.addEventListener('click', function(e) {
                            e.preventDefault();
                            sidebar.classList.add('show');
                            overlay.classList.add('active');
                        });
                    }
                    if (closeBtn) {
                        closeBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            sidebar.classList.remove('show');
                            overlay.classList.remove('active');
                        });
                    }
                    if (overlay) {
                        overlay.addEventListener('click', function() {
                            sidebar.classList.remove('show');
                            overlay.classList.remove('active');
                        });
                    }
                });
            </script>