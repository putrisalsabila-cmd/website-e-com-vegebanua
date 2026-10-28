<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$query = "
    SELECT id, tanggal, total, status
    FROM orders
    WHERE user_id = ?
    ORDER BY tanggal DESC
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Pesanan Saya | VegeBanua Mart</title>

<link rel="stylesheet"
      href="css/bootstrap.min.css">

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

/* =========================
   GLOBAL
========================= */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f8f5;
    color: #333;
}


/* =========================
   TOPBAR
========================= */

.order-topbar {
    background: white;
    min-height: 75px;
    padding: 0 35px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    box-shadow: 0 2px 15px rgba(0,0,0,0.06);

    position: relative;
    z-index: 10;
}

.vege-logo {
    font-size: 24px;
    font-weight: 800;
    color: #43a047 !important;
    text-decoration: none;
    white-space: nowrap;
}

.vege-logo:hover {
    color: #388e3c !important;
    text-decoration: none;
}

.back-shopping {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 10px 17px;

    background: #f1f8f2;
    color: #43a047 !important;

    border-radius: 9px;

    font-size: 13px;
    font-weight: 700;

    text-decoration: none;

    transition: 0.25s;
}

.back-shopping:hover {
    background: #e3f2e5;
    color: #2e7d32 !important;
    text-decoration: none;
    transform: translateY(-1px);
}


/* =========================
   CONTAINER
========================= */

.order-container {
    max-width: 1100px;
    margin: 55px auto 80px;
    padding: 0 20px;
}


/* =========================
   HEADER
========================= */

.order-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 30px;
}

.order-heading-left h1 {
    margin: 0 0 7px;

    font-size: 32px;
    font-weight: 800;

    color: #263238;
}

.order-heading-left h1 i {
    color: #43a047;
    margin-right: 8px;
}

.order-heading-left p {
    margin: 0;

    color: #888;
    font-size: 14px;
}

.order-badge {
    background: #e8f5e9;
    color: #2e7d32;

    padding: 9px 16px;

    border-radius: 30px;

    font-size: 13px;
    font-weight: bold;
}


/* =========================
   MAIN BOX
========================= */

.order-box {
    background: white;

    border-radius: 20px;

    padding: 27px;

    border: 1px solid #edf1ee;

    box-shadow: 0 6px 25px rgba(0,0,0,0.05);
}


/* =========================
   ORDER CARD
========================= */

.order-card {
    background: #ffffff;

    border: 1px solid #edf1ee;

    border-radius: 15px;

    padding: 21px;

    margin-bottom: 15px;

    transition: 0.25s;
}

.order-card:hover {
    transform: translateY(-2px);

    box-shadow: 0 7px 20px rgba(0,0,0,0.06);

    border-color: #dcefe0;
}

.order-card:last-child {
    margin-bottom: 0;
}


/* =========================
   ORDER HEADER
========================= */

.order-header {
    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 15px;

    margin-bottom: 17px;
}

.order-number {
    font-size: 17px;

    font-weight: 700;

    color: #263238;
}

.order-number span {
    color: #43a047;
}

.order-date {
    color: #999;

    font-size: 12px;
}


/* =========================
   PRODUCT LIST
========================= */

.product-list {
    border-top: 1px solid #eeeeee;

    padding-top: 15px;

    margin-bottom: 17px;
}

.product-item {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 10px 0;

    border-bottom: 1px solid #f2f2f2;
}

.product-item:last-child {
    border-bottom: none;
}

.product-info {
    display: flex;

    align-items: center;

    gap: 12px;

    min-width: 0;
}

.product-image {
    width: 55px;
    height: 55px;

    object-fit: cover;

    border-radius: 9px;

    background: #f5f5f5;
}

.product-name {
    font-size: 13px;

    font-weight: 700;

    color: #333;

    margin-bottom: 4px;
}

.product-detail {
    color: #999;

    font-size: 11px;
}

.review-btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    padding: 8px 13px;

    background: #fff8e1;

    color: #f39c12 !important;

    border: 1px solid #ffe082;

    border-radius: 8px;

    font-size: 11px;

    font-weight: 700;

    text-decoration: none;

    white-space: nowrap;

    transition: 0.25s;
}

.review-btn:hover {
    background: #ffc107;

    color: white !important;

    border-color: #ffc107;

    text-decoration: none;

    transform: translateY(-1px);
}


/* =========================
   ORDER INFO
========================= */

