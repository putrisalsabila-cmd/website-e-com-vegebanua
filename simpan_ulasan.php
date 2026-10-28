<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: pesanan.php");
    exit;
}


$user_id = $_SESSION["user_id"];

$order_id = isset($_POST["order_id"])
    ? (int) $_POST["order_id"]
    : 0;

$product_id = isset($_POST["product_id"])
    ? (int) $_POST["product_id"]
    : 0;

$rating = isset($_POST["rating"])
    ? (int) $_POST["rating"]
    : 0;

$komentar = trim($_POST["komentar"] ?? "");


/* =========================
   VALIDASI
========================= */

if (
    $order_id <= 0 ||
    $product_id <= 0 ||
    $rating < 1 ||
    $rating > 5 ||
    $komentar == ""
) {
    die("Data ulasan tidak valid.");
}


/* =========================
   CEK PESANAN SELESAI
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT order_details.id
     FROM orders
     INNER JOIN order_details
        ON orders.id = order_details.order_id
     WHERE orders.id = ?
       AND orders.user_id = ?
       AND orders.status = 'Selesai'
       AND order_details.product_id = ?"
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

if (mysqli_num_rows($result) == 0) {

    mysqli_stmt_close($stmt);

    die("Kamu hanya bisa memberikan ulasan untuk produk yang sudah dibeli dan pesanan sudah selesai.");

}

mysqli_stmt_close($stmt);


/* =========================
   CEK ULASAN SEBELUMNYA
========================= */

$stmt_cek = mysqli_prepare(
    $conn,
    "SELECT id
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

if (mysqli_num_rows($result_cek) > 0) {

    mysqli_stmt_close($stmt_cek);

    die("Kamu sudah memberikan ulasan untuk produk ini.");

}

mysqli_stmt_close($stmt_cek);


/* =========================
   SIMPAN ULASAN
========================= */

$stmt_insert = mysqli_prepare(
    $conn,
    "INSERT INTO reviews
        (user_id, order_id, product_id, rating, komentar)
     VALUES
        (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt_insert,
    "iiiis",
    $user_id,
    $order_id,
    $product_id,
    $rating,
    $komentar
);


if (mysqli_stmt_execute($stmt_insert)) {

    mysqli_stmt_close($stmt_insert);

    header("Location: pesanan.php?ulasan=berhasil");
    exit;

} else {

    mysqli_stmt_close($stmt_insert);

    die("Ulasan gagal disimpan. Silakan coba lagi.");

}
?>