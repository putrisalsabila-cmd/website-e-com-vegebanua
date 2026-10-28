<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tentang Kami - VegeBanua Mart</title>

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

        .search_icon {
            color: white;
            font-size: 17px;
            margin-left: 10px;
            cursor: pointer;
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


        /* ==============================
           ABOUT
        ============================== */

        .about_page_text {
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
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

                    <li class="nav-item active">

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

                  <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>

                     <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                              <i class="fa fa-dashboard"></i>
                              Admin Panel
                        </a>
                     </li>

                  <?php endif; ?>

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

<!-- HEADER END -->



<!-- ==========================================
     ABOUT
========================================== -->

<div class="container-fluid">

    <div class="layout_border">


        <div class="about_section layout_padding margin_bottom90">


            <div class="container">


                <!-- JUDUL -->

                <div class="row">

                    <div class="col-md-12">


                        <h1 class="about_taital">

                            Tentang Kami

                        </h1>


                        <p class="about_text about_page_text">

                            VegeBanua Mart menyediakan berbagai pilihan
                            sayuran segar dan berkualitas untuk memenuhi
                            kebutuhan sehari-hari.

                        </p>


                    </div>

                </div>


                <div class="about_section_2">
                </div>


                <!-- ISI -->

                <div class="row">


                    <!-- GAMBAR -->

                    <div class="col-md-6">


                        <div class="about_img">

                            <img src="images/about-img.png"
                                 alt="Tentang VegeBanua Mart">

                        </div>


                    </div>


                    <!-- TEKS -->

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

                            <a href="shop.php">

                                Lihat Produk

                            </a>

                        </div>


                    </div>


                </div>


            </div>

        </div>


    </div>

</div>

<!-- ABOUT END -->



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



            <!-- INFORMASI -->

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



            <!-- TENTANG VEGE BANUA -->

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



<!-- ==========================================
     JAVASCRIPT
========================================== -->

<script src="js/jquery.min.js"></script>

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.bundle.min.js"></script>

<script src="js/jquery-3.0.0.min.js"></script>

<script src="js/plugin.js"></script>


</body>

</html>