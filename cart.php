<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$cart = $_SESSION["cart"];

$total = 0;
$total_item = 0;

foreach ($cart as $item) {
    $total += $item["harga"] * $item["jumlah"];
    $total_item += $item["jumlah"];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Keranjang | VegeBanua Mart</title>

<link rel="stylesheet"
      href="css/bootstrap.min.css">

<link rel="stylesheet"
      href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

<style>

/* =========================
   DASAR
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
   HEADER CART
========================= */

.cart-topbar {
    background: white;

    padding: 18px 35px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-shadow: 0 2px 15px rgba(0,0,0,0.06);

    position: relative;

    z-index: 10;
}

.vege-logo {
    font-size: 25px;

    font-weight: 800;

    color: #43a047;

    text-decoration: none;
}

.vege-logo:hover {
    color: #2e7d32;

    text-decoration: none;
}

.back-shopping {
    display: inline-flex;

    align-items: center;

    gap: 8px;

    color: #43a047;

    font-size: 14px;

    font-weight: 600;

    text-decoration: none;

    padding: 9px 14px;

    border-radius: 8px;

    transition: 0.2s;
}

.back-shopping:hover {
    background: #eaf6eb;

    color: #2e7d32;

    text-decoration: none;
}


/* =========================
   CONTAINER
========================= */

.cart-container {

    max-width: 1150px;

    margin: 55px auto 80px;

    padding: 0 20px;
}


/* =========================
   HEADER KERANJANG
========================= */

.cart-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;
}

.cart-heading h1 {

    font-size: 32px;

    font-weight: 800;

    color: #263238;

    margin: 0 0 7px;
}

.cart-heading p {

    margin: 0;

    color: #8a8a8a;

    font-size: 14px;
}

.cart-heading i {

    color: #43a047;

    margin-right: 8px;
}


/* =========================
   BADGE JUMLAH
========================= */

.item-badge {

    background: #e8f5e9;

    color: #2e7d32;

    padding: 9px 16px;

    border-radius: 30px;

    font-size: 13px;

    font-weight: bold;
}


/* =========================
   LAYOUT
========================= */

.cart-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr) 330px;

    gap: 25px;

    align-items: start;
}


/* =========================
   PRODUCT LIST
========================= */

.products-box {

    background: white;

    border-radius: 20px;

    padding: 10px 25px;

    box-shadow:
        0 6px 25px rgba(0,0,0,0.05);

    border: 1px solid #edf1ee;
}

.cart-item {

    display: flex;

    align-items: center;

    gap: 20px;

    padding: 22px 5px;

    border-bottom: 1px solid #eeeeee;

    transition: 0.25s;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item:hover {
    transform: translateX(3px);
}


/* =========================
   FOTO PRODUK
========================= */

.product-image {

    width: 105px;

    height: 105px;

    flex-shrink: 0;

    border-radius: 16px;

    overflow: hidden;

    background: #f1f7f1;

    border: 1px solid #e6eee7;
}

.product-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition: 0.3s;
}

.cart-item:hover .product-image img {
    transform: scale(1.06);
}


/* =========================
   INFO PRODUK
========================= */

.product-info {

    flex: 1;

    min-width: 0;
}

.product-info h3 {

    margin: 0 0 7px;

    font-size: 19px;

    font-weight: 700;

    color: #263238;
}

.product-price {

    margin: 0;

    color: #43a047;

    font-size: 16px;

    font-weight: 700;
}

.product-subtotal {

    margin-top: 7px;

    color: #888;

    font-size: 13px;
}

.product-subtotal strong {
    color: #555;
}


/* =========================
   QUANTITY
========================= */

.quantity-area {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-top: 13px;
}

.quantity-label {

    color: #999;

    font-size: 12px;

    margin-right: 4px;
}

.quantity {

    display: flex;

    align-items: center;

    border: 1px solid #dfe7df;

    border-radius: 9px;

    overflow: hidden;

    background: #fafcfa;
}

.quantity-btn {

    width: 32px;

    height: 32px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: transparent;

    color: #43a047;

    text-decoration: none;

    font-size: 16px;

    font-weight: bold;

    transition: 0.2s;
}

.quantity-btn:hover {

    background: #e8f5e9;

    color: #2e7d32;

    text-decoration: none;
}

