<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["order_id"]) || !isset($_GET["product_id"])) {
    header("Location: pesanan.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$order_id = (int) $_GET["order_id"];
$product_id = (int) $_GET["product_id"];


/* =========================
   CEK PESANAN
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        orders.id AS order_id,
        orders.status,
        products.id AS product_id,
        products.nama_produk,
        products.gambar
     FROM orders
     INNER JOIN order_details
        ON orders.id = order_details.order_id
     INNER JOIN products
        ON order_details.product_id = products.id
     WHERE orders.id = ?
       AND orders.user_id = ?
       AND products.id = ?
       AND orders.status = 'Selesai'"
);

mysqli_stmt_bind_param(
    $stmt,
    "iii",
    $order_id,
    $user_id,
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$produk = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$produk) {
    header("Location: pesanan.php");
    exit;
}


/* =========================
   CEK ULASAN LAMA
========================= */

$stmt_cek = mysqli_prepare(
    $conn,
    "SELECT id, rating, komentar
     FROM reviews
     WHERE order_id = ?
       AND product_id = ?
       AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt_cek,
    "iii",
    $order_id,
    $product_id,
    $user_id
);

mysqli_stmt_execute($stmt_cek);

$result_cek = mysqli_stmt_get_result($stmt_cek);

$ulasan_lama = mysqli_fetch_assoc($result_cek);

mysqli_stmt_close($stmt_cek);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Beri Ulasan - VegeBanua Mart</title>

    <link rel="stylesheet"
          href="css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f8f6;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        /* =========================
           TOPBAR
        ========================= */

        .review-topbar {
            background: #00a94f;
            padding: 18px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .vege-logo {
            color: white;
            font-size: 21px;
            font-weight: 700;
            text-decoration: none;
        }

        .vege-logo:hover {
            color: white;
            text-decoration: none;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .back-btn:hover {
            color: #d9ffd9;
            text-decoration: none;
        }

        /* =========================
           WRAPPER
        ========================= */

        .review-wrapper {
            max-width: 650px;
            margin: 55px auto;
            padding: 0 20px;
        }

        .review-card {
            background: white;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        /* =========================
           TITLE
        ========================= */

        .review-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .review-title h2 {
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .review-title p {
            color: #888;
            font-size: 13px;
            margin: 0;
        }

        /* =========================
           PRODUK
        ========================= */

        .product-box {
            display: flex;
            align-items: center;
            gap: 18px;
            background: #f7fbf8;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 30px;
        }

        .product-box img {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-box h4 {
            margin: 0 0 5px;
            font-size: 17px;
            font-weight: 700;
        }

        .product-box span {
            color: #888;
            font-size: 12px;
        }

        /* =========================
           LABEL
        ========================= */

        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* =========================
           RATING BINTANG
        ========================= */

        .rating-box {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin: 15px 0 30px;
        }

        .rating-box input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .rating-box label {
            display: block;
            font-size: 42px;
            line-height: 1;
            color: #d9d9d9;
            cursor: pointer;
            margin: 0;
            padding: 0;
            transition: all 0.2s ease;
            user-select: none;
        }

        /* Saat mouse diarahkan ke bintang */
        .rating-box label:hover,
        .rating-box label:hover ~ label {
            color: #ffc107;
            transform: scale(1.08);
        }

        /* Saat bintang dipilih */
        .rating-box input:checked ~ label {
            color: #ffc107;
        }

        /* =========================
           TEXTAREA
        ========================= */

        textarea {
            width: 100%;
            min-height: 130px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 13px;
            font-size: 13px;
            resize: vertical;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        textarea:focus {
            border-color: #43a047;
        }

        /* =========================
           BUTTON
        ========================= */

        .submit-btn {
            width: 100%;
            border: none;
            background: #43a047;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background: #2e7d32;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 576px) {

            .review-topbar {
                padding: 15px 20px;
            }

            .review-card {
                padding: 25px 20px;
            }

            .rating-box label {
                font-size: 35px;
            }

        }

    </style>

</head>


<body>


<!-- TOPBAR -->

<div class="review-topbar">

    <a href="index.php"
       class="vege-logo">

        VegeBanua Mart

    </a>

    <a href="pesanan.php"
       class="back-btn">

        <i class="fa fa-arrow-left"></i>

        Kembali ke Pesanan

    </a>

</div>


<!-- CONTENT -->

<div class="review-wrapper">

    <div class="review-card">


        <!-- JUDUL -->

        <div class="review-title">

            <h2>
                Beri Ulasan
            </h2>

            <p>
                Bagikan pengalaman kamu dengan produk ini
            </p>

        </div>


        <!-- PRODUK -->

        <div class="product-box">

            <img
                src="images/<?= htmlspecialchars($produk["gambar"]); ?>"
                alt="<?= htmlspecialchars($produk["nama_produk"]); ?>"
            >

            <div>

                <h4>
                    <?= htmlspecialchars($produk["nama_produk"]); ?>
                </h4>

                <span>
                    Pesanan #<?= $order_id; ?>
                </span>

            </div>

        </div>


        <!-- FORM -->

        <form action="simpan_ulasan.php"
              method="POST">


            <input type="hidden"
                   name="order_id"
                   value="<?= $order_id; ?>">


            <input type="hidden"
                   name="product_id"
                   value="<?= $product_id; ?>">


            <!-- RATING -->

            <div class="form-label text-center">

                Bagaimana pengalaman kamu?

            </div>


            <div class="rating-box">


                <!-- 5 BINTANG -->

                <input
                    type="radio"
                    name="rating"
                    id="star5"
                    value="5"
                    <?= ($ulasan_lama && $ulasan_lama["rating"] == 5) ? "checked" : ""; ?>
                >

                <label for="star5"
                       title="5 bintang">

                    ★

                </label>


                <!-- 4 BINTANG -->

                <input
                    type="radio"
                    name="rating"
                    id="star4"
                    value="4"
                    <?= ($ulasan_lama && $ulasan_lama["rating"] == 4) ? "checked" : ""; ?>
                >

                <label for="star4"
                       title="4 bintang">

                    ★

                </label>


                <!-- 3 BINTANG -->

                <input
                    type="radio"
                    name="rating"
                    id="star3"
                    value="3"
                    <?= ($ulasan_lama && $ulasan_lama["rating"] == 3) ? "checked" : ""; ?>
                >

                <label for="star3"
                       title="3 bintang">

                    ★

                </label>


                <!-- 2 BINTANG -->

                <input
                    type="radio"
                    name="rating"
                    id="star2"
                    value="2"
                    <?= ($ulasan_lama && $ulasan_lama["rating"] == 2) ? "checked" : ""; ?>
                >

                <label for="star2"
                       title="2 bintang">

                    ★

                </label>


                <!-- 1 BINTANG -->

                <input
                    type="radio"
                    name="rating"
                    id="star1"
                    value="1"
                    <?= ($ulasan_lama && $ulasan_lama["rating"] == 1) ? "checked" : ""; ?>
                >

                <label for="star1"
                       title="1 bintang">

                    ★

                </label>


            </div>


            <!-- KOMENTAR -->

            <div>

                <div class="form-label">

                    Ceritakan pengalaman kamu

                </div>


                <textarea
                    name="komentar"
                    placeholder="Contoh: Sayurnya segar dan kualitasnya bagus..."
                    required><?= $ulasan_lama ? htmlspecialchars($ulasan_lama["komentar"]) : ""; ?></textarea>

            </div>


            <!-- SUBMIT -->

            <button type="submit"
                    class="submit-btn">

                <i class="fa fa-paper-plane"></i>

                <?= $ulasan_lama ? "Update Ulasan" : "Kirim Ulasan"; ?>

            </button>


        </form>


    </div>

</div>


</body>

</html>