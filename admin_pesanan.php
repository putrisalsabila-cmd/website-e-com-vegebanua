
<?php
session_start();
require_once "config/database.php";

// Pastikan hanya admin yang bisa mengakses halaman ini
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

// Token keamanan untuk form
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$pesan = "";
$jenis_pesan = "success";

// Proses konfirmasi pembayaran
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["konfirmasi_pembayaran"])
) {
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        $pesan = "Permintaan tidak valid. Silakan muat ulang halaman.";
        $jenis_pesan = "danger";
    } else {
        $id_pesanan = filter_var(
            $_POST["id_pesanan"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id_pesanan || $id_pesanan <= 0) {
            $pesan = "Nomor pesanan tidak valid.";
            $jenis_pesan = "danger";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE orders
                 SET status_pembayaran = 'Lunas'
                 WHERE id = ?
                   AND status != 'Dibatalkan'
                   AND status_pembayaran != 'Lunas'"
            );

            mysqli_stmt_bind_param($stmt, "i", $id_pesanan);
            mysqli_stmt_execute($stmt);

            $berhasil = mysqli_stmt_affected_rows($stmt) > 0;
            mysqli_stmt_close($stmt);

            // Redirect agar form tidak terkirim ulang saat refresh
            header(
                "Location: admin_pesanan.php?konfirmasi=" .
                ($berhasil ? "berhasil" : "gagal")
            );
            exit;
        }
    }
}

// Pesan setelah konfirmasi
if (isset($_GET["konfirmasi"])) {
    if ($_GET["konfirmasi"] === "berhasil") {
        $pesan = "Pembayaran berhasil dikonfirmasi sebagai Lunas.";
        $jenis_pesan = "success";
    } elseif ($_GET["konfirmasi"] === "gagal") {
        $pesan = "Pembayaran tidak berubah. Pesanan mungkin sudah lunas, dibatalkan, atau tidak ditemukan.";
        $jenis_pesan = "warning";
    }
}

