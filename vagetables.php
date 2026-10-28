<?php
session_start();
require_once "config/database.php";

/* =========================
   FILTER KATEGORI
========================= */

$kategori = isset($_GET["kategori"]) ? $_GET["kategori"] : "Semua";

$kategori_valid = [
    "Sayuran Hijau",
    "Sayuran Buah",
    "Sayuran Umbi"
];


/* =========================
   AMBIL DATA PRODUK
========================= */

if ($kategori == "Semua") {

    $query_sayuran = mysqli_query(
        $conn,
        "SELECT
            products.id,
            products.nama_produk,
            products.deskripsi,
            products.harga,
            products.stok,
            products.gambar,
            categories.nama_kategori
         FROM products
         INNER JOIN categories
            ON products.category_id = categories.id
         WHERE categories.nama_kategori IN (
            'Sayuran Hijau',
            'Sayuran Buah',
            'Sayuran Umbi'
         )
         ORDER BY categories.id ASC, products.nama_produk ASC"
    );

} elseif (in_array($kategori, $kategori_valid)) {

    $kategori_safe = mysqli_real_escape_string($conn, $kategori);

    $query_sayuran = mysqli_query(
        $conn,
        "SELECT
            products.id,
            products.nama_produk,
            products.deskripsi,
            products.harga,
            products.stok,
            products.gambar,
            categories.nama_kategori
         FROM products
         INNER JOIN categories
            ON products.category_id = categories.id
         WHERE categories.nama_kategori = '$kategori_safe'
         ORDER BY products.nama_produk ASC"
    );

} else {

    $kategori = "Semua";

    $query_sayuran = mysqli_query(
        $conn,
        "SELECT
            products.id,
            products.nama_produk,
            products.deskripsi,
            products.harga,
            products.stok,
            products.gambar,
            categories.nama_kategori
         FROM products
         INNER JOIN categories
            ON products.category_id = categories.id
         WHERE categories.nama_kategori IN (
            'Sayuran Hijau',
            'Sayuran Buah',
            'Sayuran Umbi'
         )
         ORDER BY categories.id ASC, products.nama_produk ASC"
    );
}


