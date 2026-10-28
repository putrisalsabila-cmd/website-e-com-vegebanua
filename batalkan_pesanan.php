<?php

session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: pesanan.php");
    exit;
}

$order_id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];


// Mulai transaksi
mysqli_begin_transaction($conn);

try {

    // Cek pesanan milik user dan masih Menunggu
    $query = "
        SELECT id, status
        FROM orders
        WHERE id = ?
        AND user_id = ?
        AND status = 'Menunggu'
        FOR UPDATE
    ";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $order_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {

        mysqli_stmt_close($stmt);

        mysqli_rollback($conn);

        header("Location: pesanan.php");
        exit;
    }

    mysqli_stmt_close($stmt);


    // Ambil detail pesanan
    $query_detail = "
        SELECT product_id, jumlah
        FROM order_details
        WHERE order_id = ?
    ";

    $stmt_detail = mysqli_prepare($conn, $query_detail);

    mysqli_stmt_bind_param(
        $stmt_detail,
        "i",
        $order_id
    );

    mysqli_stmt_execute($stmt_detail);

    $result_detail = mysqli_stmt_get_result($stmt_detail);


    // Kembalikan stok produk
    while ($detail = mysqli_fetch_assoc($result_detail)) {

        $product_id = $detail["product_id"];
        $jumlah = $detail["jumlah"];


        $update_stok = mysqli_prepare(
            $conn,
            "
            UPDATE products
            SET stok = stok + ?
            WHERE id = ?
            "
        );

        mysqli_stmt_bind_param(
            $update_stok,
            "ii",
            $jumlah,
            $product_id
        );

        mysqli_stmt_execute($update_stok);

        mysqli_stmt_close($update_stok);
    }

    mysqli_stmt_close($stmt_detail);


    // Ubah status pesanan menjadi Dibatalkan
    $update_order = mysqli_prepare(
        $conn,
        "
        UPDATE orders
        SET status = 'Dibatalkan'
        WHERE id = ?
        AND user_id = ?
        "
    );

    mysqli_stmt_bind_param(
        $update_order,
        "ii",
        $order_id,
        $user_id
    );

    mysqli_stmt_execute($update_order);

    mysqli_stmt_close($update_order);


    // Simpan semua perubahan
    mysqli_commit($conn);


    header("Location: pesanan.php");
    exit;


} catch (Exception $e) {

    // Kalau gagal, batalkan semua perubahan
    mysqli_rollback($conn);

    header("Location: pesanan.php");
    exit;
}

?>