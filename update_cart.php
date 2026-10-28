<?php

session_start();

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$action = $_GET["action"] ?? "";
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if (isset($_SESSION["cart"][$id])) {

    if ($action == "plus") {

        if ($_SESSION["cart"][$id]["jumlah"] < $_SESSION["cart"][$id]["stok"]) {
            $_SESSION["cart"][$id]["jumlah"]++;
        }

    } elseif ($action == "minus") {

        $_SESSION["cart"][$id]["jumlah"]--;

        if ($_SESSION["cart"][$id]["jumlah"] <= 0) {
            unset($_SESSION["cart"][$id]);
        }

    } elseif ($action == "delete") {

        unset($_SESSION["cart"][$id]);

    }

}

header("Location: cart.php");
exit;

?>
