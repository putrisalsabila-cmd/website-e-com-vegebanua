<?php
session_start();
require_once "config/database.php";

/* =========================
   AMBIL TESTIMONI TERBARU
========================= */

$query_reviews = mysqli_query(
    $conn,
    "SELECT
        reviews.rating,
        reviews.komentar,
        reviews.created_at,
        users.nama,
        products.nama_produk
     FROM reviews
     INNER JOIN users
        ON reviews.user_id = users.id
     INNER JOIN products
        ON reviews.product_id = products.id
     ORDER BY reviews.created_at DESC
     LIMIT 5"
);

$reviews = [];

while ($review = mysqli_fetch_assoc($query_reviews)) {
    $reviews[] = $review;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>VegeBanua Mart | Belanja Sayuran Segar</title>

    <meta name="description"
          content="VegeBanua Mart menyediakan berbagai pilihan sayuran segar dan berkualitas.">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <link rel="icon" href="images/fevicon.png" type="image/gif">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,800;1,400&family=Sen:wght@400;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="css/jquery.mCustomScrollbar.min.css">

    <link rel="stylesheet"
          href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

    <style>

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

.search_icon {
    color: white;
    font-size: 17px;
    margin-left: 10px;
    cursor: pointer;
}

@media (max-width: 1200px) {
    .navbar-brand.vege-logo {
        font-size: 21px;
        margin-right: 10px;
    }

    .navbar .nav-link {
        font-size: 12px;
        padding: 8px 6px !important;
    }
}
    </style>

</head>


<body>


<!-- HEADER -->
<div class="header_section">

    <div class="container-fluid">

        <nav class="navbar navbar-expand-lg navbar-light">

            <a class="navbar-brand vege-logo"
               href="index.php">
                VegeBanua Mart
            </a>

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

                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">
                            Beranda
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">
                            Belanja
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            Tentang Kami
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="vagetables.php">
                            Sayuran
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">
                            Blog
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
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
                            <a class="nav-link" href="login.php">
                                Login
                            </a>
                        </li>

                        <!-- DAFTAR -->
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
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
<!-- HEADER END -->



<!-- LAYOUT -->
<div class="container-fluid">

    <div class="layout_border">



        <!-- BANNER -->
        <div class="banner_section layout_padding">

            <div class="container-fluid">

                <div id="main_slider"
                     class="carousel slide"
                     data-ride="carousel">


                    <div class="carousel-inner">


                        <div class="carousel-item active">

                            <div class="row">


                                <div class="col-sm-6">

                                    <div class="banner_taital_main">

                                        <h1 class="banner_taital">

                                            Sayuran Segar untuk Keluarga

                                        </h1>


                                        <p class="banner_text">

                                            Temukan berbagai pilihan sayuran
                                            segar dan berkualitas untuk
                                            kebutuhan sehari-hari.

                                        </p>


                                        <div class="btn_main">

                                            <div class="started_text">

                                                <a href="shop.php">

                                                    Belanja Sekarang

                                                </a>

                                            </div>


                                            <div class="started_text active">

                                                <a href="contact.php">

                                                    Hubungi Kami

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <div class="col-sm-6">

                                    <div class="banner_img">

                                        <img src="images/banner-img.png"
                                             alt="Sayuran segar">

                                    </div>

                                </div>


                            </div>

                        </div>


                        <div class="carousel-item">

                            <div class="row">


                                <div class="col-sm-6">

                                    <div class="banner_taital_main">

                                        <h1 class="banner_taital">

                                            Belanja Sayur Lebih Mudah

                                        </h1>


                                        <p class="banner_text">

                                            Pilih sayuran yang kamu butuhkan
                                            dengan mudah melalui VegeBanua Mart.

                                        </p>


                                        <div class="btn_main">

                                            <div class="started_text">

                                                <a href="shop.php">

                                                    Belanja Sekarang

                                                </a>

                                            </div>


                                            <div class="started_text active">

                                                <a href="about.php">

                                                    Tentang Kami

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <div class="col-sm-6">

                                    <div class="banner_img">

                                        <img src="images/banner-img.png"
                                             alt="Belanja sayuran">

                                    </div>

                                </div>


                            </div>

                        </div>


                        <div class="carousel-item">

                            <div class="row">


                                <div class="col-sm-6">

                                    <div class="banner_taital_main">

                                        <h1 class="banner_taital">

                                            Segar, Berkualitas, dan Terpercaya

                                        </h1>


                                        <p class="banner_text">

                                            Kami menyediakan berbagai pilihan
                                            sayuran untuk kebutuhan rumah tangga.

                                        </p>


                                        <div class="btn_main">

                                            <div class="started_text">

                                                <a href="shop.php">

                                                    Lihat Produk

                                                </a>

                                            </div>


                                            <div class="started_text active">

                                                <a href="contact.php">

                                                    Hubungi Kami

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <div class="col-sm-6">

                                    <div class="banner_img">

                                        <img src="images/banner-img.png"
                                             alt="Sayuran berkualitas">

                                    </div>

                                </div>


                            </div>

                        </div>


                    </div>


                    <a class="carousel-control-prev"
                       href="#main_slider"
                       role="button"
                       data-slide="prev">

                        <img src="images/arrow-left.png"
                             alt="Sebelumnya">

                    </a>


                    <a class="carousel-control-next"
                       href="#main_slider"
                       role="button"
                       data-slide="next">

                        <img src="images/arrow-right.png"
                             alt="Berikutnya">

                    </a>


                </div>

            </div>

        </div>
        <!-- BANNER END -->



        <!-- ABOUT -->
        <div class="about_section layout_padding">

            <div class="container">


                <div class="row">

                    <div class="col-md-12">

                        <h1 class="about_taital">

                            Tentang Kami

                        </h1>


                        <p class="about_text">

                            VegeBanua Mart menyediakan berbagai pilihan
                            sayuran segar dan berkualitas untuk kebutuhan
                            sehari-hari.

                        </p>

                    </div>

                </div>


                <div class="about_section_2">
                </div>


                <div class="row">


                    <div class="col-md-6">

                        <div class="about_img">

                            <img src="images/about-img.png"
                                 alt="Tentang VegeBanua Mart">

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="fresh_taital">

                            Segar dan Berkualitas

                        </div>


                        <p class="ipsum_text">

                            VegeBanua Mart hadir untuk memudahkan masyarakat
                            mendapatkan sayuran segar dengan kualitas terbaik.
                            Kami menyediakan berbagai pilihan sayuran untuk
                            memenuhi kebutuhan rumah tangga sehari-hari.

                        </p>


                        <div class="read_bt">

                            <a href="about.php">

                                Selengkapnya

                            </a>

                        </div>

                    </div>


                </div>

            </div>

        </div>
        <!-- ABOUT END -->



        <!-- PRODUK -->
        <div class="vagetables_section layout_padding">

            <div class="container">


                <div class="row">

                    <div class="col-sm-12">

                        <h1 class="vagetables_taital">

                            Pilihan Sayuran Kami

                        </h1>


                        <p class="vagetables_text">

                            Berbagai pilihan sayuran segar dan berkualitas
                            untuk kebutuhan Anda.

                        </p>

                    </div>

                </div>


                <div class="courses_section_2">

                    <div class="row">


                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/bayam.jpg"
                                         alt="Bayam">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Bayam

                            </h3>


                            <h3 class="rate_text">

                                Rp5.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>



                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/img-1.png"
                                         alt="Wortel">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Wortel

                            </h3>


                            <h3 class="rate_text">

                                Rp8.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>



                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/img-3.png"
                                         alt="Tomat">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Tomat

                            </h3>


                            <h3 class="rate_text">

                                Rp10.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>


                    </div>

                </div>


                <div class="vagetables_section_2">

                    <div class="row">


                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/kangkung.jpg"
                                         alt="Kangkung">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Kangkung

                            </h3>


                            <h3 class="rate_text">

                                Rp5.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>



                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/sawi.jpeg"
                                         alt="Sawi">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Sawi

                            </h3>


                            <h3 class="rate_text">

                                Rp7.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>



                        <div class="col-md-4">

                            <div class="hover01 column">

                                <figure>

                                    <img src="images/timun.jpg"
                                         alt="Timun">

                                </figure>

                            </div>


                            <h3 class="harshal_text">

                                Timun

                            </h3>


                            <h3 class="rate_text">

                                Rp8.000

                            </h3>


                            <div class="read_bt_1">

                                <a href="shop.php">

                                    Lihat Produk

                                </a>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </div>
        <!-- PRODUK END -->



        <!-- CONTACT -->
        <div class="contact_section layout_padding">

            <div class="container">


                <div class="row">

                    <div class="col-sm-12">

                        <h1 class="contact_taital">

                            Hubungi Kami

                        </h1>

                    </div>

                </div>


                <div class="contact_section_2">

                    <div class="row">


                        <div class="col-md-6">

                            <div class="mail_section_1">

                                <input type="text"
                                       class="mail_text"
                                       placeholder="Nama"
                                       name="Nama">


                                <input type="text"
                                       class="mail_text"
                                       placeholder="Nomor Telepon"
                                       name="Nomor Telepon">


                                <input type="text"
                                       class="mail_text"
                                       placeholder="Email"
                                       name="Email">


                                <textarea class="massage-bt"
                                          placeholder="Pesan"
                                          rows="5"
                                          name="Pesan"></textarea>


                                <div class="send_bt">

                                    <a href="contact.php">

                                        KIRIM

                                    </a>

                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="map_main">

                                <div class="map-responsive">

                                    <iframe
                                        src="https://www.google.com/maps?q=Banjarmasin,Kalimantan+Selatan&output=embed"
                                        width="600"
                                        height="340"
                                        frameborder="0"
                                        style="border:0; width:100%;"
                                        allowfullscreen="">
                                    </iframe>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </div>
        <!-- CONTACT END -->



 <!-- TESTIMONI -->

<div class="testimonial_section layout_padding">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1 class="testimonial_taital">
                    Testimoni Pelanggan
                </h1>

                <p style="
                    text-align:center;
                    color:#888;
                    font-size:13px;
                    margin-bottom:30px;
                ">
                    Pengalaman pelanggan setelah berbelanja di VegeBanua Mart
                </p>

            </div>

        </div>


        <div class="testimonial_section_2">

            <div class="row">

                <div class="col-md-12">

                    <div class="testimonial_box">


                        <?php if (count($reviews) > 0): ?>


                            <div id="testimonialSlider"
                                 class="carousel slide"
                                 data-ride="carousel">


                                <!-- INDICATOR -->

                                <ol class="carousel-indicators">

                                    <?php foreach ($reviews as $index => $review): ?>

                                        <li
                                            data-target="#testimonialSlider"
                                            data-slide-to="<?= $index; ?>"
                                            class="<?= $index == 0 ? 'active' : ''; ?>">
                                        </li>

                                    <?php endforeach; ?>

                                </ol>


                                <!-- TESTIMONI -->

                                <div class="carousel-inner">


                                    <?php foreach ($reviews as $index => $review): ?>


                                        <div class="carousel-item <?= $index == 0 ? 'active' : ''; ?>">


                                            <!-- BINTANG -->

                                            <div style="
                                                text-align:center;
                                                margin-bottom:15px;
                                            ">

                                                <?php for ($i = 1; $i <= 5; $i++): ?>

                                                    <?php if ($i <= $review["rating"]): ?>

                                                        <span style="
                                                            color:#ffc107;
                                                            font-size:22px;
                                                        ">
                                                            ★
                                                        </span>

                                                    <?php else: ?>

                                                        <span style="
                                                            color:#ddd;
                                                            font-size:22px;
                                                        ">
                                                            ★
                                                        </span>

                                                    <?php endif; ?>

                                                <?php endfor; ?>

                                            </div>


                                            <!-- KOMENTAR -->

                                            <p class="testimonial_text">

                                                "<?= htmlspecialchars($review["komentar"]); ?>"

                                            </p>


                                            <!-- PRODUK -->

                                            <p style="
                                                text-align:center;
                                                color:#888;
                                                font-size:12px;
                                                margin-top:15px;
                                                margin-bottom:0;
                                            ">

                                                Produk:
                                                <?= htmlspecialchars($review["nama_produk"]); ?>

                                            </p>


                                        </div>


                                    <?php endforeach; ?>


                                </div>


                                <!-- PANAH -->

                                <?php if (count($reviews) > 1): ?>

                                    <a class="carousel-control-prev"
                                       href="#testimonialSlider"
                                       role="button"
                                       data-slide="prev">

                                        <span class="carousel-control-prev-icon"></span>

                                    </a>


                                    <a class="carousel-control-next"
                                       href="#testimonialSlider"
                                       role="button"
                                       data-slide="next">

                                        <span class="carousel-control-next-icon"></span>

                                    </a>

                                <?php endif; ?>


                            </div>


                        <?php else: ?>


                            <!-- BELUM ADA TESTIMONI -->

                            <div style="
                                text-align:center;
                                padding:45px 20px;
                            ">

                                <div style="
                                    font-size:45px;
                                    color:#43a047;
                                    margin-bottom:15px;
                                ">

                                    <i class="fa fa-comments-o"></i>

                                </div>


                                <h4 style="
                                    font-size:18px;
                                    font-weight:600;
                                    margin-bottom:8px;
                                ">

                                    Belum Ada Testimoni

                                </h4>


                                <p style="
                                    color:#888;
                                    font-size:13px;
                                    margin:0;
                                ">

                                    Jadilah pelanggan pertama yang memberikan
                                    ulasan untuk VegeBanua Mart.

                                </p>

                            </div>


                        <?php endif; ?>


                    </div>


                    <?php if (count($reviews) > 0): ?>


                        <!-- NAMA CUSTOMER -->

                        <div class="client_main">

                            <div class="client_left">

                                <div class="client_img">

                                    <div style="
                                        width:65px;
                                        height:65px;
                                        border-radius:50%;
                                        background:#e9f9ef;
                                        color:#43a047;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:28px;
                                    ">

                                        <i class="fa fa-user"></i>

                                    </div>

                                </div>

                            </div>


                            <div class="client_right">

                                <h4 class="client_name">

                                    <?= htmlspecialchars($reviews[0]["nama"]); ?>

                                </h4>


                                <p class="customer_text">

                                    Customer

                                </p>

                            </div>

                        </div>


                    <?php endif; ?>


                </div>

            </div>

        </div>

    </div>

</div>

<!-- TESTIMONI END -->


    </div>

</div>
<!-- LAYOUT END -->



<!-- FOOTER -->
<div class="footer_section layout_padding">

    <div class="container">

        <div class="row">


            <div class="col-lg-4 col-sm-6">

                <h3 class="footer_text">

                    Tautan

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

                                Banjarmasin,
                                Kalimantan Selatan

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

                        Temukan Kami

                    </h3>


                    <p class="dummy_text">

                        Ikuti informasi dan perkembangan
                        VegeBanua Mart melalui media sosial kami.

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



<!-- COPYRIGHT -->
<div class="copyright_section">

    <div class="container">

        <p class="copyright_text">

            © 2026 VegeBanua Mart.
            Semua Hak Dilindungi.

        </p>

    </div>

</div>
<!-- COPYRIGHT END -->



<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.0.0.min.js"></script>
<script src="js/plugin.js"></script>

</body>

</html>

