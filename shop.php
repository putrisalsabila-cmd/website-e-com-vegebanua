<?php
session_start();
require_once "config/database.php";

/* ==========================================
   SEARCH
========================================== */

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if ($search != "") {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $query = "
        SELECT
            products.*,
            COALESCE(AVG(reviews.rating), 0) AS rata_rating,
            COUNT(reviews.id) AS jumlah_ulasan
        FROM products
        LEFT JOIN reviews
            ON products.id = reviews.product_id
        WHERE products.nama_produk LIKE '%$search_safe%'
        GROUP BY products.id
        ORDER BY products.id DESC
    ";

} else {

    $query = "
        SELECT
            products.*,
            COALESCE(AVG(reviews.rating), 0) AS rata_rating,
            COUNT(reviews.id) AS jumlah_ulasan
        FROM products
        LEFT JOIN reviews
            ON products.id = reviews.product_id
        GROUP BY products.id
        ORDER BY products.id DESC
    ";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Belanja - VegeBanua Mart</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <link rel="icon" href="images/fevicon.png" type="image/gif">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,800;1,400&family=Sen:wght@400;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="css/jquery.mCustomScrollbar.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>

        /* ==============================
           NAVBAR
        ============================== */

        .header_section {
            background: #00a94f;
            box-shadow: 0 3px 15px rgba(0,0,0,0.15);
            position: relative;
            z-index: 1000;
        }

        .navbar {
            min-height: 75px;
            padding: 10px 25px;
        }

        .navbar-brand.vege-logo {
            font-size: 23px;
            font-weight: 800;
            color: white !important;
            white-space: nowrap;
            margin-right: 25px;
        }

        .navbar .nav-link {
            white-space: nowrap;
            color: white !important;
            font-size: 13px;
            padding: 8px 9px !important;
        }

        .navbar .nav-link:hover {
            color: #d9ffd9 !important;
        }

        .navbar .fa-user,
        .navbar .fa-shopping-cart {
            margin-right: 4px;
            color: white;
        }

        .navbar .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            margin-top: 8px;
            padding: 8px;
            min-width: 190px;
        }

        .navbar .dropdown-item {
            padding: 10px 14px;
            border-radius: 6px;
            color: #444;
            font-size: 13px;
        }

        .navbar .dropdown-item:hover {
            background: #f1fff5;
            color: #43a047;
        }

        .navbar .dropdown-item i {
            width: 20px;
            color: #43a047;
        }

        .navbar .dropdown-divider {
            margin: 5px 0;
        }


        /* ==============================
           SEARCH
        ============================== */

        .search_box {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        .search_box input {
            width: 165px;
            height: 35px;
            border: none;
            outline: none;
            padding: 0 14px;
            font-size: 12px;
            border-radius: 20px 0 0 20px;
        }

        .search_box button {
            width: 40px;
            height: 35px;
            border: none;
            outline: none;
            background: white;
            color: #00a94f;
            cursor: pointer;
            border-radius: 0 20px 20px 0;
            transition: 0.2s;
        }

        .search_box button:hover {
            background: #eafff0;
        }


        /* ==============================
           RESPONSIVE NAVBAR
        ============================== */

        @media (max-width: 1200px) {

            .navbar-brand.vege-logo {
                font-size: 21px;
                margin-right: 10px;
            }

            .navbar .nav-link {
                font-size: 12px;
                padding: 8px 6px !important;
            }

            .search_box input {
                width: 140px;
            }

        }


        @media (max-width: 991px) {

            .navbar {
                padding: 10px 15px;
            }

            .navbar-brand.vege-logo {
                font-size: 21px;
            }

            .navbar-collapse {
                background: #00a94f;
                padding: 10px 5px;
                margin-top: 5px;
                border-radius: 8px;
            }

            .navbar .nav-link {
                padding: 9px 10px !important;
            }

            .search_box {
                margin: 10px;
            }

            .search_box input {
                width: 200px;
            }

        }


        /* ==============================
           SHOP
        ============================== */

        .shop-title {
            text-align: center;
            margin-bottom: 45px;
        }

        .shop-title h1 {
            font-size: 42px;
            font-weight: 700;
            color: #222;
            margin-bottom: 15px;
        }

        .shop-title p {
            color: #666;
            font-size: 16px;
            max-width: 650px;
            margin: auto;
            line-height: 1.8;
        }


        /* ==============================
           SEARCH RESULT
        ============================== */

        .search-result-info {
            text-align: center;
            margin-top: -20px;
            margin-bottom: 30px;
            color: #666;
            font-size: 14px;
        }

        .search-result-info strong {
            color: #00a94f;
        }

        .reset-search {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 18px;
            background: #00a94f;
            color: white !important;
            border-radius: 20px;
            text-decoration: none;
            font-size: 12px;
        }

        .reset-search:hover {
            background: #008f45;
            text-decoration: none;
        }


        /* ==============================
           PRODUCT CARD
        ============================== */

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 35px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .product-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }

        .product-content {
            padding: 22px;
            text-align: center;
        }

        .product-content h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #222;
        }

        .product-rating {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .product-rating .stars {
            color: #ffc107;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .product-rating .rating-number {
            color: #555;
            font-weight: 600;
            margin-left: 5px;
        }

        .product-rating .review-count {
            color: #999;
            margin-left: 3px;
        }

        .product-price {
            color: #4caf50;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .product-description {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            min-height: 48px;
            margin-bottom: 10px;
        }

        .product-stock {
            font-size: 14px;
            color: #555;
            margin-bottom: 18px;
        }

        .shop-products {
            padding-top: 20px;
        }

        .empty-search {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-search i {
            font-size: 45px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .empty-search h3 {
            color: #555;
            margin-bottom: 10px;
        }

        .empty-search p {
            color: #999;
        }

    </style>

</head>


<body>


<!-- ==========================================
     HEADER
========================================== -->

<div class="header_section">

    <div class="container-fluid">

        <nav class="navbar navbar-expand-lg navbar-light">


            <!-- LOGO -->

            <a class="navbar-brand vege-logo"
               href="index.php">

                VegeBanua Mart

            </a>


            <!-- MOBILE TOGGLE -->

            <button class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <!-- MENU -->

            <div class="collapse navbar-collapse"
                 id="navbarSupportedContent">


                <ul class="navbar-nav ml-auto">


                    <li class="nav-item">

                        <a class="nav-link"
                           href="index.php">

                            Beranda

                        </a>

                    </li>


                    <li class="nav-item active">

                        <a class="nav-link"
                           href="shop.php">

                            Belanja

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link"
                           href="about.php">

                            Tentang Kami

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link"
                           href="vagetables.php">

                            Sayuran

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link"
                           href="blog.php">

                            Blog

                        </a>

                    </li>


                    <li class="nav-item">

                        <a class="nav-link"
                           href="contact.php">

                            Hubungi Kami

                        </a>

                    </li>


                    <?php if (isset($_SESSION["user_id"])): ?>


                        <li class="nav-item">

                            <a class="nav-link"
                               href="cart.php"
                               title="Keranjang">

                                <i class="fa fa-shopping-cart"></i>

                            </a>

                        </li>


                        <li class="nav-item dropdown">


                            <a class="nav-link dropdown-toggle"
                               href="#"
                               id="akunDropdown"
                               role="button"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false">

                                <i class="fa fa-user"></i>

                                <?= htmlspecialchars($_SESSION["nama"]); ?>

                            </a>


                            <div class="dropdown-menu dropdown-menu-right"
                                 aria-labelledby="akunDropdown">


                                <a class="dropdown-item"
                                   href="pesanan.php">

                                    <i class="fa fa-file-text-o"></i>

                                    Pesanan Saya

                                </a>


                                <div class="dropdown-divider"></div>


                                <a class="dropdown-item"
                                   href="logout.php">

                                    <i class="fa fa-sign-out"></i>

                                    Logout

                                </a>


                            </div>


                        </li>


                    <?php else: ?>


                        <li class="nav-item">

                            <a class="nav-link"
                               href="login.php">

                                Login

                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link"
                               href="register.php">

                                Daftar

                            </a>

                        </li>


                    <?php endif; ?>


                </ul>


                <!-- SEARCH -->

                <form class="form-inline my-2 my-lg-0"
                      action="shop.php"
                      method="GET">

                    <div class="search_box">

                        <input
                            type="text"
                            name="search"
                            placeholder="Cari sayuran..."
                            value="<?= htmlspecialchars($search); ?>"
                        >

                        <button type="submit"
                                title="Cari">

                            <i class="fa fa-search"></i>

                        </button>

                    </div>

                </form>


            </div>

        </nav>

    </div>

</div>

<!-- HEADER END -->



<!-- ==========================================
     SHOP
========================================== -->

<div class="container-fluid">

    <div class="layout_border">

        <div class="vagetables_section layout_padding margin_bottom90">


            <div class="container">


                <!-- JUDUL -->

                <div class="shop-title">

                    <h1>
                        Pilihan Sayuran Segar
                    </h1>

                    <p>
                        Temukan berbagai sayuran segar dan berkualitas
                        untuk kebutuhan sehari-hari Anda.
                    </p>

                </div>


                <!-- HASIL SEARCH -->

                <?php if ($search != ""): ?>

                    <div class="search-result-info">

                        Hasil pencarian untuk:
                        <strong>
                            "<?= htmlspecialchars($search); ?>"
                        </strong>

                    </div>

                <?php endif; ?>


                <!-- PRODUK -->

                <div class="shop-products">

                    <div class="row">


                        <?php if (mysqli_num_rows($result) > 0): ?>


                            <?php while ($produk = mysqli_fetch_assoc($result)): ?>


                                <div class="col-lg-4 col-md-6 mb-4">


                                    <div class="product-card">


                                        <!-- GAMBAR -->

                                        <img
                                            src="images/<?= htmlspecialchars($produk['gambar']); ?>"
                                            alt="<?= htmlspecialchars($produk['nama_produk']); ?>"
                                            class="product-image"
                                        >


                                        <div class="product-content">


                                            <!-- NAMA -->

                                            <h3>

                                                <?= htmlspecialchars($produk['nama_produk']); ?>

                                            </h3>


                                            <!-- RATING -->

                                            <div class="product-rating">

                                                <?php if ($produk["jumlah_ulasan"] > 0): ?>

                                                    <span class="stars">

                                                        <?php

                                                        $rating = round($produk["rata_rating"]);

                                                        for ($i = 1; $i <= 5; $i++):

                                                        ?>

                                                            <?php if ($i <= $rating): ?>

                                                                ★

                                                            <?php else: ?>

                                                                <span style="color:#ddd;">★</span>

                                                            <?php endif; ?>

                                                        <?php endfor; ?>

                                                    </span>

                                                    <span class="rating-number">

                                                        <?= number_format($produk["rata_rating"], 1); ?>

                                                    </span>

                                                    <span class="review-count">

                                                        (<?= $produk["jumlah_ulasan"]; ?> ulasan)

                                                    </span>

                                                <?php else: ?>

                                                    <span style="color:#999;">

                                                        Belum ada ulasan

                                                    </span>

                                                <?php endif; ?>

                                            </div>


                                            <!-- HARGA -->

                                            <div class="product-price">

                                                Rp<?= number_format(
                                                    $produk['harga'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ); ?>

                                            </div>


                                            <!-- DESKRIPSI -->

                                            <p class="product-description">

                                                <?= !empty($produk['deskripsi'])
                                                    ? htmlspecialchars($produk['deskripsi'])
                                                    : 'Sayuran segar dan berkualitas untuk kebutuhan sehari-hari.'; ?>

                                            </p>


                                            <!-- STOK -->

                                            <div class="product-stock">

                                                Stok:

                                                <strong>

                                                    <?= htmlspecialchars($produk['stok']); ?>

                                                </strong>

                                            </div>


                                            <!-- KERANJANG -->

                                            <a href="add_to_cart.php?id=<?= $produk['id']; ?>"
                                               class="btn btn-success mt-2">

                                                <i class="fa fa-shopping-cart"></i>

                                                Tambah ke Keranjang

                                            </a>


                                        </div>

                                    </div>


                                </div>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <!-- HASIL PENCARIAN KOSONG -->

                            <div class="col-12">

                                <div class="empty-search">

                                    <i class="fa fa-search"></i>

                                    <?php if ($search != ""): ?>

                                        <h3>
                                            Produk tidak ditemukan
                                        </h3>

                                        <p>
                                            Maaf, tidak ada produk yang cocok dengan
                                            pencarian
                                            <strong>
                                                "<?= htmlspecialchars($search); ?>"
                                            </strong>
                                        </p>

                                        <a href="shop.php"
                                           class="reset-search">

                                            <i class="fa fa-refresh"></i>
                                            Tampilkan Semua Produk

                                        </a>

                                    <?php else: ?>

                                        <h3>
                                            Belum ada produk tersedia.
                                        </h3>

                                        <p>
                                            Silakan cek kembali nanti.
                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>


                        <?php endif; ?>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- SHOP END -->



<!-- ==========================================
     FOOTER
========================================== -->

<div class="footer_section layout_padding">

    <div class="container">

        <div class="row">


            <div class="col-lg-4 col-sm-6">

                <h3 class="footer_text">
                    Menu
                </h3>

                <div class="footer_menu">

                    <ul>

                        <li>
                            <a href="index.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Beranda
                            </a>
                        </li>

                        <li>
                            <a href="shop.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Belanja
                            </a>
                        </li>

                        <li>
                            <a href="about.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Tentang Kami
                            </a>
                        </li>

                        <li>
                            <a href="vagetables.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Sayuran
                            </a>
                        </li>

                        <li>
                            <a href="blog.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Blog
                            </a>
                        </li>

                        <li>
                            <a href="contact.php">
                                <span class="angle_icon">
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                                Hubungi Kami
                            </a>
                        </li>

                    </ul>

                </div>

            </div>


            <div class="col-lg-4 col-sm-6">

                <h3 class="footer_text">
                    Informasi
                </h3>

                <div class="location_text">

                    <ul>

                        <li>
                            <a href="#">
                                <span class="padding_left_10">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                Banjarmasin, Kalimantan Selatan
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="padding_left_10">
                                    <i class="fa fa-phone"></i>
                                </span>
                                08xxxxxxxxxx
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <span class="padding_left_10">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                info@vegebanua.com
                            </a>
                        </li>

                    </ul>

                </div>

            </div>


            <div class="col-lg-4 col-sm-6">

                <div class="footer_main">

                    <h3 class="footer_text">
                        VegeBanua Mart
                    </h3>

                    <p class="dummy_text">
                        Menyediakan berbagai pilihan sayuran
                        segar dan berkualitas untuk kebutuhan
                        sehari-hari.
                    </p>

                    <div class="social_icon">

                        <ul>

                            <li>
                                <a href="#">
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>

                            <li>
                                <a href="#">
                                    <i class="fa fa-instagram"></i>
                                </a>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


        </div>

    </div>

</div>

<!-- FOOTER END -->


<div class="copyright_section">

    <div class="container">

        <p class="copyright_text">

            © 2026 VegeBanua Mart. Semua Hak Dilindungi.

        </p>

    </div>

</div>



<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.0.0.min.js"></script>
<script src="js/plugin.js"></script>


</body>

</html>