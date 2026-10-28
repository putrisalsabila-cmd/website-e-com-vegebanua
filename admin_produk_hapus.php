<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

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
    "SELECT gambar FROM products WHERE id = ?"
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
   HAPUS PRODUK
========================= */

$stmt_delete = mysqli_prepare(
    $conn,
    "DELETE FROM products WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt_delete,
    "i",
    $id
);

if (mysqli_stmt_execute($stmt_delete)) {

    mysqli_stmt_close($stmt_delete);

    /* Hapus gambar dari folder */

    if (!empty($produk["gambar"])) {

        $file_gambar =
            "images/" . $produk["gambar"];

        if (file_exists($file_gambar)) {
            unlink($file_gambar);
        }
    }

    header("Location: admin_produk.php");
    exit;

} else {

    mysqli_stmt_close($stmt_delete);

    die(
        "Produk gagal dihapus: "
        . mysqli_error($conn)
    );
}
?>