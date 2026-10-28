<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: admin_produk.php");
    exit;
}

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
   UPLOAD GAMBAR
========================= */

if (!isset($_FILES["gambar"]) || $_FILES["gambar"]["error"] != 0) {
    die("Gambar produk wajib dipilih.");
}

$file = $_FILES["gambar"];

$allowed = [
    "jpg",
    "jpeg",
    "png",
    "webp"
];

$extension = strtolower(
    pathinfo($file["name"], PATHINFO_EXTENSION)
);

if (!in_array($extension, $allowed)) {
    die("Format gambar tidak didukung.");
}

if ($file["size"] > 2 * 1024 * 1024) {
    die("Ukuran gambar maksimal 2 MB.");
}


/* Nama file unik */

$nama_file = time() . "_" . uniqid() . "." . $extension;

$target = "images/" . $nama_file;

if (!move_uploaded_file($file["tmp_name"], $target)) {
    die("Gagal mengupload gambar.");
}


/* =========================
   SIMPAN DATABASE
========================= */

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO products
    (
        category_id,
        nama_produk,
        deskripsi,
        harga,
        stok,
        gambar
    )
    VALUES (?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "issdis",
    $category_id,
    $nama_produk,
    $deskripsi,
    $harga,
    $stok,
    $nama_file
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: admin_produk.php");
    exit;

} else {

    mysqli_stmt_close($stmt);

    if (file_exists($target)) {
        unlink($target);
    }

    die(
        "Gagal menyimpan produk: "
        . mysqli_error($conn)
    );
}
?>