// Ambil daftar pesanan
$query = "
    SELECT
        orders.id,
        orders.tanggal,
        orders.total,
        orders.status,
        orders.metode_pembayaran,
        orders.status_pembayaran,
        users.nama AS nama_user,
        users.email
    FROM orders
    INNER JOIN users ON orders.user_id = users.id
    ORDER BY orders.tanggal DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . htmlspecialchars(mysqli_error($conn)));
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - VegeBanua Mart</title>

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

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #00a94f, #008f45);
            padding: 25px 15px;
            color: white;
            z-index: 1000;
            box-shadow: 3px 0 15px rgba(0,0,0,.08);
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            width: 55px;
            height: 55px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .sidebar-logo h3 {
            font-size: 19px;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-logo span {
            font-size: 11px;
            opacity: .8;
        }

        .menu-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: .65;
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

        .sidebar-menu a,
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

        .sidebar-menu a:hover,
        .sidebar-menu a.active,
        .sidebar-bottom a:hover {
            background: rgba(255,255,255,.16);
        }

        .sidebar-menu a i,
        .sidebar-bottom a i {
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

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding: 25px 30px;
        }

        .topbar {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
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
            flex-shrink: 0;
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

        .page-header {
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

        .order-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,.04);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .order-table th {
            padding: 13px 10px;
            background: #f8faf9;
            color: #777;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .3px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .order-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            vertical-align: middle;
        }

        .order-table tr:last-child td {
            border-bottom: none;
        }

        .order-id,
        .order-price {
            font-weight: 700;
            color: #00a94f;
        }

        .customer-name {
            font-weight: 700;
            color: #333;
        }

        .customer-email {
            display: block;
            color: #999;
            font-size: 10px;
            margin-top: 4px;
        }

        .order-date {
            color: #666;
            white-space: nowrap;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-menunggu {
            background: #fff2dd;
            color: #b8790c;
        }

        .status-diproses {
            background: #eaf3ff;
            color: #3977d5;
        }

        .status-selesai {
            background: #e9f8ef;
            color: #008f45;
        }

        .status-dibatalkan {
            background: #ffe8e8;
            color: #d9534f;
        }

        .btn-detail,
        .btn-konfirmasi {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 11px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-detail {
            background: #eaf8ef;
            color: #008f45;
        }

        .btn-detail:hover {
            background: #dff3e7;
            color: #006e35;
            text-decoration: none;
        }

        .btn-konfirmasi {
            background: #287a3e;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-konfirmasi:hover {
            background: #206532;
        }

        .empty-order {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-order i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #ccc;
        }

        .empty-order h5 {
            font-weight: 600;
            color: #777;
        }

        .empty-order p {
            font-size: 12px;
        }

        .alert {
            border-radius: 9px;
            margin-bottom: 20px;
        }

        .aksi-form {
            margin-top: 8px;
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
            .sidebar-bottom span {
                display: none;
            }

            .sidebar-logo {
                margin-bottom: 25px;
            }

            .logo-icon {
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

            .admin-profile > div:last-child {
                display: none;
            }
        }
    </style>
</head>

<body>

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
            <a href="admin_pesanan.php" class="active">
                <i class="fa fa-file-text-o"></i>
                <span>Kelola Pesanan</span>
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

    <div class="sidebar-bottom">
        <a href="logout.php">
            <i class="fa fa-sign-out"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<main class="main-content">

    <div class="topbar">
        <div class="topbar-title">
            <h4>Kelola Pesanan</h4>
            <p>Kelola dan pantau pesanan pelanggan VegeBanua Mart</p>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">
                <i class="fa fa-user"></i>
            </div>

            <div>
                <strong><?= e($_SESSION["nama"] ?? "Admin"); ?></strong>
                <span>Administrator</span>
            </div>
        </div>
    </div>

    <div class="page-header">
        <h2>Daftar Pesanan</h2>
        <p>Kelola pesanan dan pantau status pembayaran pelanggan.</p>
    </div>

    <?php if ($pesan !== ""): ?>
        <div class="alert alert-<?= e($jenis_pesan); ?>">
            <?= e($pesan); ?>
        </div>
    <?php endif; ?>

    <div class="order-card">
        <div class="table-wrapper">

            <?php if (mysqli_num_rows($result) > 0): ?>

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status Pesanan</th>
                            <th>Metode Bayar</th>
                            <th>Status Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while ($order = mysqli_fetch_assoc($result)): ?>

                        <?php
                        $status_class = "status-menunggu";

                        if ($order["status"] === "Diproses") {
                            $status_class = "status-diproses";
                        } elseif ($order["status"] === "Selesai") {
                            $status_class = "status-selesai";
                        } elseif ($order["status"] === "Dibatalkan") {
                            $status_class = "status-dibatalkan";
                        }

                        $status_bayar = $order["status_pembayaran"]
                            ?: "Belum Dibayar";

                        $metode_bayar = $order["metode_pembayaran"]
                            ?: "Belum dipilih";
                        ?>

                        <tr>
                            <td>
                                <span class="order-id">
                                    #<?= (int) $order["id"]; ?>
                                </span>
                            </td>

                            <td>
                                <span class="customer-name">
                                    <?= e($order["nama_user"]); ?>
                                </span>
                                <span class="customer-email">
                                    <?= e($order["email"]); ?>
                                </span>
                            </td>

                            <td>
                                <span class="order-date">
                                    <?= e(date(
                                        "d M Y, H:i",
                                        strtotime($order["tanggal"])
                                    )); ?>
                                </span>
                            </td>

                            <td>
                                <span class="order-price">
                                    <?= rupiah($order["total"]); ?>
                                </span>
                            </td>

                            <td>
                                <span class="status-badge <?= $status_class; ?>">
                                    <?= e($order["status"]); ?>
                                </span>
                            </td>

                            <td><?= e($metode_bayar); ?></td>

                            <td>
                                <?php
                                $bayar_class = $status_bayar === "Lunas"
                                    ? "status-selesai"
                                    : "status-menunggu";
                                ?>

                                <span class="status-badge <?= $bayar_class; ?>">
                                    <?= e($status_bayar); ?>
                                </span>
                            </td>

                            <td>
                                <a
                                    href="admin_pesanan_detail.php?id=<?= (int) $order["id"]; ?>"
                                    class="btn-detail"
                                >
                                    <i class="fa fa-eye"></i>
                                    Lihat Detail
                                </a>

                                <?php if (
                                    $status_bayar !== "Lunas" &&
                                    $order["status"] !== "Dibatalkan"
                                ): ?>
                                    <form
                                        method="POST"
                                        class="aksi-form"
                                        onsubmit="return confirm('Pastikan pembayaran sudah benar-benar diterima. Tandai pesanan ini sebagai Lunas?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($_SESSION["csrf_token"]); ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id_pesanan"
                                            value="<?= (int) $order["id"]; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="konfirmasi_pembayaran"
                                            value="1"
                                            class="btn-konfirmasi"
                                        >
                                            <i class="fa fa-check"></i>
                                            Konfirmasi Lunas
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                    </tbody>
                </table>

            <?php else: ?>

                <div class="empty-order">
                    <i class="fa fa-file-text-o"></i>
                    <h5>Belum Ada Pesanan</h5>
                    <p>Belum ada pelanggan yang melakukan pesanan.</p>
                </div>

            <?php endif; ?>

        </div>
    </div>

</main>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
