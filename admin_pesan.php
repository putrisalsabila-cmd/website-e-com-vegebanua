<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

/* =========================
   DATA PESAN
========================= */

$query_pesan = mysqli_query(
    $conn,
    "SELECT *
     FROM messages
     ORDER BY created_at DESC"
);

if (!$query_pesan) {
    die("Query pesan gagal: " . mysqli_error($conn));
}

$total_pesan = mysqli_num_rows($query_pesan);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pesan Masuk - VegeBanua Mart</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
           PAGE HEADER
        ========================= */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .page-header p {
            margin: 5px 0 0;
            color: #888;
            font-size: 12px;
        }

        .message-count {
            background: #e9f8ef;
            color: #00a94f;
            padding: 9px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        /* =========================
           MESSAGE CARD
        ========================= */

        .message-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
        }

        .message-table-wrapper {
            overflow-x: auto;
        }

        .message-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .message-table th {
            padding: 13px 10px;
            background: #f8faf9;
            color: #777;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .message-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            vertical-align: top;
        }

        .message-table tr:last-child td {
            border-bottom: none;
        }

        .sender-name {
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .sender-email {
            color: #888;
            font-size: 10px;
        }

        .phone-number {
            color: #555;
            font-size: 11px;
        }

        .message-content {
            max-width: 350px;
            line-height: 1.6;
            color: #555;
        }

        .message-date {
            color: #888;
            font-size: 10px;
            white-space: nowrap;
        }

        .message-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #e9f8ef;
            color: #00a94f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty-message {
            text-align: center;
            padding: 70px 20px;
            color: #999;
        }

        .empty-message i {
            font-size: 45px;
            margin-bottom: 15px;
            color: #ccc;
        }

        .empty-message h4 {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .empty-message p {
            font-size: 12px;
            margin: 0;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .sidebar-logo h3,
            .sidebar-logo span,
            .menu-title,
            .sidebar-menu a span,
            .sidebar-bottom span {
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

            .page-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }

            .topbar {
                padding: 13px 15px;
            }

            .admin-profile > div:last-child {
                display: none;
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


    <p class="menu-title">
        Menu Utama
    </p>


    <ul class="sidebar-menu">

        <li>
            <a href="admin.php">
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
            </a>
        </li>

        <li>
            <a href="admin_pesan.php" class="active">
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


    <div class="sidebar-bottom">

        <a href="logout.php">

            <i class="fa fa-sign-out"></i>

            <span>Keluar</span>

        </a>

    </div>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main-content">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="topbar-title">

            <h4>
                Pesan Masuk
            </h4>

            <p>
                Lihat pesan yang dikirim melalui halaman Hubungi Kami
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

                <span>
                    Administrator
                </span>

            </div>

        </div>

    </div>


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h2>
                Pesan dari Pengunjung
            </h2>

            <p>
                Semua pesan yang dikirim melalui form Hubungi Kami.
            </p>

        </div>

        <div class="message-count">

            <i class="fa fa-envelope"></i>

            <?= $total_pesan; ?> Pesan

        </div>

    </div>


    <!-- MESSAGE TABLE -->

    <div class="message-card">

        <div class="message-table-wrapper">

            <?php if ($total_pesan > 0): ?>

                <table class="message-table">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Pengirim</th>

                            <th>Telepon</th>

                            <th>Pesan</th>

                            <th>Tanggal</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php
                    $no = 1;

                    while ($pesan = mysqli_fetch_assoc($query_pesan)):
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <td>

                                <div style="
                                    display:flex;
                                    align-items:flex-start;
                                    gap:10px;
                                ">

                                    <div class="message-icon">
                                        <i class="fa fa-user"></i>
                                    </div>

                                    <div>

                                        <div class="sender-name">
                                            <?= htmlspecialchars($pesan["nama"]); ?>
                                        </div>

                                        <div class="sender-email">
                                            <?= htmlspecialchars($pesan["email"]); ?>
                                        </div>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="phone-number">

                                    <i class="fa fa-phone"></i>
                                    <?= htmlspecialchars($pesan["telepon"]); ?>

                                </span>

                            </td>


                            <td>

                                <div class="message-content">

                                    <?= nl2br(
                                        htmlspecialchars($pesan["pesan"])
                                    ); ?>

                                </div>

                            </td>


                            <td>

                                <div class="message-date">

                                    <i class="fa fa-clock-o"></i>

                                    <?= date(
                                        "d/m/Y H:i",
                                        strtotime($pesan["created_at"])
                                    ); ?>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty-message">

                    <i class="fa fa-envelope-o"></i>

                    <h4>
                        Belum Ada Pesan
                    </h4>

                    <p>
                        Pesan dari pengunjung akan muncul di sini.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>