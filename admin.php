<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

/* =========================
   DATA DASHBOARD
========================= */

// Total produk
$query_produk = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);
$data_produk = mysqli_fetch_assoc($query_produk);

// Total pesanan
$query_pesanan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);
$data_pesanan = mysqli_fetch_assoc($query_pesanan);

// Total pengguna
$query_user = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);
$data_user = mysqli_fetch_assoc($query_user);

// Pesanan menunggu
$query_menunggu = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders
     WHERE status = 'Menunggu'"
);
$data_menunggu = mysqli_fetch_assoc($query_menunggu);

// Pesanan terbaru
$query_terbaru = mysqli_query(
    $conn,
    "SELECT 
        orders.id,
        orders.tanggal,
        orders.total,
        orders.status,
        users.nama
     FROM orders
     INNER JOIN users ON orders.user_id = users.id
     ORDER BY orders.tanggal DESC
     LIMIT 5"
);

// Produk stok menipis
$query_stok = mysqli_query(
    $conn,
    "SELECT id, nama_produk, stok, harga
     FROM products
     WHERE stok <= 10
     ORDER BY stok ASC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - VegeBanua Mart</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f6;
            color: #333;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(
                180deg,
                #00a94f 0%,
                #008f45 100%
            );
            padding: 25px 15px;
            color: white;
            z-index: 1000;
            box-shadow: 3px 0 15px rgba(0,0,0,0.08);
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar-logo .logo-icon {
            width: 55px;
            height: 55px;
            margin: auto;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .sidebar-logo h3 {
            font-size: 19px;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-logo span {
            font-size: 11px;
            opacity: 0.8;
        }

        .menu-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.65;
            margin: 0 12px 10px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: white;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 13px;
            transition: 0.2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.16);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-menu .badge {
            margin-left: auto;
            background: #ff5252;
            color: white;
            font-size: 10px;
            padding: 4px 7px;
            border-radius: 20px;
        }

        .sidebar-bottom {
            position: absolute;
            left: 15px;
            right: 15px;
            bottom: 20px;
        }

        .sidebar-bottom a {
            display: flex;
            align-items: center;
            gap: 13px;
            color: white;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 13px;
        }

        .sidebar-bottom a:hover {
            background: rgba(255,255,255,0.16);
        }

        /* =========================
           MAIN
        ========================= */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding: 25px 30px;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
        }

        .topbar-title h4 {
            margin: 0;
            font-weight: 700;
            color: #333;
        }

        .topbar-title p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #888;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9f9ef;
            color: #00a94f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .admin-profile strong {
            display: block;
            font-size: 13px;
        }

        .admin-profile span {
            display: block;
            font-size: 10px;
            color: #999;
        }

        /* =========================
           WELCOME
        ========================= */

        .welcome-box {
            background: linear-gradient(
                135deg,
                #00a94f,
                #36bd70
            );
            color: white;
            border-radius: 15px;
            padding: 25px 28px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .welcome-box::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            right: -40px;
            top: -70px;
        }

        .welcome-box h2 {
            margin: 0 0 7px;
            font-size: 23px;
            font-weight: 700;
        }

        .welcome-box p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        /* =========================
           STAT CARDS
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .icon-green {
            background: #e8f8ee;
            color: #00a94f;
        }

        .icon-blue {
            background: #eaf3ff;
            color: #4385e5;
        }

        .icon-purple {
            background: #f1ebff;
            color: #8057d6;
        }

        .icon-orange {
            background: #fff3df;
            color: #e99a27;
        }

        .stat-info small {
            display: block;
            color: #999;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .stat-info strong {
            font-size: 23px;
            color: #333;
        }

        /* =========================
           CONTENT GRID
        ========================= */

        .content-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }

        .card-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .card-heading h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .card-heading a {
            font-size: 11px;
            color: #00a94f;
            text-decoration: none;
            font-weight: 600;
        }

        /* =========================
           TABLE PESANAN
        ========================= */

        .order-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-table th {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .order-table td {
            padding: 13px 8px;
            border-bottom: 1px solid #f1f1f1;
            font-size: 12px;
            vertical-align: middle;
        }

        .order-table tr:last-child td {
            border-bottom: none;
        }

        .order-id {
            font-weight: 700;
            color: #00a94f;
        }

        .order-name {
            font-weight: 600;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 600;
        }

        .status-menunggu {
            background: #fff3df;
            color: #d88916;
        }

        .status-diproses {
            background: #eaf3ff;
            color: #3977d5;
        }

        .status-selesai {
            background: #e8f8ee;
            color: #00a94f;
        }

        .status-dibatalkan {
            background: #ffeaea;
            color: #d9534f;
        }

        /* =========================
           STOK MENIPIS
        ========================= */

        .stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .stock-item:last-child {
            border-bottom: none;
        }

        .stock-name {
            font-size: 12px;
            font-weight: 600;
        }

        .stock-price {
            font-size: 10px;
            color: #999;
            margin-top: 3px;
        }

        .stock-number {
            min-width: 48px;
            text-align: center;
            padding: 5px 8px;
            border-radius: 20px;
            background: #fff0f0;
            color: #d9534f;
            font-size: 10px;
            font-weight: 700;
        }

        .stock-safe {
            background: #fff5df;
            color: #db921c;
        }

        .empty-data {
            text-align: center;
            padding: 25px 10px;
            color: #aaa;
            font-size: 12px;
        }

        /* =========================
           QUICK MENU
        ========================= */

        .quick-menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .quick-item {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #333;
            padding: 18px 10px;
            border: 1px solid #eee;
            border-radius: 11px;
            transition: 0.2s;
        }

        .quick-item:hover {
            border-color: #bce8ce;
            background: #f7fffa;
            text-decoration: none;
            color: #00a94f;
            transform: translateY(-2px);
        }

        .quick-item i {
            display: block;
            font-size: 22px;
            color: #00a94f;
            margin-bottom: 8px;
        }

        .quick-item span {
            font-size: 11px;
            font-weight: 600;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .sidebar-logo h3,
            .sidebar-logo span,
            .menu-title,
            .sidebar-menu a span,
            .sidebar-bottom span,
            .sidebar-menu .badge {
                display: none;
            }

            .sidebar-logo {
                margin-bottom: 25px;
            }

            .sidebar-logo .logo-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .sidebar-menu a,
            .sidebar-bottom a {
                justify-content: center;
                padding: 12px;
            }

            .sidebar-menu a i,
            .sidebar-bottom a i {
                width: auto;
            }

            .sidebar-bottom {
                left: 10px;
                right: 10px;
            }

            .main-content {
                margin-left: 70px;
                padding: 15px;
            }

            .topbar {
                padding: 13px 15px;
            }

            .topbar-title h4 {
                font-size: 16px;
            }

            .topbar-title p {
                display: none;
            }

            .admin-profile > div:last-child {
                display: none;
            }

            .welcome-box {
                padding: 20px;
            }

            .welcome-box h2 {
                font-size: 19px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-menu {
                grid-template-columns: 1fr;
            }

            .order-table {
                min-width: 600px;
            }

            .dashboard-card {
                overflow-x: auto;
            }

        }

    </style>
</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="sidebar-logo">

        <div class="logo-icon">
            <i class="fa fa-leaf"></i>
        </div>

        <h3>VegeBanua</h3>
        <span>Admin Panel</span>

    </div>

    <p class="menu-title">Menu Utama</p>

    <ul class="sidebar-menu">

    <li>
        <a href="admin.php" class="active">
            <i class="fa fa-dashboard"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li>
        <a href="admin_produk.php">
            <i class="fa fa-shopping-basket"></i>
            <span>Kelola Produk</span>
        </a>
    </li>

    <li>
        <a href="admin_pesanan.php">
            <i class="fa fa-file-text-o"></i>
            <span>Kelola Pesanan</span>

            <?php if ($data_menunggu["total"] > 0): ?>
                <span class="badge">
                    <?= $data_menunggu["total"]; ?>
                </span>
            <?php endif; ?>

        </a>
    </li>

    <li>
        <a href="admin_pesan.php">
            <i class="fa fa-envelope"></i>
            <span>Pesan Masuk</span>
        </a>
    </li>

    <li>
        <a href="index.php" target="_blank">
            <i class="fa fa-globe"></i>
            <span>Lihat Website</span>
        </a>
    </li>

</ul>

    </ul>

    <div class="sidebar-bottom">

        <a href="logout.php">
            <i class="fa fa-sign-out"></i>
            <span>Keluar</span>
        </a>

    </div>

</aside>


<!-- =========================
     MAIN CONTENT
========================= -->

<main class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <div class="topbar-title">

            <h4>Dashboard Admin</h4>

            <p>
                Kelola VegeBanua Mart dengan mudah
            </p>

        </div>

        <div class="admin-profile">

            <div class="admin-avatar">
                <i class="fa fa-user"></i>
            </div>

            <div>

                <strong>
                    <?= htmlspecialchars($_SESSION["nama"]); ?>
                </strong>

                <span>Administrator</span>

            </div>

        </div>

    </div>


    <!-- WELCOME -->

    <div class="welcome-box">

        <h2>
            Halo, <?= htmlspecialchars($_SESSION["nama"]); ?> 👋
        </h2>

        <p>
            Selamat datang di dashboard admin VegeBanua Mart.
            Pantau produk dan pesanan dari sini.
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-icon icon-green">
                <i class="fa fa-shopping-basket"></i>
            </div>

            <div class="stat-info">

                <small>Total Produk</small>

                <strong>
                    <?= $data_produk["total"]; ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon icon-blue">
                <i class="fa fa-file-text-o"></i>
            </div>

            <div class="stat-info">

                <small>Total Pesanan</small>

                <strong>
                    <?= $data_pesanan["total"]; ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon icon-purple">
                <i class="fa fa-users"></i>
            </div>

            <div class="stat-info">

                <small>Total Pengguna</small>

                <strong>
                    <?= $data_user["total"]; ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon icon-orange">
                <i class="fa fa-clock-o"></i>
            </div>

            <div class="stat-info">

                <small>Menunggu</small>

                <strong>
                    <?= $data_menunggu["total"]; ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="content-grid">

        <!-- PESANAN TERBARU -->

        <div class="dashboard-card">

            <div class="card-heading">

                <h5>
                    <i class="fa fa-shopping-cart"></i>
                    Pesanan Terbaru
                </h5>

                <a href="admin_pesanan.php">
                    Lihat Semua
                    <i class="fa fa-arrow-right"></i>
                </a>

            </div>


            <?php if (mysqli_num_rows($query_terbaru) > 0): ?>

                <table class="order-table">

                    <thead>

                        <tr>

                            <th>Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while ($order = mysqli_fetch_assoc($query_terbaru)): ?>

                        <?php

                        $status_class = "";

                        if ($order["status"] == "Menunggu") {
                            $status_class = "status-menunggu";
                        } elseif ($order["status"] == "Diproses") {
                            $status_class = "status-diproses";
                        } elseif ($order["status"] == "Selesai") {
                            $status_class = "status-selesai";
                        } elseif ($order["status"] == "Dibatalkan") {
                            $status_class = "status-dibatalkan";
                        }

                        ?>

                        <tr>

                            <td>

                                <span class="order-id">
                                    #<?= $order["id"]; ?>
                                </span>

                                <br>

                                <small style="color:#999;">
                                    <?= date("d/m/Y", strtotime($order["tanggal"])); ?>
                                </small>

                            </td>

                            <td>

                                <span class="order-name">
                                    <?= htmlspecialchars($order["nama"]); ?>
                                </span>

                            </td>

                            <td>

                                Rp <?= number_format(
                                    $order["total"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </td>

                            <td>

                                <span class="status <?= $status_class; ?>">
                                    <?= htmlspecialchars($order["status"]); ?>
                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty-data">

                    <i
                        class="fa fa-shopping-cart"
                        style="font-size:25px; margin-bottom:10px;"
                    ></i>

                    <br>

                    Belum ada pesanan.

                </div>

            <?php endif; ?>

        </div>


        <!-- STOK MENIPIS -->

        <div class="dashboard-card">

            <div class="card-heading">

                <h5>
                    <i class="fa fa-exclamation-triangle"></i>
                    Stok Menipis
                </h5>

                <a href="admin_produk.php">
                    Kelola Produk
                </a>

            </div>


            <?php if (mysqli_num_rows($query_stok) > 0): ?>

                <?php while ($produk = mysqli_fetch_assoc($query_stok)): ?>

                    <div class="stock-item">

                        <div>

                            <div class="stock-name">
                                <?= htmlspecialchars(
                                    $produk["nama_produk"]
                                ); ?>
                            </div>

                            <div class="stock-price">
                                Rp <?= number_format(
                                    $produk["harga"],
                                    0,
                                    ",",
                                    "."
                                ); ?>
                            </div>

                        </div>

                        <div class="stock-number
                            <?= $produk["stok"] > 5 ? "stock-safe" : ""; ?>">

                            <?= $produk["stok"]; ?> stok

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-data">

                    <i
                        class="fa fa-check-circle"
                        style="font-size:25px; margin-bottom:10px;"
                    ></i>

                    <br>

                    Semua stok masih aman.

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- QUICK MENU -->

    <div class="dashboard-card">

        <div class="card-heading">

            <h5>
                Menu Cepat
            </h5>

        </div>

        <div class="quick-menu">

            <a
                href="admin_produk.php"
                class="quick-item"
            >

                <i class="fa fa-plus-circle"></i>

                <span>
                    Tambah / Kelola Produk
                </span>

            </a>


            <li>
    <a href="admin_pesanan.php">
        <i class="fa fa-file-text-o"></i>
        <span>Kelola Pesanan</span>

        <?php if ($data_menunggu["total"] > 0): ?>
            <span class="badge">
                <?= $data_menunggu["total"]; ?>
            </span>
        <?php endif; ?>

    </a>
</li>

<li>
    <a href="admin_pesan.php">
        <i class="fa fa-envelope"></i>
        <span>Pesan Masuk</span>
    </a>
</li>

<li>
    <a href="index.php" target="_blank">
        <i class="fa fa-globe"></i>
        <span>Lihat Website</span>
    </a>
</li>

            </a>

        </div>

    </div>

</main>

</body>
</html>