.quantity-number {

    min-width: 35px;

    text-align: center;

    font-size: 13px;

    font-weight: bold;

    color: #333;
}


/* =========================
   HAPUS
========================= */

.delete-btn {

    margin-left: 8px;

    color: #999;

    font-size: 12px;

    text-decoration: none;

    padding: 7px 9px;

    border-radius: 7px;

    transition: 0.2s;
}

.delete-btn:hover {

    color: #dc3545;

    background: #fff1f2;

    text-decoration: none;
}


/* =========================
   SUMMARY
========================= */

.summary-box {

    background: white;

    border-radius: 20px;

    padding: 25px;

    box-shadow:
        0 6px 25px rgba(0,0,0,0.05);

    border: 1px solid #edf1ee;

    position: sticky;

    top: 25px;
}

.summary-title {

    display: flex;

    align-items: center;

    gap: 9px;

    font-size: 18px;

    font-weight: 700;

    color: #263238;

    margin-bottom: 23px;
}

.summary-title i {
    color: #43a047;
}

.summary-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 15px;

    font-size: 13px;

    color: #777;
}

.summary-row strong {
    color: #444;
}

.summary-divider {

    border: none;

    border-top: 1px solid #eeeeee;

    margin: 20px 0;
}

.total-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;
}

.total-label {

    font-size: 15px;

    font-weight: 600;

    color: #444;
}

.total-price {

    font-size: 22px;

    font-weight: 800;

    color: #2e7d32;
}


/* =========================
   CHECKOUT BUTTON
========================= */

.checkout-btn {

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 9px;

    width: 100%;

    padding: 14px;

    background: linear-gradient(
        135deg,
        #43a047,
        #66bb6a
    );

    color: white !important;

    border-radius: 11px;

    text-decoration: none;

    font-size: 14px;

    font-weight: bold;

    box-shadow:
        0 6px 15px rgba(67,160,71,0.22);

    transition: 0.25s;
}

.checkout-btn:hover {

    transform: translateY(-2px);

    box-shadow:
        0 9px 20px rgba(67,160,71,0.3);

    text-decoration: none;

    color: white !important;
}


/* =========================
   BACK SHOP
========================= */

.back-shop {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    margin-top: 15px;

    padding: 11px;

    border-radius: 10px;

    color: #43a047;

    background: #f1f8f2;

    text-decoration: none;

    font-size: 13px;

    font-weight: 600;

    transition: 0.2s;
}

.back-shop:hover {

    background: #e5f2e6;

    color: #2e7d32;

    text-decoration: none;
}


/* =========================
   EMPTY CART
========================= */

.empty-cart {

    background: white;

    border-radius: 20px;

    padding: 75px 25px;

    text-align: center;

    box-shadow:
        0 6px 25px rgba(0,0,0,0.05);
}

.empty-icon {

    width: 90px;

    height: 90px;

    margin: 0 auto 22px;

    border-radius: 50%;

    background: #e8f5e9;

    color: #43a047;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 38px;
}

.empty-cart h2 {

    margin-bottom: 9px;

    font-size: 22px;

    color: #333;
}

.empty-cart p {

    color: #888;

    font-size: 14px;

    margin-bottom: 25px;
}

.empty-shop-btn {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    background: #43a047;

    color: white !important;

    padding: 12px 22px;

    border-radius: 9px;

    text-decoration: none;

    font-weight: 600;

    font-size: 13px;
}

.empty-shop-btn:hover {

    background: #2e7d32;

    text-decoration: none;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .cart-layout {

        grid-template-columns: 1fr;
    }

    .summary-box {

        position: static;
    }

}


@media (max-width: 650px) {

    .cart-topbar {

        padding: 15px 18px;
    }

    .vege-logo {

        font-size: 21px;
    }

    .cart-container {

        margin-top: 40px;
    }

    .cart-header {

        align-items: flex-start;

        gap: 15px;

        flex-direction: column;
    }

    .cart-heading h1 {

        font-size: 27px;
    }

    .products-box {

        padding: 5px 15px;
    }

    .cart-item {

        align-items: flex-start;

        gap: 13px;
    }

    .product-image {

        width: 80px;

        height: 80px;
    }

    .product-info h3 {

        font-size: 16px;
    }

    .quantity-area {

        flex-wrap: wrap;
    }

    .delete-btn {

        margin-left: 0;
    }

}

