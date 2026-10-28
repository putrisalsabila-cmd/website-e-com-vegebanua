<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION["cart"];
$total = 0;
$total_item = 0;

foreach ($cart as $item) {
    $total += $item["harga"] * $item["jumlah"];
    $total_item += $item["jumlah"];
}

$pesan = "";
$metode = $_POST["metode_pembayaran"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $metode_valid = ["QRIS", "Transfer Bank", "Tunai"];

    if (!in_array($metode, $metode_valid, true)) {
        $pesan = "Silakan pilih metode pembayaran terlebih dahulu.";
    } else {
        $user_id = (int) $_SESSION["user_id"];

        mysqli_begin_transaction($conn);

        try {
            // Periksa stok terbaru sebelum membuat pesanan.
            foreach ($cart as $item) {
                $product_id = (int) $item["id"];
                $jumlah = (int) $item["jumlah"];

                $cek = mysqli_prepare(
                    $conn,
                    "SELECT stok FROM products WHERE id = ? FOR UPDATE"
                );
                mysqli_stmt_bind_param($cek, "i", $product_id);
                mysqli_stmt_execute($cek);
                mysqli_stmt_bind_result($cek, $stok_db);
                $ada = mysqli_stmt_fetch($cek);
                mysqli_stmt_close($cek);

                if (!$ada || $jumlah < 1 || $stok_db < $jumlah) {
                    throw new Exception(
                        "Stok salah satu produk tidak mencukupi. Silakan periksa keranjang."
                    );
                }
            }

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO orders
                (user_id, total, status, metode_pembayaran, status_pembayaran)
                VALUES (?, ?, 'Menunggu', ?, 'Belum Dibayar')"
            );

            mysqli_stmt_bind_param($stmt, "ids", $user_id, $total, $metode);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            foreach ($cart as $item) {
                $product_id = (int) $item["id"];
                $jumlah = (int) $item["jumlah"];
                $harga = (float) $item["harga"];
                $subtotal = $harga * $jumlah;

                $detail = mysqli_prepare(
                    $conn,
                    "INSERT INTO order_details
                    (order_id, product_id, jumlah, harga, subtotal)
                    VALUES (?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $detail,
                    "iiidd",
                    $order_id,
                    $product_id,
                    $jumlah,
                    $harga,
                    $subtotal
                );
                mysqli_stmt_execute($detail);
                mysqli_stmt_close($detail);

                $stok = mysqli_prepare(
                    $conn,
                    "UPDATE products SET stok = stok - ?
                     WHERE id = ? AND stok >= ?"
                );
                mysqli_stmt_bind_param(
                    $stok,
                    "iii",
                    $jumlah,
                    $product_id,
                    $jumlah
                );
                mysqli_stmt_execute($stok);

                if (mysqli_stmt_affected_rows($stok) !== 1) {
                    mysqli_stmt_close($stok);
                    throw new Exception("Stok berubah. Silakan ulangi checkout.");
                }

                mysqli_stmt_close($stok);
            }

            mysqli_commit($conn);
            $_SESSION["cart"] = [];

            
            header("Location: pesanan.php?checkout=berhasil");
            exit;

            

        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $pesan = $e->getMessage();

            if (
                strpos($pesan, "Stok") === false &&
                strpos($pesan, "stok") === false
            ) {
                $pesan = "Pesanan gagal dibuat. Silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Checkout | VegeBanua Mart</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
*{box-sizing:border-box}
body{margin:0;font-family:Arial,sans-serif;background:#f5f8f5;color:#333}
.checkout-topbar{background:#fff;padding:18px 35px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 15px #0000000f}
.vege-logo{font-size:25px;font-weight:800;color:#43a047;text-decoration:none}
.vege-logo:hover{color:#2e7d32;text-decoration:none}
.back-cart-top{color:#43a047;font-weight:600;text-decoration:none;padding:9px 14px;border-radius:8px}
.back-cart-top:hover{background:#eaf6eb;text-decoration:none;color:#2e7d32}
.checkout-container{max-width:1100px;margin:45px auto 80px;padding:0 20px}
.checkout-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
.checkout-heading h1{font-size:32px;font-weight:800;margin:0 0 7px;color:#263238}
.checkout-heading h1 i{color:#43a047;margin-right:8px}
.checkout-heading p{color:#888;font-size:14px;margin:0}
.checkout-badge{background:#e8f5e9;color:#2e7d32;padding:9px 16px;border-radius:30px;font-size:13px;font-weight:bold}
.checkout-layout{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:25px;align-items:start}
.order-box,.summary-box{background:#fff;border-radius:18px;padding:25px;border:1px solid #edf1ee;box-shadow:0 6px 25px #0000000d}
.box-title,.summary-title{font-size:18px;font-weight:700;margin-bottom:20px;color:#263238}
.box-title i,.summary-title i{color:#43a047;margin-right:8px}
.account-box{background:#f5faf5;border-radius:12px;padding:15px;display:flex;align-items:center;gap:12px;margin-bottom:20px}
.account-icon{width:42px;height:42px;border-radius:50%;background:#e8f5e9;color:#43a047;display:flex;align-items:center;justify-content:center}
.account-info span{display:block;font-size:11px;color:#999;margin-bottom:4px}
.checkout-item{display:flex;align-items:center;gap:13px;padding:15px 0;border-bottom:1px solid #eee}
.item-image{width:65px;height:65px;flex-shrink:0;border-radius:10px;overflow:hidden;background:#f1f7f1}
.item-image img{width:100%;height:100%;object-fit:cover}
.item-info{flex:1}
.item-info h3{font-size:15px;font-weight:700;margin:0 0 6px}
.item-info p{font-size:12px;color:#888;margin:0}
.item-price{font-weight:700;color:#43a047;white-space:nowrap;font-size:13px}
.pesan{background:#fff1f2;color:#b71c1c;padding:12px;border-radius:9px;margin-bottom:17px;font-size:13px}
.summary-box{position:sticky;top:20px}
.summary-row{display:flex;justify-content:space-between;margin-bottom:14px;font-size:13px;color:#777;gap:10px}
.summary-row strong{color:#444}
.summary-divider{border:0;border-top:1px solid #eee;margin:20px 0}
.total-row{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:20px}
.total-label{font-weight:600}
.total-price{font-size:21px;font-weight:800;color:#2e7d32}
.payment-title{font-size:15px;font-weight:700;margin:22px 0 12px}
.payment-option{display:block;border:1px solid #e1e8e1;border-radius:11px;padding:13px;margin-bottom:10px;cursor:pointer;transition:.2s}
.payment-option:has(input:checked){border-color:#43a047;background:#f2fbf3;box-shadow:0 0 0 1px #43a047}
.payment-option input{accent-color:#43a047;margin-right:8px}
.payment-option strong{font-size:13px}
.payment-option small{display:block;color:#888;margin:5px 0 0 25px;font-size:11px}
.payment-info{display:none;background:#f6faf6;border-radius:10px;padding:13px;margin:10px 0 16px;font-size:12px;line-height:1.7}
.payment-info.active{display:block}
.demo-warning{color:#a15c00;background:#fff7e6;padding:9px;border-radius:7px;margin-top:8px;font-size:11px}
.confirm-btn{width:100%;border:0;padding:14px;border-radius:10px;background:linear-gradient(135deg,#43a047,#66bb6a);color:white;font-size:14px;font-weight:bold;cursor:pointer}
.confirm-btn:hover{background:#2e7d32}
.secure-info{text-align:center;margin-top:14px;color:#999;font-size:11px;line-height:1.6}
@media(max-width:850px){.checkout-layout{grid-template-columns:1fr}.summary-box{position:static}}
@media(max-width:600px){.checkout-topbar{padding:15px;gap:10px}.vege-logo{font-size:19px}.checkout-container{margin-top:30px}.checkout-header{align-items:flex-start;flex-direction:column;gap:12px}.checkout-heading h1{font-size:27px}.order-box,.summary-box{padding:18px}.item-image{width:55px;height:55px}.item-price{font-size:12px}}
</style>
</head>
<body>

<header class="checkout-topbar">
    <a href="index.php" class="vege-logo">VegeBanua Mart</a>
    <a href="cart.php" class="back-cart-top"><i class="fa fa-arrow-left"></i> Kembali ke Keranjang</a>
</header>

<main class="checkout-container">
    <div class="checkout-header">
        <div class="checkout-heading">
            <h1><i class="fa fa-credit-card"></i> Checkout</h1>
            <p>Periksa pesanan dan pilih metode pembayaranmu.</p>
        </div>
        <div class="checkout-badge"><?= $total_item; ?> item</div>
    </div>

    <div class="checkout-layout">
        <section class="order-box">
            <?php if ($pesan !== ""): ?>
                <div class="pesan"><?= htmlspecialchars($pesan); ?></div>
            <?php endif; ?>

            <div class="box-title"><i class="fa fa-shopping-bag"></i> Detail Pesanan</div>

            <div class="account-box">
                <div class="account-icon"><i class="fa fa-user"></i></div>
                <div class="account-info">
                    <span>Pesanan untuk</span>
                    <strong><?= htmlspecialchars($_SESSION["nama"]); ?></strong>
                </div>
            </div>

            <?php foreach ($cart as $item): ?>
                <div class="checkout-item">
                    <div class="item-image">
                        <img src="images/<?= htmlspecialchars($item["gambar"]); ?>"
                             alt="<?= htmlspecialchars($item["nama_produk"]); ?>">
                    </div>
                    <div class="item-info">
                        <h3><?= htmlspecialchars($item["nama_produk"]); ?></h3>
                        <p><?= (int)$item["jumlah"]; ?> × Rp<?= number_format($item["harga"],0,",","."); ?></p>
                    </div>
                    <div class="item-price">
                        Rp<?= number_format($item["harga"]*$item["jumlah"],0,",","."); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="summary-box">
            <div class="summary-title"><i class="fa fa-file-text-o"></i> Ringkasan Pembayaran</div>

            <div class="summary-row"><span>Jenis produk</span><strong><?= count($cart); ?></strong></div>
            <div class="summary-row"><span>Total item</span><strong><?= $total_item; ?></strong></div>
            <hr class="summary-divider">

            <div class="total-row">
                <span class="total-label">Total Pembayaran</span>
                <span class="total-price">Rp<?= number_format($total,0,",","."); ?></span>
            </div>

            <form method="POST" id="checkoutForm">
                <div class="payment-title">Pilih Metode Pembayaran</div>

                <label class="payment-option">
                    <input type="radio" name="metode_pembayaran" value="QRIS" required <?= $metode==="QRIS" ? "checked" : ""; ?>>
                    <strong><i class="fa fa-qrcode"></i> QRIS</strong>
                    <small>Pembayaran menggunakan kode QR.</small>
                </label>

                <div class="payment-info <?= $metode==="QRIS" ? "active" : ""; ?>" id="infoQRIS">
                    <strong>QRIS — DEMONSTRASI</strong><br>
                    Pada versi tugas ini, QRIS asli belum terhubung.
                    <div class="demo-warning">Ini hanya simulasi. Jangan gunakan untuk melakukan pembayaran sungguhan.</div>
                </div>

                <label class="payment-option">
                    <input type="radio" name="metode_pembayaran" value="Transfer Bank" required <?= $metode==="Transfer Bank" ? "checked" : ""; ?>>
                    <strong><i class="fa fa-university"></i> Transfer Bank</strong>
                    <small>Transfer melalui rekening bank.</small>
                </label>

                <div class="payment-info <?= $metode==="Transfer Bank" ? "active" : ""; ?>" id="infoTransfer">
                    <strong>Informasi Rekening — CONTOH</strong><br>
                    Bank: <strong>Bank Contoh</strong><br>
                    Nomor rekening: <strong>0000000000</strong><br>
                    Atas nama: <strong>VegeBanua Mart (Contoh)</strong>
                    <div class="demo-warning">Data rekening di atas fiktif dan hanya untuk demonstrasi tugas.</div>
                </div>

                <label class="payment-option">
                    <input type="radio" name="metode_pembayaran" value="Tunai" required <?= $metode==="Tunai" ? "checked" : ""; ?>>
                    <strong><i class="fa fa-money"></i> Tunai (Cash)</strong>
                    <small>Bayar tunai saat pesanan diterima atau sesuai ketentuan toko.</small>
                </label>

                <div class="payment-info <?= $metode==="Tunai" ? "active" : ""; ?>" id="infoTunai">
                    Pembayaran dicatat sebagai <strong>Belum Dibayar</strong> sampai admin mengonfirmasi bahwa pembayaran sudah diterima.
                </div>

                <button type="submit" class="confirm-btn">
                    <i class="fa fa-check-circle"></i> Buat Pesanan
                </button>
            </form>

            <div class="secure-info">
                <i class="fa fa-lock"></i>
                Pesanan disimpan ke sistem. Status pembayaran perlu diperbarui setelah pembayaran diverifikasi.
            </div>
        </section>
    </div>
</main>

<script>
const radioMetode = document.querySelectorAll('input[name="metode_pembayaran"]');
const informasi = {
    "QRIS": document.getElementById("infoQRIS"),
    "Transfer Bank": document.getElementById("infoTransfer"),
    "Tunai": document.getElementById("infoTunai")
};

function tampilkanInfo() {
    const dipilih = document.querySelector('input[name="metode_pembayaran"]:checked');
    Object.values(informasi).forEach(el => el.classList.remove("active"));
    if (dipilih && informasi[dipilih.value]) {
        informasi[dipilih.value].classList.add("active");
    }
}

radioMetode.forEach(radio => radio.addEventListener("change", tampilkanInfo));
tampilkanInfo();
</script>
</body>
</html>