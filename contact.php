<?php
session_start();
require_once "config/database.php";

$pesan_sukses = "";
$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = trim($_POST["nama"] ?? "");
    $telepon = trim($_POST["telepon"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $isi_pesan = trim($_POST["pesan"] ?? "");

    if ($nama == "" || $telepon == "" || $email == "" || $isi_pesan == "") {

        $pesan_error = "Semua data wajib diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO messages (nama, telepon, email, pesan)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama,
            $telepon,
            $email,
            $isi_pesan
        );

        if (mysqli_stmt_execute($stmt)) {

            $pesan_sukses = "Pesan berhasil dikirim. Terima kasih sudah menghubungi VegeBanua Mart!";

        } else {

            $pesan_error = "Pesan gagal dikirim. Silakan coba lagi.";

        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title>Hubungi Kami - VegeBanua Mart</title>

      <meta name="keywords" content="VegeBanua Mart, kontak, hubungi kami">
      <meta name="description" content="Hubungi VegeBanua Mart untuk informasi dan kebutuhan sayuran Anda.">
      <meta name="author" content="VegeBanua Mart">

      <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="css/style.css">
      <link rel="stylesheet" href="css/responsive.css">

      <link rel="icon" href="images/fevicon.png" type="image/gif">

      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,800;1,400&family=Sen:wght@400;700;800&display=swap" rel="stylesheet">

      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">

      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

      <style>
         .contact_section {
            padding-top: 70px;
            padding-bottom: 80px;
         }

         .contact-title {
            text-align: center;
            color: #333;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
         }

         .contact-subtitle {
            text-align: center;
            color: #777;
            font-size: 16px;
            margin-bottom: 45px;
         }

         .contact-box {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
         }

         .mail_text,
         .massage-bt {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 13px 15px;
            margin-bottom: 15px;
            outline: none;
            font-size: 14px;
         }

         .mail_text:focus,
         .massage-bt:focus {
            border-color: #43a047;
         }

         .massage-bt {
            resize: vertical;
         }

         .send_bt a {
            display: inline-block;
            background: #43a047;
            color: white;
            padding: 11px 28px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
         }

         .send_bt a:hover {
            background: #2e7d32;
            color: white;
         }

         .map-box {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: 100%;
         }

         .map-responsive {
            height: 100%;
            min-height: 370px;
         }

         .map-responsive iframe {
            width: 100%;
            height: 100%;
            min-height: 370px;
            border: 0;
         }
      </style>
   </head>

   <body>

      <!-- HEADER -->
      <div class="header_section">
         <div class="container-fluid">

            <nav class="navbar navbar-expand-lg navbar-light">

               <a class="navbar-brand vege-logo" href="index.php">
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

               <div class="collapse navbar-collapse" id="navbarSupportedContent">

                  <ul class="navbar-nav ml-auto">

                     <li class="nav-item">
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

                              <a class="dropdown-item" href="pesanan.php">
                                 <i class="fa fa-file-text-o"></i>
                                 Pesanan Saya
                              </a>

                              <div class="dropdown-divider"></div>

                              <a class="dropdown-item" href="logout.php">
                                 <i class="fa fa-sign-out"></i>
                                 Logout
                              </a>

                           </div>

                        </li>

                     <?php else: ?>

                        <li class="nav-item">
                           <a class="nav-link" href="login.php">
                              Login
                           </a>
                        </li>

                        <li class="nav-item">
                           <a class="nav-link" href="register.php">
                              Daftar
                           </a>
                        </li>

                     <?php endif; ?>

                  </ul>

                  <form class="form-inline my-2 my-lg-0">
                     <div class="search_icon">
                        <i class="fa fa-search" aria-hidden="true"></i>
                     </div>
                  </form>

               </div>
            </nav>

         </div>
      </div>
      <!-- HEADER END -->


      <!-- CONTACT -->
      <div class="container-fluid">

         <div class="layout_border">

            <div class="contact_section">

               <div class="container">

                  <div class="row">

                     <div class="col-sm-12">

                        <h1 class="contact-title">
                           Hubungi Kami
                        </h1>

                        <p class="contact-subtitle">
                           Ada pertanyaan atau ingin mengetahui lebih lanjut?
                           Silakan hubungi VegeBanua Mart.
                        </p>

                     </div>

                  </div>


                  <div class="row">

                     <!-- FORM -->
                     <div class="col-md-6">

                       <div class="contact-box">

    <?php if ($pesan_sukses != ""): ?>

        <div style="
            background:#e9f8ef;
            color:#2e7d32;
            padding:14px 16px;
            border-radius:8px;
            margin-bottom:20px;
            font-size:14px;
        ">
            <i class="fa fa-check-circle"></i>
            <?= htmlspecialchars($pesan_sukses); ?>
        </div>

    <?php endif; ?>


    <?php if ($pesan_error != ""): ?>

        <div style="
            background:#ffeaea;
            color:#d9534f;
            padding:14px 16px;
            border-radius:8px;
            margin-bottom:20px;
            font-size:14px;
        ">
            <i class="fa fa-exclamation-circle"></i>
            <?= htmlspecialchars($pesan_error); ?>
        </div>

    <?php endif; ?>


    <form action="contact.php" method="POST">

        <input
            type="text"
            class="mail_text"
            placeholder="Nama"
            name="nama"
            required
        >

        <input
            type="text"
            class="mail_text"
            placeholder="Nomor Telepon"
            name="telepon"
            required
        >

        <input
            type="email"
            class="mail_text"
            placeholder="Email"
            name="email"
            required
        >

        <textarea
            class="massage-bt"
            placeholder="Pesan"
            rows="5"
            name="pesan"
            required
        ></textarea>


        <div class="send_bt">

            <button
                type="submit"
                style="
                    background:#43a047;
                    color:white;
                    border:none;
                    padding:11px 28px;
                    border-radius:7px;
                    font-weight:600;
                    cursor:pointer;
                "
            >
                <i class="fa fa-paper-plane"></i>
                KIRIM
            </button>

        </div>

    </form>

</div>

</div>

                     <!-- MAP -->
                     <div class="col-md-6">

                        <div class="map-box">

                           <div class="map-responsive">

                              <iframe
                                 src="https://www.google.com/maps?q=Banjarmasin,Kalimantan+Selatan&output=embed"
                                 allowfullscreen="">
                              </iframe>

                           </div>

                        </div>

                     </div>

                  </div>

               </div>

            </div>

         </div>

      </div>
      <!-- CONTACT END -->


      <!-- FOOTER -->
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


               <!-- KONTAK -->
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


      <!-- COPYRIGHT -->
      <div class="copyright_section">

         <div class="container">

            <p class="copyright_text">
               © 2026 VegeBanua Mart. Semua Hak Dilindungi.
            </p>

         </div>

      </div>
      <!-- COPYRIGHT END -->


      <!-- JAVASCRIPT -->
      <script src="js/jquery.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/plugin.js"></script>

   </body>
</html>