</style>

</head>

<body>


<!-- =========================
     HEADER KHUSUS CART
========================= -->

<div class="cart-topbar">

    <a href="index.php"
       class="vege-logo">

        VegeBanua Mart

    </a>


    <a href="shop.php"
       class="back-shopping">

        <i class="fa fa-arrow-left"></i>

        Lanjut Belanja

    </a>

</div>


<!-- =========================
     CONTENT
========================= -->

<div class="cart-container">


<?php if (empty($cart)): ?>


    <!-- EMPTY -->

    <div class="empty-cart">

        <div class="empty-icon">

            <i class="fa fa-shopping-basket"></i>

        </div>


        <h2>Keranjangmu masih kosong</h2>

        <p>
            Yuk pilih sayuran segar favoritmu
            dan mulai belanja di VegeBanua Mart.
        </p>


        <a href="shop.php"
           class="empty-shop-btn">

            <i class="fa fa-leaf"></i>

            Mulai Belanja

        </a>

    </div>


<?php else: ?>


    <!-- HEADER -->

    <div class="cart-header">

        <div class="cart-heading">

            <h1>

                <i class="fa fa-shopping-cart"></i>

                Keranjang Belanja

            </h1>

            <p>
                Periksa kembali produk pilihanmu
                sebelum melanjutkan ke checkout.
            </p>

        </div>


        <div class="item-badge">

            <?= $total_item; ?> item

        </div>

    </div>


    <!-- LAYOUT -->

    <div class="cart-layout">


        <!-- PRODUCTS -->

        <div class="products-box">


            <?php foreach ($cart as $item): ?>


                <div class="cart-item">


                    <div class="product-image">

                        <img
                            src="images/<?= htmlspecialchars($item["gambar"]); ?>"
                            alt="<?= htmlspecialchars($item["nama_produk"]); ?>">

                    </div>


                    <div class="product-info">

                        <h3>
                            <?= htmlspecialchars($item["nama_produk"]); ?>
                        </h3>


                        <p class="product-price">

                            Rp<?= number_format(
                                $item["harga"],
                                0,
                                ",",
                                "."
                            ); ?>

                        </p>


                        <div class="quantity-area">

                            <span class="quantity-label">
                                Jumlah
                            </span>


                            <div class="quantity">

                                <a
                                    href="update_cart.php?action=minus&id=<?= $item["id"]; ?>"
                                    class="quantity-btn">

                                    −

                                </a>


                                <span class="quantity-number">

                                    <?= $item["jumlah"]; ?>

                                </span>


                                <a
                                    href="update_cart.php?action=plus&id=<?= $item["id"]; ?>"
                                    class="quantity-btn">

                                    +

                                </a>

                            </div>


                            <a
                                href="update_cart.php?action=delete&id=<?= $item["id"]; ?>"
                                class="delete-btn">

                                <i class="fa fa-trash"></i>

                                Hapus

                            </a>

                        </div>


                        <div class="product-subtotal">

                            Subtotal:

                            <strong>

                                Rp<?= number_format(
                                    $item["harga"] * $item["jumlah"],
                                    0,
                                    ",",
                                    "."
                                ); ?>

                            </strong>

                        </div>

                    </div>


                </div>


            <?php endforeach; ?>


        </div>


        <!-- SUMMARY -->

        <div class="summary-box">


            <div class="summary-title">

                <i class="fa fa-file-text-o"></i>

                Ringkasan Belanja

            </div>


            <div class="summary-row">

                <span>
                    Jumlah item
                </span>

                <strong>
                    <?= $total_item; ?> item
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Jenis produk
                </span>

                <strong>
                    <?= count($cart); ?>
                </strong>

            </div>


            <hr class="summary-divider">


            <div class="total-row">

                <span class="total-label">
                    Total
                </span>

                <span class="total-price">

                    Rp<?= number_format(
                        $total,
                        0,
                        ",",
                        "."
                    ); ?>

                </span>

            </div>


            <a href="checkout.php"
               class="checkout-btn">

                Lanjut ke Checkout

                <i class="fa fa-arrow-right"></i>

            </a>

        </div>


    </div>


<?php endif; ?>


</div>


<script src="js/jquery.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>