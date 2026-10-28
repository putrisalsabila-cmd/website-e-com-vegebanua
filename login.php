<?php
session_start();
require_once "config/database.php";

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {

        $pesan = "Email dan password wajib diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama, email, password, role FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["nama"] = $user["nama"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                if ($user["role"] == "admin") {
                    header("Location: admin.php");
                    exit;
                } else {
                    header("Location: index.php");
                    exit;
                }

            } else {

                $pesan = "Email atau password salah.";
            }

        } else {

            $pesan = "Email atau password salah.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - VegeBanua Mart</title>

    <style>

        {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f5e9, #f8fff8);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 420px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo h1 {
            color: #4CAF50;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .logo p {
            color: #777;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 23px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #444;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #4CAF50;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #43a047;
        }

        .pesan {
            background: #ffebee;
            color: #c62828;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register {
            text-align: center;
            margin-top: 22px;
            color: #777;
            font-size: 14px;
        }

        .register a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .register a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 15px;
        }

        .back-home a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
        }

        .back-home a:hover {
            color: #4CAF50;
        }

    </style>
</head>

<body>

<div class="login-container">

    <div class="logo">
        <h1>VegeBanua Mart</h1>
        <p>Belanja sayur dan kebutuhan segar dengan mudah</p>
    </div>

    <h2>Login Akun</h2>

    <?php if ($pesan != ""): ?>

        <div class="pesan">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Masukkan email"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="register">
        Belum punya akun?
        <a href="register.php">Daftar di sini</a>
    </div>

    <div class="back-home">
        <a href="index.php">← Kembali ke Beranda</a>
    </div>

</div>

</body>
</html>