if (!$query_sayuran) {
    die("Data sayuran gagal diambil: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Sayuran - VegeBanua Mart</title>

    <meta name="keywords"
          content="sayuran, sayuran segar, VegeBanua Mart">

    <meta name="description"
          content="Pilihan sayuran segar dan berkualitas dari VegeBanua Mart">

    <meta name="author"
          content="VegeBanua Mart">


    <!-- BOOTSTRAP -->

    <link rel="stylesheet"
          type="text/css"
          href="css/bootstrap.min.css">

    <link rel="stylesheet"
          type="text/css"
          href="css/style.css">

    <link rel="stylesheet"
          href="css/responsive.css">


    <!-- FONT AWESOME -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- GOOGLE FONT -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,800;1,400&family=Sen:wght@400;700;800&display=swap"
          rel="stylesheet">


    <link rel="stylesheet"
          href="css/jquery.mCustomScrollbar.min.css">


    <style>

        /* =========================
           SAYURAN
        ========================= */

        .vagetables_section {
            padding-top: 70px;
            padding-bottom: 80px;
        }


        .vagetables_taital {
            text-align: center;
            color: #333;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }


        .vagetables_text {
            text-align: center;
            color: #777;
            font-size: 16px;
            margin-bottom: 30px;
        }


        /* =========================
           FILTER KATEGORI
        ========================= */

.category-filter {
    width: 100%;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 12px;
    margin: 0 auto 45px auto;
    text-align: center;
}

.category-filter a {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: auto !important;
    min-width: 120px;
    padding: 10px 22px;
    border: 1px solid #43a047;
    border-radius: 25px;
    color: #43a047;
    background: white;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin: 0 !important;
    float: none !important;
}

.category-filter a:hover,
.category-filter a.active {
    background: #43a047;
    color: white;
    text-decoration: none;
}

@media (max-width: 576px) {

    .category-filter {
        gap: 8px;
        padding: 0 10px;
    }

    .category-filter a {
        min-width: auto;
        padding: 8px 15px;
        font-size: 12px;
    }

}


        /* =========================
           CARD PRODUK
        ========================= */

        .vegetable-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 35px;
            padding-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }


        .vegetable-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.14);
        }


        .vegetable-image {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }


        /* =========================
           KATEGORI PRODUK
        ========================= */

        .vegetable-category {
            text-align: center;
            color: #43a047;
            font-size: 11px;
            font-weight: 600;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }


        /* =========================
           NAMA PRODUK
        ========================= */

        .vegetable-name {
            text-align: center;
            color: #333;
            font-size: 22px;
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 5px;
        }


        /* =========================
           HARGA
        ========================= */

        .vegetable-price {
            text-align: center;
            color: #43a047;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
        }


        /* =========================
           TOMBOL PRODUK
        ========================= */

        .vegetable-button {
            text-align: center;
        }


        .vegetable-button a {
            display: inline-block;
            background: #43a047;
            color: white;
            padding: 9px 20px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            transition: 0.3s;
        }


        .vegetable-button a:hover {
            background: #2e7d32;
            color: white;
            text-decoration: none;
        }


        /* =========================
           PRODUK KOSONG
        ========================= */

        .empty-vegetable {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }


        .empty-vegetable i {
            font-size: 45px;
            color: #43a047;
            margin-bottom: 15px;
        }


        .empty-vegetable h3 {
            font-size: 20px;
            color: #555;
            margin-bottom: 8px;
        }


        .empty-vegetable p {
            font-size: 13px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 576px) {

            .category-filter {
                gap: 8px;
            }


            .category-filter a {
                padding: 8px 15px;
                font-size: 12px;
            }


            .vagetables_taital {
                font-size: 28px;
            }


            .vagetables_text {
                font-size: 14px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<div class="header_section">

    <div class="container-fluid">

        <nav class="navbar navbar-expand-lg navbar-light">


            <!-- LOGO -->

            <a class="navbar-brand vege-logo"
               href="index.php">

                VegeBanua Mart

            </a>


            <!-- MOBILE BUTTON -->

            <button class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <div class="collapse navbar-collapse"
                 id="navbarSupportedContent">


                <ul class="navbar-nav ml-auto">


                    <!-- BERANDA -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="index.php">

                            Beranda

                        </a>

                    </li>


                    <!-- BELANJA -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="shop.php">

                            Belanja

                        </a>

                    </li>


                    <!-- TENTANG KAMI -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="about.php">

                            Tentang Kami

                        </a>

                    </li>


                    <!-- SAYURAN -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="vagetables.php">

                            Sayuran

                        </a>

                    </li>


                    <!-- BLOG -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="blog.php">

                            Blog

                        </a>

                    </li>


                    <!-- HUBUNGI KAMI -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="contact.php">

                            Hubungi Kami

                        </a>

                    </li>


                    <?php if (isset($_SESSION["user_id"])): ?>


                        <!-- KERANJANG -->

                        <li class="nav-item">

                            <a class="nav-link"
                               href="cart.php"
                               title="Keranjang">

                                <i class="fa fa-shopping-cart"></i>

                            </a>

                        </li>


                        <!-- AKUN -->

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


                        <!-- LOGIN -->

                        <li class="nav-item">

                            <a class="nav-link"
                               href="login.php">

                                Login

                            </a>

                        </li>


                        <!-- DAFTAR -->

                        <li class="nav-item">

                            <a class="nav-link"
                               href="register.php">

                                Daftar

                            </a>

                        </li>


                    <?php endif; ?>


                </ul>


                <!-- SEARCH -->

                <form class="form-inline my-2 my-lg-0">

                    <div class="search_icon">

                        <i class="fa fa-search"
                           aria-hidden="true"></i>

                    </div>

                </form>


            </div>

        </nav>

    </div>

</div>


<!-- =========================
     SAYURAN
========================= -->

<div class="container-fluid">

    <div class="layout_border">

        <div class="vagetables_section">


            <div class="container">


                <!-- JUDUL -->

                <div class="row">

                    <div class="col-sm-12">


                        <h1 class="vagetables_taital">

                            Pilihan Sayuran Kami

                        </h1>


                        <p class="vagetables_text">

                            Berbagai pilihan sayuran segar dan berkualitas
                            untuk kebutuhan Anda.

                        </p>


                        <!-- =========================
                             FILTER KATEGORI
                        ========================= -->

                        <div class="category-filter">


                            <!-- SEMUA -->

                            <a href="vagetables.php"
                               class="<?= $kategori == 'Semua' ? 'active' : ''; ?>">

                                Semua

                            </a>


                            <!-- SAYURAN HIJAU -->

                            <a href="vagetables.php?kategori=Sayuran%20Hijau"
                               class="<?= $kategori == 'Sayuran Hijau' ? 'active' : ''; ?>">

                                Sayuran Hijau

                            </a>


                            <!-- SAYURAN BUAH -->

                            <a href="vagetables.php?kategori=Sayuran%20Buah"
                               class="<?= $kategori == 'Sayuran Buah' ? 'active' : ''; ?>">

                                Sayuran Buah

                            </a>


                            <!-- SAYURAN UMBI -->

                            <a href="vagetables.php?kategori=Sayuran%20Umbi"
                               class="<?= $kategori == 'Sayuran Umbi' ? 'active' : ''; ?>">

                                Sayuran Umbi

                            </a>


                        </div>


                    </div>

                </div>


                <!-- =========================
                     PRODUK
                ========================= -->

                <?php if (mysqli_num_rows($query_sayuran) > 0): ?>


                    <div class="row">


                        <?php while ($produk = mysqli_fetch_assoc($query_sayuran)): ?>


                            <div class="col-md-4">


                                <div class="vegetable-card">


                                    <!-- GAMBAR -->

                                    <img
                                        src="images/<?= htmlspecialchars($produk["gambar"]); ?>"
                                        class="vegetable-image"
                                        alt="<?= htmlspecialchars($produk["nama_produk"]); ?>"
                                    >


                                    <!-- KATEGORI -->

                                    <div class="vegetable-category">

                                        <?= htmlspecialchars($produk["nama_kategori"]); ?>

                                    </div>


                                    <!-- NAMA -->

                                    <h3 class="vegetable-name">

                                        <?= htmlspecialchars($produk["nama_produk"]); ?>

                                    </h3>


                                    <!-- HARGA -->

                                    <h3 class="vegetable-price">

                                        Rp<?= number_format(
                                            $produk["harga"],
                                            0,
                                            ",",
                                            "."
                                        ); ?>

                                    </h3>


                                    <!-- TOMBOL -->

                                    <div class="vegetable-button">

                                 <a href="shop.php">
                                    <i class="fa fa-shopping-basket"></i>
                                    Lihat Produk
                                 </a>

                                    </div>


                                </div>

                            </div>


                        <?php endwhile; ?>


                    </div>


                <?php else: ?>


                    <!-- JIKA TIDAK ADA PRODUK -->

                    <div class="empty-vegetable">


                        <i class="fa fa-leaf"></i>


                        <h3>

                            Belum Ada Produk

                        </h3>


                        <p>

                            Belum ada produk dalam kategori ini.

                        </p>


                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>

</div>


<!-- =========================
     FOOTER
========================= -->

<div class="footer_section layout_padding">


    <div class="container">


        <div class="row">


            <!-- MENU -->

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


            <!-- HUBUNGI -->

            <div class="col-lg-4 col-sm-6">


                <h3 class="footer_text">

                    Hubungi Kami

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

                                0812-3456-7890

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


            <!-- SOSIAL MEDIA -->

            <div class="col-lg-4 col-sm-6">


                <div class="footer_main">


                    <h3 class="footer_text">

                        VegeBanua Mart

                    </h3>


                    <p class="dummy_text">

                        Menyediakan sayuran segar dan berkualitas
                        untuk kebutuhan sehari-hari.

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

                                    <i class="fa fa-twitter"></i>

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


<!-- =========================
     COPYRIGHT
========================= -->

<div class="copyright_section">

    <div class="container">

        <p class="copyright_text">

            © 2026 VegeBanua Mart. Semua Hak Dilindungi.

        </p>

    </div>

</div>


<!-- =========================
     JAVASCRIPT
========================= -->

<script src="js/jquery.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.bundle.min.js"></script>

<script src="js/plugin.js"></script>


</body>

</html>