<?php

session_start();
require_once "config/database.php";

if (!isset($_GET["id"])) {
    header("Location: shop.php");
    exit;
}

$id = (int) $_GET["id"];

$query = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE id = $id"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: shop.php");
    exit;
}

$produk = mysqli_fetch_assoc($query);

if ($produk["stok"] <= 0) {
    header("Location: shop.php");
    exit;
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_SESSION["cart"][$id])) {

    if ($_SESSION["cart"][$id]["jumlah"] < $produk["stok"]) {
        $_SESSION["cart"][$id]["jumlah"]++;
    }

} else {

    $_SESSION["cart"][$id] = [
        "id" => $produk["id"],
        "nama_produk" => $produk["nama_produk"],
        "harga" => $produk["harga"],
        "gambar" => $produk["gambar"],
        "stok" => $produk["stok"],
        "jumlah" => 1
    ];

}

header("Location: cart.php");
exit;

?>