.order-info {
    display: flex;

    justify-content: space-between;

    align-items: center;

    border-top: 1px solid #eeeeee;

    padding-top: 17px;
}

.order-total {
    font-size: 13px;

    color: #777;
}

.order-total span {
    display: inline-block;

    margin-left: 5px;

    font-size: 17px;

    font-weight: 800;

    color: #43a047;
}


/* =========================
   STATUS
========================= */

.status {
    display: inline-block;

    padding: 7px 14px;

    border-radius: 30px;

    font-size: 12px;

    font-weight: 700;
}

.status-menunggu {
    background: #fff8e1;
    color: #b77900;
}

.status-diproses {
    background: #e3f2fd;
    color: #1976d2;
}

.status-selesai {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-dibatalkan {
    background: #ffebee;
    color: #c62828;
}


/* =========================
   CANCEL BUTTON
========================= */

.cancel-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    margin-top: 17px;

    padding: 9px 15px;

    background: #fff5f5;

    color: #d32f2f !important;

    border: 1px solid #ffcdd2;

    border-radius: 8px;

    font-size: 12px;

    font-weight: 700;

    text-decoration: none;

    transition: 0.25s;
}

.cancel-btn:hover {
    background: #ffebee;

    color: #c62828 !important;

    text-decoration: none;

    border-color: #ef9a9a;
}


/* =========================
   EMPTY ORDER
========================= */

.empty-order {
    text-align: center;

    padding: 65px 20px;
}

.empty-order-icon {
    width: 85px;
    height: 85px;

    margin: 0 auto 20px;

    border-radius: 50%;

    background: #e8f5e9;

    display: flex;

    align-items: center;
    justify-content: center;
}

.empty-order-icon i {
    font-size: 38px;

    color: #43a047;
}

.empty-order h3 {
    margin: 0 0 9px;

    font-size: 20px;

    font-weight: 700;

    color: #333;
}

.empty-order p {
    margin: 0;

    color: #888;

    font-size: 14px;
}


/* =========================
   SHOP BUTTON
========================= */

.shop-btn {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    margin-top: 22px;

    padding: 12px 23px;

    background: #43a047;

    color: white !important;

    border-radius: 9px;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    transition: 0.25s;
}

