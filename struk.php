
<?php
session_start();
require_once "config/database.php";

// Hanya admin yang boleh membuka halaman struk
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

// Ambil ID pesanan dari URL
$order_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($order_id <= 0) {
    http_response_code(400);
    die("Nomor pesanan tidak valid.");
}

// Ambil data pesanan dan nama pelanggan
$stmt = $conn->prepare("
    SELECT
        o.id,
        o.tanggal,
        o.total,
        o.status,
        o.metode_pembayaran,
        o.status_pembayaran,
        u.nama
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    http_response_code(404);
    die("Pesanan tidak ditemukan.");
}

// Ambil rincian produk pesanan
$stmt = $conn->prepare("
    SELECT
        p.nama_produk,
        od.jumlah,
        od.harga,
        od.subtotal
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    WHERE od.order_id = ?
");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();

// Fungsi keamanan output
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Format harga Rupiah
function rupiah($angka) {
    return "Rp " . number_format((float) $angka, 0, ",", ".");
}

$metode = $order["metode_pembayaran"] ?: "Belum dipilih";
$status_pembayaran = $order["status_pembayaran"] ?: "Belum Dibayar";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #<?= e($order["id"]) ?> - VegeBanua Mart</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            background: #f0f7f0;
            color: #263b2a;
            font-family: Arial, sans-serif;
        }

        .struk {
            max-width: 650px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #b8cfb9;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 8px;
            color: #26713b;
        }

        .header p {
            margin: 5px 0;
            color: #647568;
        }

        .info {
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .info p {
            margin: 4px 0;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 11px 8px;
            border-bottom: 1px solid #e4ece4;
            text-align: left;
        }

        th {
            background: #edf6ee;
            color: #28653a;
        }

        .angka {
            text-align: right;
            white-space: nowrap;
        }

        .total {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 16px 0;
            border-top: 2px dashed #b8cfb9;
            font-size: 19px;
            font-weight: bold;
        }

        .pembayaran {
            background: #f3f8f3;
            padding: 15px;
            border-radius: 8px;
            line-height: 1.8;
            overflow-wrap: anywhere;
        }

        .catatan {
            margin-top: 20px;
            font-size: 13px;
            color: #68766b;
            line-height: 1.6;
        }

        .tombol {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .tombol a,
        .tombol button {
            display: inline-block;
            padding: 11px 18px;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .cetak {
            background: #287a3e;
            color: white;
        }

        .kembali {
            background: #e6eee7;
            color: #294c30;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .struk {
                max-width: none;
                padding: 10px;
                box-shadow: none;
                border-radius: 0;
            }

            .tombol {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="struk">
        <div class="header">
            <h1>VegeBanua Mart</h1>
            <p>Struk Pesanan</p>
            <p>Nomor Pesanan: #<?= e($order["id"]) ?></p>
        </div>

        <div class="info">
            <p>
                <strong>Nama pelanggan:</strong>
                <?= e($order["nama"]) ?>
            </p>

            <p>
                <strong>Tanggal:</strong>
                <?= e(date("d-m-Y H:i", strtotime($order["tanggal"]))) ?>
            </p>

            <p>
                <strong>Status pesanan:</strong>
                <?= e($order["status"]) ?>
            </p>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="angka">Jumlah</th>
                        <th class="angka">Harga</th>
                        <th class="angka">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($items->num_rows > 0): ?>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($item["nama_produk"]) ?></td>
                                <td class="angka"><?= e($item["jumlah"]) ?></td>
                                <td class="angka"><?= rupiah($item["harga"]) ?></td>
                                <td class="angka"><?= rupiah($item["subtotal"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Rincian produk tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="total">
            <span>Total</span>
            <span><?= rupiah($order["total"]) ?></span>
        </div>

        <div class="pembayaran">
            <strong>Informasi Pembayaran</strong><br>
            Metode: <?= e($metode) ?><br>
            Status pembayaran:
            <strong><?= e($status_pembayaran) ?></strong>
        </div>

        <p class="catatan">
            Terima kasih telah berbelanja di VegeBanua Mart!<br>
            Struk ini memuat rincian pesanan pelanggan.
            Status pembayaran mengikuti konfirmasi admin.
        </p>

        <div class="tombol">
            <button
                type="button"
                class="cetak"
                onclick="window.print()"
            >
                Cetak Struk
            </button>

            <a class="kembali" href="admin_pesanan.php">
                Kembali ke Daftar Pesanan
            </a>
        </div>
    </main>
</body>
</html>
