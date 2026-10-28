<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blog - VegeBanua Mart</title>

    <meta name="keywords" content="sayuran, tips sayuran, VegeBanua Mart">
    <meta name="description" content="Informasi dan tips seputar sayuran segar dari VegeBanua Mart">
    <meta name="author" content="VegeBanua Mart">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <link rel="icon" href="images/fevicon.png" type="image/gif">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,800;1,400&family=Sen:wght@400;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">

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
        .navbar .fa-shopping-cart,
        .navbar .fa-dashboard {
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


        /* ==============================
           BLOG
        ============================== */

        .blog_section {
            padding-top: 70px;
            padding-bottom: 80px;
        }

        .blog-title {
            text-align: center;
            color: #333;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .blog-subtitle {
            text-align: center;
            color: #777;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 50px;
        }

        .blog-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.13);
        }

        .blog-image {
            width: 100%;
            height: 240px;
            object-fit: cover;
        }

        .blog-content {
            padding: 25px 28px 28px;
        }

        .blog-category {
            display: inline-block;
            color: #43a047;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .blog-content h3 {
            color: #333;
            font-size: 21px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 14px;
        }

        .blog-content p {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 12px;
        }

        .blog-button {
            margin-top: 20px;
        }

        .blog-button a {
            display: inline-block;
            background: #43a047;
            color: white;
            padding: 9px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .blog-button a:hover {
            background: #2e7d32;
            color: white;
        }

        .blog-button i {
            margin-left: 5px;
        }


        /* ==============================
           BLOG PENUTUP
        ============================== */

        .blog-info {
            margin-top: 45px;
            padding: 40px;
            text-align: center;
            background: #f1fff5;
            border-radius: 15px;
        }

        .blog-info h3 {
            color: #333;
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .blog-info p {
            max-width: 700px;
            margin: 0 auto 22px;
            color: #666;
            font-size: 14px;
            line-height: 1.8;
        }

        .blog-main-button {
            display: inline-block;
            background: #00a94f;
            color: white;
            padding: 11px 24px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .blog-main-button:hover {
            background: #008f45;
            color: white;
            text-decoration: none;
        }

        .blog-main-button i {
            margin-left: 6px;
        }


        /* ==============================
           RESPONSIVE
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

            .search_icon {
                margin: 8px 10px;
            }

        }


        @media (max-width: 767px) {

            .blog_section {
                padding-top: 45px;
                padding-bottom: 60px;
            }

            .blog-title {
                font-size: 28px;
            }

            .blog-subtitle {
                font-size: 14px;
                margin-bottom: 30px;
            }

            .blog-image {
                height: 210px;
            }

            .blog-content {
                padding: 22px;
            }

            .blog-info {
                padding: 30px 20px;
            }

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


                    <li class="nav-item">

                        <a class="nav-link"
                           href="index.php">

                            Beranda

                        </a>

                    </li>


                    <li class="nav-item">

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


                    <li class="nav-item active">

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


                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>

                        <li class="nav-item">

                            <a class="nav-link"
                               href="admin.php">

                                <i class="fa fa-dashboard"></i>
                                Admin Panel

                            </a>

                        </li>

                    <?php endif; ?>


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


<!-- ==========================================
     BLOG
========================================== -->

<div class="container-fluid">

    <div class="layout_border">

        <div class="blog_section">

            <div class="container">


                <!-- JUDUL -->

                <div class="row">

                    <div class="col-sm-12">

                        <h1 class="blog-title">

                            Blog VegeBanua Mart

                        </h1>


                        <p class="blog-subtitle">

                            Informasi, tips, dan inspirasi seputar sayuran
                            untuk membantu menjaga pola hidup sehat.

                        </p>

                    </div>

                </div>


                <!-- BARIS 1 -->

                <div class="row mb-4">


                    <!-- ARTIKEL 1 -->

                    <div class="col-md-6 mb-4">

                        <div class="blog-card">

                            <img src="images/img-3.png"
                                 class="blog-image"
                                 alt="Tips memilih sayuran segar">

                            <div class="blog-content">

                                <span class="blog-category">

                                    Tips Sayuran

                                </span>


                                <h3>

                                    Tips Memilih Sayuran Segar

                                </h3>


                                <p>

                                    Memilih sayuran yang segar menjadi salah
                                    satu hal penting sebelum berbelanja.
                                    Perhatikan warna sayuran, teksturnya,
                                    serta kondisi daun dan batangnya.

                                </p>


                                <p>

                                    Sayuran yang masih segar biasanya memiliki
                                    warna alami, tidak terlalu layu, dan tidak
                                    menunjukkan tanda kerusakan.

                                </p>


                                <div class="blog-button">

                                    <a href="shop.php">

                                        Lihat Sayuran

                                        <i class="fa fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ARTIKEL 2 -->

                    <div class="col-md-6 mb-4">

                        <div class="blog-card">

                            <img src="images/bayam.jpg"
                                 class="blog-image"
                                 alt="Cara menyimpan sayuran">

                            <div class="blog-content">

                                <span class="blog-category">

                                    Tips Penyimpanan

                                </span>


                                <h3>

                                    Cara Menyimpan Sayuran Agar Tetap Segar

                                </h3>


                                <p>

                                    Setelah membeli sayuran, penyimpanan yang
                                    tepat dapat membantu menjaga kualitasnya
                                    agar tetap segar lebih lama.

                                </p>


                                <p>

                                    Bersihkan bagian yang kotor, pisahkan
                                    sayuran yang mudah rusak, lalu simpan di
                                    tempat yang sesuai seperti lemari pendingin.

                                </p>


                                <div class="blog-button">

                                    <a href="shop.php">

                                        Belanja Sekarang

                                        <i class="fa fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>


                <!-- BARIS 2 -->

                <div class="row mb-4">


                    <!-- ARTIKEL 3 -->

                    <div class="col-md-6 mb-4">

                        <div class="blog-card">

                            <img src="images/img-1.png"
                                 class="blog-image"
                                 alt="Manfaat mengonsumsi sayuran">

                            <div class="blog-content">

                                <span class="blog-category">

                                    Hidup Sehat

                                </span>


                                <h3>

                                    Manfaat Mengonsumsi Sayuran Setiap Hari

                                </h3>


                                <p>

                                    Sayuran merupakan salah satu bahan makanan
                                    yang dapat melengkapi kebutuhan nutrisi
                                    dalam menu sehari-hari.

                                </p>


                                <p>

                                    Beragam jenis sayuran dapat diolah menjadi
                                    menu yang berbeda sehingga makanan menjadi
                                    lebih bervariasi dan menarik untuk dinikmati.

                                </p>


                                <div class="blog-button">

                                    <a href="shop.php">

                                        Pilih Sayuran

                                        <i class="fa fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ARTIKEL 4 -->

                    <div class="col-md-6 mb-4">

                        <div class="blog-card">

                            <img src="images/timun.jpg"
                                 class="blog-image"
                                 alt="Ide olahan sayuran sederhana">

                            <div class="blog-content">

                                <span class="blog-category">

                                    Inspirasi Masak

                                </span>


                                <h3>

                                    Ide Olahan Sayuran Sederhana

                                </h3>


                                <p>

                                    Tidak perlu membuat masakan yang rumit
                                    untuk menikmati sayuran. Berbagai sayuran
                                    dapat diolah menjadi menu sederhana.

                                </p>


                                <p>

                                    Tumis sayuran, sup, sayur bening, hingga
                                    lalapan dapat menjadi pilihan praktis
                                    untuk menu sehari-hari.

                                </p>


                                <div class="blog-button">

                                    <a href="shop.php">

                                        Lihat Produk

                                        <i class="fa fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>


                <!-- PENUTUP -->

                <div class="blog-info">

                    <h3>

                        Temukan Sayuran Favoritmu

                    </h3>


                    <p>

                        VegeBanua Mart menyediakan berbagai pilihan sayuran
                        yang dapat digunakan untuk kebutuhan masak sehari-hari.
                        Yuk, pilih sayuran favoritmu dan mulai belanja!

                    </p>


                    <a href="shop.php"
                       class="blog-main-button">

                        Mulai Belanja

                        <i class="fa fa-shopping-cart"></i>

                    </a>

                </div>


            </div>

        </div>

    </div>

</div>

<!-- BLOG END -->


<!-- ==========================================
     FOOTER
========================================== -->

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


            <!-- HUBUNGI KAMI -->

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

<!-- FOOTER END -->


<!-- ==========================================
     COPYRIGHT
========================================== -->

<div class="copyright_section">

    <div class="container">

        <p class="copyright_text">

            © 2026 VegeBanua Mart.
            Semua Hak Dilindungi.

        </p>

    </div>

</div>


<!-- JAVASCRIPT -->

<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/plugin.js"></script>

</body>

</html>