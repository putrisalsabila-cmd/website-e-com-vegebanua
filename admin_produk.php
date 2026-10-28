<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

/* =========================
   DATA KATEGORI
========================= */

$query_kategori = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY nama_kategori ASC"
);

/* =========================
   DATA PRODUK
========================= */

$query_produk = mysqli_query(
    $conn,
    "SELECT 
        products.*,
        categories.nama_kategori
     FROM products
     LEFT JOIN categories
        ON products.category_id = categories.id
     ORDER BY products.id DESC"
);

if (!$query_produk) {
    die("Query produk gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Produk - VegeBanua Mart</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
            box-shadow: 3px 0 15px rgba(0,0,0,0.08);
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar-logo .logo-icon {
            width: 55px;
            height: 55px;
            margin: auto;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            margin-bottom: 10px;
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
            margin: 0 12px 10px;
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
            background: rgba(255,255,255,0.16);
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 15px;
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
            background: rgba(255,255,255,0.16);
        }

        /* =========================
           MAIN
        ========================= */

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding: 25px 30px;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
        }

        .topbar-title h4 {
            margin: 0;
            font-weight: 700;
        }

        .topbar-title p {
            margin: 4px 0 0;
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
           PAGE HEADER
        ========================= */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .page-header p {
            margin: 5px 0 0;
            color: #888;
            font-size: 12px;
        }

        .btn-add {
            background: #00a94f;
            color: white;
            border: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-add:hover {
            background: #008f45;
            color: white;
        }

        /* =========================
           PRODUCT TABLE
        ========================= */

        .product-card {
            background: white;
            border-radius: 13px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.04);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        .product-table th {
            padding: 13px 10px;
            background: #f8faf9;
            color: #777;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .product-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            vertical-align: middle;
        }

        .product-table tr:last-child td {
            border-bottom: none;
        }

        .product-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 9px;
            border: 1px solid #eee;
        }

        .product-name {
            font-weight: 700;
            color: #333;
        }

        .product-category {
            display: inline-block;
            background: #eaf8ef;
            color: #00a94f;
            padding: 5px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 600;
        }

        .product-price {
            font-weight: 700;
            color: #00a94f;
        }

        .stock-badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
        }

        .stock-aman {
            background: #e9f8ef;
            color: #00a94f;
        }

        .stock-menipis {
            background: #fff2dd;
            color: #d98a13;
        }

        .stock-habis {
            background: #ffe8e8;
            color: #d9534f;
        }

        /* =========================
           ACTION BUTTON
        ========================= */

        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #eaf3ff;
            color: #3977d5;
        }

        .btn-edit:hover {
            background: #dcecff;
            color: #3977d5;
        }

        .btn-delete {
            background: #ffeaea;
            color: #d9534f;
        }

        .btn-delete:hover {
            background: #ffdada;
            color: #d9534f;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty-product {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-product i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #ccc;
        }

        /* =========================
           MODAL
        ========================= */

        .modal-content {
            border: none;
            border-radius: 14px;
            overflow: hidden;
        }

        .modal-header {
            background: #00a94f;
            color: white;
            border: none;
        }

        .modal-header h5 {
            font-weight: 700;
        }

        .modal-header .close {
            color: white;
            opacity: 1;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-control {
            height: 42px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        textarea.form-control {
            height: 90px;
            resize: none;
        }

        .form-control:focus {
            border-color: #00a94f;
            box-shadow: 0 0 0 0.1rem rgba(0,169,79,0.12);
        }

        .btn-save {
            background: #00a94f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-save:hover {
            background: #008f45;
            color: white;
        }

        .btn-cancel {
            background: #eee;
            color: #555;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 12px;
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

            .page-header {
                align-items: flex-start;
                gap: 15px;
                flex-direction: column;
            }

            .topbar {
                padding: 13px 15px;
            }

            .admin-profile > div:last-child {
                display: none;
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

        <h3>VegeBanua</h3>

        <span>Admin Panel</span>

    </div>


    <p class="menu-title">
        Menu Utama
    </p>


    <ul class="sidebar-menu">

        <li>
            <a href="admin.php">
                <i class="fa fa-dashboard"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="admin_produk.php" class="active">
                <i class="fa fa-shopping-basket"></i>
                <span>Kelola Produk</span>
            </a>
        </li>

    

        <li>
            <a href="admin_pesanan.php">
                <i class="fa fa-file-text-o"></i>
                <span>Kelola Pesanan</span>
            </a>
        </li>

    <li>
        <a href="admin_pesan.php">
            <i class="fa fa-envelope"></i>
            <span>Pesan Masuk</span>
        </a>
    </li>

        <li>
            <a href="index.php" target="_blank">
                <i class="fa fa-globe"></i>
                <span>Lihat Website</span>
            </a>
        </li>

    </ul>


    <div class="sidebar-bottom">

        <a href="logout.php">

            <i class="fa fa-sign-out"></i>

            <span>Keluar</span>

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
                Tambahkan dan kelola produk VegeBanua Mart
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


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h2>
                Daftar Produk
            </h2>

            <p>
                Kelola informasi, harga, kategori, dan stok produk.
            </p>

        </div>


        <button
            type="button"
            class="btn-add"
            data-toggle="modal"
            data-target="#modalTambah"
        >

            <i class="fa fa-plus"></i>

            Tambah Produk

        </button>

    </div>


    <!-- PRODUCT TABLE -->

    <div class="product-card">

        <div class="table-wrapper">

            <?php if (mysqli_num_rows($query_produk) > 0): ?>

                <table class="product-table">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Produk</th>

                            <th>Kategori</th>

                            <th>Harga</th>

                            <th>Stok</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php
                    $no = 1;

                    while ($produk = mysqli_fetch_assoc($query_produk)):
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <td>

                                <div style="
                                    display:flex;
                                    align-items:center;
                                    gap:12px;
                                ">

                                    <img
                                        src="images/<?= htmlspecialchars($produk["gambar"]); ?>"
                                        class="product-image"
                                        alt="<?= htmlspecialchars($produk["nama_produk"]); ?>"
                                        onerror="this.src='images/placeholder.jpg';"
                                    >

                                    <div>

                                        <div class="product-name">

                                            <?= htmlspecialchars(
                                                $produk["nama_produk"]
                                            ); ?>

                                        </div>

                                        <small style="color:#999;">
                                            ID Produk #<?= $produk["id"]; ?>
                                        </small>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="product-category">

                                    <?= htmlspecialchars(
                                        $produk["nama_kategori"] ?? "Tanpa Kategori"
                                    ); ?>

                                </span>

                            </td>


                            <td>

                                <span class="product-price">

                                    Rp <?= number_format(
                                        $produk["harga"],
                                        0,
                                        ",",
                                        "."
                                    ); ?>

                                </span>

                            </td>


                            <td>

                                <?php if ($produk["stok"] <= 0): ?>

                                    <span class="stock-badge stock-habis">
                                        Habis
                                    </span>

                                <?php elseif ($produk["stok"] <= 10): ?>

                                    <span class="stock-badge stock-menipis">
                                        <?= $produk["stok"]; ?> stok
                                    </span>

                                <?php else: ?>

                                    <span class="stock-badge stock-aman">
                                        <?= $produk["stok"]; ?> stok
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="admin_produk_edit.php?id=<?= $produk["id"]; ?>"
                                        class="btn-action btn-edit"
                                        title="Edit Produk"
                                    >
                                        <i class="fa fa-pencil"></i>
                                    </a>


                                    <a
                                        href="admin_produk_hapus.php?id=<?= $produk["id"]; ?>"
                                        class="btn-action btn-delete"
                                        title="Hapus Produk"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?');"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty-product">

                    <i class="fa fa-shopping-basket"></i>

                    <p>
                        Belum ada produk.
                    </p>

                    <button
                        type="button"
                        class="btn-add"
                        data-toggle="modal"
                        data-target="#modalTambah"
                    >
                        Tambah Produk Pertama
                    </button>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>


<!-- =========================
     MODAL TAMBAH PRODUK
========================= -->

<div
    class="modal fade"
    id="modalTambah"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="fa fa-plus-circle"></i>

                    Tambah Produk

                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >

                    <span>&times;</span>

                </button>

            </div>


            <form
                action="admin_produk_tambah.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="modal-body">

                    <div class="row">


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Nama Produk
                                </label>

                                <input
                                    type="text"
                                    name="nama_produk"
                                    class="form-control"
                                    placeholder="Contoh: Bayam"
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

                                    <?php while ($kategori = mysqli_fetch_assoc($query_kategori)): ?>

                                        <option
                                            value="<?= $kategori["id"]; ?>"
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
                                    placeholder="Contoh: 12000"
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
                                    placeholder="Contoh: 20"
                                    min="0"
                                    required
                                >

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Gambar Produk
                                </label>

                                <input
                                    type="file"
                                    name="gambar"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    required
                                >

                                <small style="
                                    display:block;
                                    margin-top:6px;
                                    color:#999;
                                    font-size:10px;
                                ">
                                    Format JPG, JPEG, PNG, atau WEBP.
                                </small>

                            </div>


                            <div class="form-group">

                                <label>
                                    Deskripsi Produk
                                </label>

                                <textarea
                                    name="deskripsi"
                                    class="form-control"
                                    placeholder="Masukkan deskripsi produk..."
                                ></textarea>

                            </div>

                        </div>

                    </div>

                </div>


                <div
                    style="
                        padding:0 25px 25px;
                        text-align:right;
                    "
                >

                    <button
                        type="button"
                        class="btn-cancel"
                        data-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-save"
                    >

                        <i class="fa fa-save"></i>

                        Simpan Produk

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- JS -->

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>