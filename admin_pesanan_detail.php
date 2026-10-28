<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: admin_pesanan.php");
    exit;
}

$order_id = (int) $_GET["id"];

/* AMBIL DATA PESANAN + PEMBELI */
$stmt = mysqli_prepare(
    $conn,
    "SELECT 
        orders.id,
        orders.tanggal,
        orders.total,
        orders.status,
        users.nama,
        users.email
     FROM orders
     INNER JOIN users ON orders.user_id = users.id
     WHERE orders.id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);

$result_order = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result_order);

mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: admin_pesanan.php");
    exit;
}

/* AMBIL DETAIL PRODUK */
$stmt_detail = mysqli_prepare(
    $conn,
    "SELECT
        order_details.product_id,
        order_details.jumlah,
        order_details.harga,
        order_details.subtotal,
        products.nama_produk,
        products.gambar
     FROM order_details
     INNER JOIN products ON order_details.product_id = products.id
     WHERE order_details.order_id = ?"
);

mysqli_stmt_bind_param($stmt_detail, "i", $order_id);
mysqli_stmt_execute($stmt_detail);

$result_detail = mysqli_stmt_get_result($stmt_detail);

mysqli_stmt_close($stmt_detail);


/* PROSES UBAH STATUS */
$pesan = "";
$tipe_pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ubah_status"])) {

    $status_baru = $_POST["status"];

    $status_valid = [
        "Menunggu",
        "Diproses",
        "Selesai",
        "Dibatalkan"
    ];

    if (!in_array($status_baru, $status_valid)) {

        $pesan = "Status tidak valid.";
        $tipe_pesan = "danger";

    } else {

        $status_lama = $order["status"];

        /* PESANAN SELESAI / DIBATALKAN TIDAK BISA DIUBAH LAGI */
        if ($status_lama == "Selesai" || $status_lama == "Dibatalkan") {

            $pesan = "Pesanan dengan status " . $status_lama . " tidak dapat diubah lagi.";
            $tipe_pesan = "warning";

        }

        /* STATUS SAMA */
        elseif ($status_lama == $status_baru) {

            $pesan = "Status pesanan sudah " . $status_lama . ".";
            $tipe_pesan = "warning";

        }

        /* PROSES PERUBAHAN STATUS */
        else {

            mysqli_begin_transaction($conn);

            try {

                /*
                 * Jika pesanan dibatalkan,
                 * stok dikembalikan.
                 */
                if ($status_baru == "Dibatalkan") {

                    $stmt_items = mysqli_prepare(
                        $conn,
                        "SELECT product_id, jumlah
                         FROM order_details
                         WHERE order_id = ?"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_items,
                        "i",
                        $order_id
                    );

                    mysqli_stmt_execute($stmt_items);

                    $items = mysqli_stmt_get_result($stmt_items);

                    while ($item = mysqli_fetch_assoc($items)) {

                        $update_stok = mysqli_prepare(
                            $conn,
                            "UPDATE products
                             SET stok = stok + ?
                             WHERE id = ?"
                        );

                        mysqli_stmt_bind_param(
                            $update_stok,
                            "ii",
                            $item["jumlah"],
                            $item["product_id"]
                        );

                        mysqli_stmt_execute($update_stok);
                        mysqli_stmt_close($update_stok);
                    }

                    mysqli_stmt_close($stmt_items);
                }


                /* UPDATE STATUS */
                $stmt_update = mysqli_prepare(
                    $conn,
                    "UPDATE orders
                     SET status = ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt_update,
                    "si",
                    $status_baru,
                    $order_id
                );

                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);


                mysqli_commit($conn);

                header(
                    "Location: admin_pesanan_detail.php?id=" .
                    $order_id .
                    "&success=1"
                );

                exit;

            } catch (Exception $e) {

                mysqli_rollback($conn);

                $pesan = "Gagal mengubah status pesanan.";
                $tipe_pesan = "danger";
            }
        }
    }
}


/* PESAN BERHASIL */
if (isset($_GET["success"])) {
    $pesan = "Status pesanan berhasil diperbarui.";
    $tipe_pesan = "success";
}


