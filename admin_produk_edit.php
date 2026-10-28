<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}


/* =========================
   CEK ID PRODUK
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: admin_produk.php");
    exit;
}

$id = (int) $_GET["id"];


/* =========================
   AMBIL DATA PRODUK
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM products WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$produk = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$produk) {
    header("Location: admin_produk.php");
    exit;
}


/* =========================
   DATA KATEGORI
========================= */

$query_kategori = mysqli_query(
    $conn,
    "SELECT * FROM categories
     ORDER BY nama_kategori ASC"
);


/* =========================
   PROSES UPDATE
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama_produk = trim($_POST["nama_produk"]);
    $category_id = (int) $_POST["category_id"];
    $deskripsi = trim($_POST["deskripsi"]);
    $harga = (float) $_POST["harga"];
    $stok = (int) $_POST["stok"];

    if (
        empty($nama_produk) ||
        $category_id <= 0 ||
        $harga < 0 ||
        $stok < 0
    ) {
        die("Data produk tidak valid.");
    }


    /* =========================
       CEK APAKAH GANTI GAMBAR
    ========================= */

    $nama_file_baru = $produk["gambar"];

    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"] != UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES["gambar"];

        if ($file["error"] != 0) {
            die("Gagal mengupload gambar.");
        }


        $allowed = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        $extension = strtolower(
            pathinfo(
                $file["name"],
                PATHINFO_EXTENSION
            )
        );


        if (!in_array($extension, $allowed)) {
            die("Format gambar tidak didukung.");
        }


        if ($file["size"] > 2 * 1024 * 1024) {
            die("Ukuran gambar maksimal 2 MB.");
        }


        $nama_file_baru =
            time()
            . "_"
            . uniqid()
            . "."
            . $extension;


        $target = "images/" . $nama_file_baru;


        if (!move_uploaded_file(
            $file["tmp_name"],
            $target
        )) {
            die("Gagal menyimpan gambar baru.");
        }


        /* Hapus gambar lama */

        $gambar_lama = "images/" . $produk["gambar"];

        if (
            !empty($produk["gambar"]) &&
            file_exists($gambar_lama)
        ) {
            unlink($gambar_lama);
        }
    }


    /* =========================
       UPDATE DATABASE
    ========================= */

    $stmt_update = mysqli_prepare(
        $conn,
        "UPDATE products
         SET
            category_id = ?,
            nama_produk = ?,
            deskripsi = ?,
            harga = ?,
            stok = ?,
            gambar = ?
         WHERE id = ?"
    );


    mysqli_stmt_bind_param(
        $stmt_update,
        "issdisi",
        $category_id,
        $nama_produk,
        $deskripsi,
        $harga,
        $stok,
        $nama_file_baru,
        $id
    );


    if (mysqli_stmt_execute($stmt_update)) {

        mysqli_stmt_close($stmt_update);

        header("Location: admin_produk.php");
        exit;

    } else {

        mysqli_stmt_close($stmt_update);

        die(
            "Gagal mengupdate produk: "
            . mysqli_error($conn)
        );
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Produk - VegeBanua Mart
    </title>


    <link
        rel="stylesheet"
        href="css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="css/font-awesome.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f6;
            color: #333;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;

            background: linear-gradient(
                180deg,
                #00a94f 0%,
                #008f45 100%
            );

            padding: 25px 15px;
            color: white;

            z-index: 1000;

            box-shadow:
                3px 0 15px
                rgba(0,0,0,0.08);
        }


        .sidebar-logo {
            text-align: center;
            margin-bottom: 35px;
        }


        .sidebar-logo .logo-icon {
            width: 55px;
            height: 55px;

            margin: auto;
            margin-bottom: 10px;

            border-radius: 50%;

            background:
                rgba(255,255,255,0.15);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;
        }


        .sidebar-logo h3 {
            font-size: 19px;
            font-weight: 700;
            margin: 0;
        }


        .sidebar-logo span {
            font-size: 11px;
            opacity: 0.8;
        }


        .menu-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;

            opacity: 0.65;

            margin:
                0
                12px
                10px;
        }


        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }


        .sidebar-menu li {
            margin-bottom: 6px;
        }


        .sidebar-menu a {
            display: flex;
            align-items: center;

            gap: 13px;

            color: white;
            text-decoration: none;

            padding: 12px 14px;

            border-radius: 9px;

            font-size: 13px;

            transition: 0.2s;
        }


        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background:
                rgba(255,255,255,0.16);
        }


        .sidebar-menu a i {
            width: 20px;
            text-align: center;
        }


        .sidebar-bottom {
            position: absolute;

            left: 15px;
            right: 15px;
            bottom: 20px;
        }


        .sidebar-bottom a {
            display: flex;
            align-items: center;

            gap: 13px;

            color: white;
            text-decoration: none;

            padding: 12px 14px;

            border-radius: 9px;

            font-size: 13px;
        }


        .sidebar-bottom a:hover {
            background:
                rgba(255,255,255,0.16);
        }


        /* =========================
           MAIN
        ========================= */

        .main-content {
            margin-left: 250px;

            min-height: 100vh;

            padding:
                25px 30px;
        }


        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            background: white;

            padding: 16px 20px;

            border-radius: 12px;

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            margin-bottom: 25px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.04);
        }


        .topbar-title h4 {
            margin: 0;
            font-weight: 700;
        }


        .topbar-title p {
            margin:
                4px 0 0;

            font-size: 12px;
            color: #888;
        }


        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }


        .admin-avatar {
            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #e9f9ef;
            color: #00a94f;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        .admin-profile strong {
            display: block;
            font-size: 13px;
        }


        .admin-profile span {
            display: block;
            font-size: 10px;
            color: #999;
        }


        /* =========================
           EDIT CARD
        ========================= */

        .edit-card {
            background: white;

            border-radius: 14px;

            padding: 28px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.04);
        }


        .edit-header {
            display: flex;
            align-items: center;

            gap: 15px;

            margin-bottom: 25px;

            padding-bottom: 18px;

            border-bottom:
                1px solid #eee;
        }


        .edit-icon {
            width: 45px;
            height: 45px;

            border-radius: 10px;

            background: #e9f9ef;

            color: #00a94f;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 19px;
        }


        .edit-header h2 {
            margin: 0;

            font-size: 20px;

            font-weight: 700;
        }


        .edit-header p {
            margin:
                4px 0 0;

            font-size: 11px;

            color: #999;
        }


        /* =========================
           FORM
        ========================= */

        .form-group label {
            font-size: 12px;

            font-weight: 600;

            margin-bottom: 7px;
        }


        .form-control {
            height: 42px;

            border-radius: 8px;

            border:
                1px solid #ddd;

            font-size: 12px;
        }


        textarea.form-control {
            height: 100px;

            resize: none;
        }


        .form-control:focus {
            border-color: #00a94f;

            box-shadow:
                0 0 0
                0.1rem
                rgba(0,169,79,0.12);
        }


        /* =========================
           IMAGE PREVIEW
        ========================= */

        .current-image {
            width: 140px;
            height: 140px;

            object-fit: cover;

            border-radius: 12px;

            border:
                1px solid #eee;

            display: block;

            margin-bottom: 10px;
        }


        .image-info {
            font-size: 10px;
            color: #999;

            margin-bottom: 10px;
        }


        /* =========================
           BUTTON
        ========================= */

        .form-actions {
            display: flex;

            justify-content:
                flex-end;

            gap: 10px;

            margin-top: 20px;

            padding-top: 20px;

            border-top:
                1px solid #eee;
        }


        .btn-back {
            background: #eee;

            color: #555;

            border: none;

            padding:
                11px 18px;

            border-radius: 8px;

            font-size: 12px;

            text-decoration: none;
        }


        .btn-back:hover {
            background: #ddd;
            color: #333;

            text-decoration: none;
        }


        .btn-save {
            background: #00a94f;

            color: white;

            border: none;

            padding:
                11px 20px;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;
        }


        .btn-save:hover {
            background: #008f45;
            color: white;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }


            .sidebar-logo h3,
            .sidebar-logo span,
            .menu-title,
            .sidebar-menu a span,
            .sidebar-bottom span {
                display: none;
            }


            .sidebar-logo {
                margin-bottom: 25px;
            }


            .sidebar-logo .logo-icon {
                width: 45px;
                height: 45px;

                font-size: 20px;
            }


            .sidebar-menu a,
            .sidebar-bottom a {
                justify-content: center;
                padding: 12px;
            }


            .sidebar-menu a i,
            .sidebar-bottom a i {
                width: auto;
            }


            .sidebar-bottom {
                left: 10px;
                right: 10px;
            }


            .main-content {
                margin-left: 70px;

                padding: 15px;
            }


            .topbar {
                padding:
                    13px 15px;
            }


            .admin-profile > div:last-child {
                display: none;
            }


            .edit-card {
                padding: 20px;
            }


            .form-actions {
                flex-direction: column;
            }


            .btn-back,
            .btn-save {
                width: 100%;
                text-align: center;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="sidebar-logo">

        <div class="logo-icon">

            <i class="fa fa-leaf"></i>

        </div>


        <h3>
            VegeBanua
        </h3>


        <span>
            Admin Panel
        </span>

    </div>


    <p class="menu-title">
        Menu Utama
    </p>


    <ul class="sidebar-menu">

        <li>

            <a href="admin.php">

                <i class="fa fa-dashboard"></i>

                <span>
                    Dashboard
                </span>

            </a>

        </li>


        <li>

            <a
                href="admin_produk.php"
                class="active"
            >

                <i class="fa fa-shopping-basket"></i>

                <span>
                    Kelola Produk
                </span>

            </a>

        </li>


        <li>

            <a href="admin_pesanan.php">

                <i class="fa fa-file-text-o"></i>

                <span>
                    Kelola Pesanan
                </span>

            </a>

        </li>


        <li>

            <a
                href="index.php"
                target="_blank"
            >

                <i class="fa fa-globe"></i>

                <span>
                    Lihat Website
                </span>

            </a>

        </li>

    </ul>


    <div class="sidebar-bottom">

        <a href="logout.php">

            <i class="fa fa-sign-out"></i>

            <span>
                Keluar
            </span>

        </a>

    </div>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main-content">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="topbar-title">

            <h4>
                Kelola Produk
            </h4>

            <p>
                Edit informasi produk VegeBanua Mart
            </p>

        </div>


        <div class="admin-profile">

            <div class="admin-avatar">

                <i class="fa fa-user"></i>

            </div>


            <div>

                <strong>
                    <?= htmlspecialchars($_SESSION["nama"]); ?>
                </strong>

                <span>
                    Administrator
                </span>

            </div>

        </div>

    </div>


    <!-- EDIT CARD -->

    <div class="edit-card">


        <div class="edit-header">

            <div class="edit-icon">

                <i class="fa fa-pencil"></i>

            </div>


            <div>

                <h2>
                    Edit Produk
                </h2>

                <p>
                    Perbarui informasi produk
                    #<?= $produk["id"]; ?>
                </p>

            </div>

        </div>


        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="row">


                <!-- KOLOM KIRI -->

                <div class="col-md-6">


                    <div class="form-group">

                        <label>
                            Nama Produk
                        </label>


                        <input
                            type="text"
                            name="nama_produk"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $produk["nama_produk"]
                            ); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Kategori
                        </label>


                        <select
                            name="category_id"
                            class="form-control"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>


                            <?php while (
                                $kategori =
                                mysqli_fetch_assoc(
                                    $query_kategori
                                )
                            ): ?>

                                <option
                                    value="<?= $kategori["id"]; ?>"
                                    <?= (
                                        $produk["category_id"]
                                        ==
                                        $kategori["id"]
                                    )
                                    ? "selected"
                                    : ""; ?>
                                >

                                    <?= htmlspecialchars(
                                        $kategori["nama_kategori"]
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Harga
                        </label>


                        <input
                            type="number"
                            name="harga"
                            class="form-control"
                            value="<?= $produk["harga"]; ?>"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Stok
                        </label>


                        <input
                            type="number"
                            name="stok"
                            class="form-control"
                            value="<?= $produk["stok"]; ?>"
                            min="0"
                            required
                        >

                    </div>


                </div>


                <!-- KOLOM KANAN -->

                <div class="col-md-6">


                    <div class="form-group">

                        <label>
                            Gambar Produk Saat Ini
                        </label>


                        <?php if (
                            !empty($produk["gambar"])
                        ): ?>

                            <img
                                src="images/<?= htmlspecialchars(
                                    $produk["gambar"]
                                ); ?>"
                                class="current-image"
                                alt="Gambar Produk"
                            >

                        <?php else: ?>

                            <div
                                style="
                                    width:140px;
                                    height:140px;
                                    border-radius:12px;
                                    background:#f5f5f5;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#aaa;
                                    margin-bottom:10px;
                                "
                            >

                                <i
                                    class="fa fa-image"
                                    style="font-size:35px;"
                                ></i>

                            </div>

                        <?php endif; ?>


                        <div class="image-info">

                            Pilih gambar baru jika ingin
                            mengganti gambar produk.

                        </div>


                        <input
                            type="file"
                            name="gambar"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Deskripsi Produk
                        </label>


                        <textarea
                            name="deskripsi"
                            class="form-control"
                            placeholder="Masukkan deskripsi produk..."
                        ><?= htmlspecialchars(
                            $produk["deskripsi"]
                        ); ?></textarea>

                    </div>


                </div>

            </div>


            <!-- ACTION -->

            <div class="form-actions">

                <a
                    href="admin_produk.php"
                    class="btn-back"
                >

                    <i class="fa fa-arrow-left"></i>

                    Kembali

                </a>


                <button
                    type="submit"
                    class="btn-save"
                >

                    <i class="fa fa-save"></i>

                    Simpan Perubahan

                </button>

            </div>


        </form>

    </div>

</main>


</body>

</html>