.shop-btn:hover {
    background: #388e3c;

    color: white !important;

    text-decoration: none;

    transform: translateY(-1px);
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 768px) {

    .order-topbar {
        padding: 0 18px;
    }

    .vege-logo {
        font-size: 20px;
    }

    .back-shopping {
        padding: 9px 12px;
        font-size: 12px;
    }

    .order-container {
        margin-top: 40px;
    }

    .order-heading {
        align-items: flex-start;

        flex-direction: column;

        gap: 15px;
    }

    .order-heading-left h1 {
        font-size: 27px;
    }

    .order-box {
        padding: 20px;
    }

    .order-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .order-date {
        margin-top: -5px;
    }

    .product-item {
        align-items: flex-start;

        flex-direction: column;
    }

    .review-btn {
        width: 100%;
    }

    .order-info {
        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .status {
        margin-top: 2px;
    }

    .cancel-btn {
        width: 100%;
    }

}


@media (max-width: 480px) {

    .order-topbar {
        min-height: 68px;
    }

    .vege-logo {
        font-size: 18px;
    }

    .back-shopping {
        padding: 8px 10px;
    }

    .back-shopping i {
        display: none;
    }

}

</style>

</head>


<body>


<!-- =========================
     TOPBAR
========================= -->

<div class="order-topbar">

    <a href="index.php"
       class="vege-logo">

        VegeBanua Mart

    </a>


    <a href="shop.php"
       class="back-shopping">

        <i class="fa fa-arrow-left"></i>

        Kembali Belanja

    </a>

</div>


<!-- =========================
     PESANAN
========================= -->

<div class="order-container">


    <!-- HEADER -->

    <div class="order-heading">

        <div class="order-heading-left">

            <h1>

                <i class="fa fa-file-text-o"></i>

                Pesanan Saya

            </h1>

            <p>
                Lihat riwayat dan status pesananmu di VegeBanua Mart.
            </p>

        </div>


        <div class="order-badge">

            <?= mysqli_num_rows($result); ?> pesanan

        </div>

    </div>


    <!-- ORDER BOX -->

    <div class="order-box">


        <?php if (mysqli_num_rows($result) == 0): ?>


            <!-- =========================
                 BELUM ADA PESANAN
            ========================= -->

            <div class="empty-order">

                <div class="empty-order-icon">

                    <i class="fa fa-shopping-bag"></i>

                </div>


                <h3>

                    Belum Ada Pesanan

                </h3>


                <p>

                    Kamu belum melakukan pembelian di VegeBanua Mart.

                </p>


                <a href="shop.php"
                   class="shop-btn">

                    <i class="fa fa-shopping-cart"></i>

                    Mulai Belanja

                </a>

            </div>


        <?php else: ?>


            <!-- =========================
                 DAFTAR PESANAN
            ========================= -->

            <?php while ($order = mysqli_fetch_assoc($result)): ?>


                <?php

                /*
                 * Ambil produk yang ada di pesanan ini
                 */

                $order_id = $order["id"];

                $query_products = "
                    SELECT
                        order_details.product_id,
                        order_details.jumlah,
                        order_details.harga,
                        products.nama_produk,
                        products.gambar
                    FROM order_details
                    INNER JOIN products
                        ON order_details.product_id = products.id
                    WHERE order_details.order_id = ?
                ";

                $stmt_products = mysqli_prepare(
                    $conn,
                    $query_products
                );

                mysqli_stmt_bind_param(
                    $stmt_products,
                    "i",
                    $order_id
                );

                mysqli_stmt_execute(
                    $stmt_products
                );

                $result_products = mysqli_stmt_get_result(
                    $stmt_products
                );

                ?>


                <div class="order-card">


                    <!-- HEADER PESANAN -->

                    <div class="order-header">


                        <div class="order-number">

                            Pesanan

                            <span>
                                #<?= $order["id"]; ?>
                            </span>

                        </div>


                        <div class="order-date">

                            <i class="fa fa-calendar"></i>

                            <?= date(
                                "d-m-Y H:i",
                                strtotime($order["tanggal"])
                            ); ?>

                        </div>


                    </div>


                    <?php

                    $status = $order["status"];

                    $status_class = strtolower(
                        str_replace(
                            " ",
                            "-",
                            $status
                        )
                    );

                    ?>


                    <!-- DAFTAR PRODUK -->

                    <div class="product-list">


                        <?php while ($product = mysqli_fetch_assoc($result_products)): ?>


                            <div class="product-item">


                                <div class="product-info">


                                    <img
                                        src="images/<?= htmlspecialchars($product["gambar"]); ?>"
                                        class="product-image"
                                        alt="<?= htmlspecialchars($product["nama_produk"]); ?>"
                                    >


                                    <div>

                                        <div class="product-name">

                                            <?= htmlspecialchars(
                                                $product["nama_produk"]
                                            ); ?>

                                        </div>


                                        <div class="product-detail">

                                            <?= $product["jumlah"]; ?> item

                                            ×

                                            Rp<?= number_format(
                                                $product["harga"],
                                                0,
                                                ",",
                                                "."
                                            ); ?>

                                        </div>

                                    </div>


                                </div>


                                <?php if ($status == "Selesai"): ?>


                                    <a
                                        href="beri_ulasan.php?order_id=<?= $order_id; ?>&product_id=<?= $product["product_id"]; ?>"
                                        class="review-btn"
                                    >

                                        <i class="fa fa-star"></i>

                                        Beri Ulasan

                                    </a>


                                <?php endif; ?>


                            </div>


                        <?php endwhile; ?>


                    </div>


                    <!-- TOTAL + STATUS -->

                    <div class="order-info">


                        <div class="order-total">

                            Total:

                            <span>

                                Rp<?= number_format(
                                    $order["total"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </span>

                        </div>


                        <span class="status status-<?= $status_class; ?>">

                            <?= htmlspecialchars($status); ?>

                        </span>


                    </div>


                    <!-- BATALKAN -->

                    <?php if ($status == "Menunggu"): ?>


                        <a
                            href="batalkan_pesanan.php?id=<?= $order["id"]; ?>"
                            class="cancel-btn"
                            onclick="return confirm('Yakin ingin membatalkan pesanan ini?');"
                        >

                            <i class="fa fa-times-circle"></i>

                            Batalkan Pesanan

                        </a>


                    <?php endif; ?>


                </div>


                <?php

                mysqli_stmt_close(
                    $stmt_products
                );

                ?>


            <?php endwhile; ?>


        <?php endif; ?>


    </div>


</div>


<script src="js/jquery.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.bundle.min.js"></script>


</body>

</html>


<?php

mysqli_stmt_close($stmt);

?>