/* CLASS STATUS */
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

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Pesanan #<?= $order["id"]; ?> - VegeBanua Mart</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #00a94f, #087f3e);
            color: white;
            padding: 25px 18px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-logo {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar-logo span {
            display: block;
            font-size: 12px;
            font-weight: normal;
            opacity: 0.8;
            margin-top: 5px;
        }

        .menu-title {
            font-size: 11px;
            text-transform: uppercase;
            opacity: 0.7;
            margin: 20px 10px 10px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 6px;
            font-size: 14px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.18);
        }

        .sidebar a i {
            width: 25px;
            font-size: 16px;
        }

        .logout-menu {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 15px;
        }

        /* MAIN */

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        /* TOPBAR */

        .topbar {
            height: 75px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .page-title {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e8f8ef;
            color: #00a94f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .admin-name {
            font-size: 13px;
            font-weight: bold;
        }

        .admin-role {
            font-size: 11px;
            color: #888;
        }

        /* CONTENT */

        .content {
            padding: 35px;
        }

        .back-btn {
            display: inline-block;
            color: #00a94f;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            color: #078b44;
            text-decoration: none;
        }

        .back-btn i {
            margin-right: 6px;
        }

        /* HEADER CARD */

        .order-header {
            background: linear-gradient(135deg, #00a94f, #18bd66);
            color: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,169,79,0.15);
        }

        .order-header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-number {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .order-date {
            font-size: 13px;
            opacity: 0.9;
        }

        /* STATUS */

        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-menunggu {
            background: #fff4d6;
            color: #b77900;
        }

        .status-diproses {
            background: #e4f1ff;
            color: #2878c8;
        }

        .status-selesai {
            background: #e4f8ec;
            color: #168548;
        }

        .status-dibatalkan {
            background: #ffe7e7;
            color: #d33a3a;
        }

        /* GRID */

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .card-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.06);
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .customer-icon {
            width: 50px;
            height: 50px;
            background: #e8f8ef;
            color: #00a94f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .customer-name {
            font-weight: bold;
            font-size: 15px;
        }

        .customer-email {
            color: #888;
            font-size: 12px;
            margin-top: 4px;
        }

        /* STATUS FORM */

        .status-form select {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 11px 12px;
            font-size: 13px;
            margin-bottom: 12px;
            outline: none;
        }

        .status-form select:focus {
            border-color: #00a94f;
        }

        .update-btn {
            width: 100%;
            border: none;
            background: #00a94f;
            color: white;
            border-radius: 8px;
            padding: 11px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #078b44;
        }

        .locked-message {
            background: #f7f7f7;
            border-radius: 8px;
            padding: 12px;
            color: #888;
            font-size: 12px;
        }

        /* PRODUCT TABLE */

        .product-table {
            width: 100%;
        }

        .product-table th {
            border-top: none;
            border-bottom: 2px solid #eee;
            color: #777;
            font-size: 12px;
            text-transform: uppercase;
            padding: 12px 8px;
        }

        .product-table td {
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            padding: 15px 8px;
            font-size: 13px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            background: #f5f5f5;
        }

        .product-name {
            font-weight: bold;
        }

        .product-price {
            color: #777;
            font-size: 12px;
            margin-top: 3px;
        }

        .subtotal {
            font-weight: bold;
            color: #00a94f;
        }

        .total-box {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 25px;
            padding-top: 20px;
        }

        .total-label {
            color: #777;
            font-size: 14px;
        }

        .total-value {
            font-size: 21px;
            font-weight: bold;
            color: #00a94f;
        }

        /* ALERT */

        .alert {
            border: none;
            border-radius: 10px;
            font-size: 13px;
        }

        /* RESPONSIVE */

        @media (max-width: 991px) {

            .sidebar {
                width: 210px;
            }

            .main-content {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }

        }

        @media (max-width: 767px) {

            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .admin-wrapper {
                display: block;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                height: auto;
                padding: 18px;
            }

            .content {
                padding: 15px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .order-header-top {
                display: block;
            }

            .order-header .status-badge {
                margin-top: 15px;
            }

            .card-box {
                overflow-x: auto;
            }

            .product-table {
                min-width: 650px;
            }

        }

    </style>

</head>

<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-logo">
            VegeBanua Mart
            <span>Admin Panel</span>
        </div>

        <div class="menu-title">
            Menu Utama
        </div>

        <a href="admin.php">
            <i class="fa fa-dashboard"></i>
            Dashboard
        </a>

        <a href="admin_produk.php">
            <i class="fa fa-leaf"></i>
            Kelola Produk
        </a>

        <a href="admin_pesanan.php" class="active">
            <i class="fa fa-shopping-bag"></i>
            Kelola Pesanan
        </a>

        <div class="menu-title">
            Website
        </div>

        <a href="index.php" target="_blank">
            <i class="fa fa-globe"></i>
            Lihat Website
        </a>

        <div class="logout-menu">

            <a href="logout.php">
                <i class="fa fa-sign-out"></i>
                Keluar
            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="main-content">

        <!-- TOPBAR -->

        <div class="topbar">


            <h3 class="page-title">
                Detail Pesanan
            </h3>

            <div class="admin-profile">

                <div class="admin-icon">
                    <i class="fa fa-user"></i>
                </div>

                <div>

                    <div class="admin-name">
                        <?= htmlspecialchars($_SESSION["nama"]); ?>
                    </div>

                    <div class="admin-role">
                        Administrator
                    </div>

                </div>

            </div>

        </div>


        <!-- CONTENT -->

        <div class="content">

            <a href="admin_pesanan.php" class="back-btn">
                <i class="fa fa-arrow-left"></i>
                Kembali ke Daftar Pesanan
            </a>


            <?php if ($pesan != ""): ?>

                <div class="alert alert-<?= $tipe_pesan; ?>">
                    <?= htmlspecialchars($pesan); ?>
                </div>

            <?php endif; ?>


            <!-- ORDER HEADER -->

            <div class="order-header">

                <div class="order-header-top">

                    <div>

                        <div class="order-number">
                            Pesanan #<?= $order["id"]; ?>
                        </div>

                        <div class="order-date">
                            <i class="fa fa-calendar"></i>
                            <?= date("d M Y, H:i", strtotime($order["tanggal"])); ?>
                        </div>

                    </div>

                    <div>

                        <span class="status-badge <?= $status_class; ?>">
                            <?= htmlspecialchars($order["status"]); ?>
                        </span>

                    </div>

                </div>

            </div>


            <!-- INFO -->

            <div class="info-grid">

                <!-- PEMBELI -->

                <div class="card-box">

                    <div class="card-title">
                        <i class="fa fa-user"></i>
                        Informasi Pembeli
                    </div>

                    <div class="customer-info">

                        <div class="customer-icon">
                            <i class="fa fa-user"></i>
                        </div>

                        <div>

                            <div class="customer-name">
                                <?= htmlspecialchars($order["nama"]); ?>
                            </div>

                            <div class="customer-email">
                                <?= htmlspecialchars($order["email"]); ?>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- STATUS -->

                <div class="card-box">

                    <div class="card-title">
                        <i class="fa fa-refresh"></i>
                        Kelola Status
                    </div>

                    <?php if ($order["status"] != "Selesai" && $order["status"] != "Dibatalkan"): ?>

                        <form method="POST" class="status-form">

                            <select name="status" required>

                                <option value="">
                                    Pilih status baru
                                </option>

                                <?php if ($order["status"] == "Menunggu"): ?>

                                    <option value="Diproses">
                                        Diproses
                                    </option>

                                    <option value="Dibatalkan">
                                        Dibatalkan
                                    </option>

                                <?php elseif ($order["status"] == "Diproses"): ?>

                                    <option value="Selesai">
                                        Selesai
                                    </option>

                                    <option value="Dibatalkan">
                                        Dibatalkan
                                    </option>

                                <?php endif; ?>

                            </select>

                            <button
                                type="submit"
                                name="ubah_status"
                                class="update-btn"
                                onclick="return confirm('Yakin ingin mengubah status pesanan ini?');">

                                <i class="fa fa-check"></i>
                                Perbarui Status

                            </button>

                        </form>

                    <?php else: ?>

                        <div class="locked-message">

                            <i class="fa fa-lock"></i>

                            Status pesanan sudah
                            <strong><?= htmlspecialchars($order["status"]); ?></strong>
                            dan tidak dapat diubah lagi.

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- DETAIL PRODUK -->

            <div class="card-box">

                <div class="card-title">
                    <i class="fa fa-shopping-cart"></i>
                    Produk yang Dipesan
                </div>


                <div class="table-responsive">

                    <table class="product-table">

                        <thead>

                            <tr>

                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while ($item = mysqli_fetch_assoc($result_detail)): ?>

                            <tr>

                                <td>

                                    <div class="product-info">

                                        <?php if (!empty($item["gambar"])): ?>

                                            <img
                                                src="images/<?= htmlspecialchars($item["gambar"]); ?>"
                                                class="product-image"
                                                alt="<?= htmlspecialchars($item["nama_produk"]); ?>">

                                        <?php else: ?>

                                            <div class="product-image"></div>

                                        <?php endif; ?>


                                        <div>

                                            <div class="product-name">
                                                <?= htmlspecialchars($item["nama_produk"]); ?>
                                            </div>

                                            <div class="product-price">
                                                Produk VegeBanua Mart
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>
                                    Rp <?= number_format($item["harga"], 0, ",", "."); ?>
                                </td>

                                <td>
                                    <?= $item["jumlah"]; ?> pcs
                                </td>

                                <td>

                                    <span class="subtotal">
                                        Rp <?= number_format($item["subtotal"], 0, ",", "."); ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>


                <div class="total-box">

                    <div class="total-label">
                        Total Pesanan
                    </div>

                    <div class="total-value">
                        Rp <?= number_format($order["total"], 0, ",", "."); ?>
                    </div>
                    
                    
                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                        <a
                            href="struk.php?id=<?= (int) $order['id']; ?>"
                            target="_blank"
                            style="
                                display: inline-block;
                                padding: 12px 20px;
                                background: #00a94f;
                                color: white;
                                text-decoration: none;
                                border-radius: 8px;
                                font-size: 14px;
                                font-weight: bold;
                            "
                        >
                            <i class="fa fa-print"></i>
                            Cetak Struk
                        </a>
                    </div>


                </div>

            </div>

        </div>

    </main>

</div>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>