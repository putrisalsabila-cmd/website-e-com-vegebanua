<?php
require_once "config/database.php";

$pesan = "";
$berhasil = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($nama == "" || $email == "" || $password == "") {
        $pesan = "Semua data wajib diisi.";
    } else {

        $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($cek, "s", $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Email sudah terdaftar.";

        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (nama, email, password, role)
                 VALUES (?, ?, ?, 'user')"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nama,
                $email,
                $password_hash
            );

            if (mysqli_stmt_execute($stmt)) {
                $pesan = "Pendaftaran berhasil!";
                $berhasil = true;
            } else {
                $pesan = "Pendaftaran gagal.";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Akun - VegeBanua Mart</title>

    <style>

        * {
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

        .register-container {
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
            margin-top: 5px;
        }

        button:hover {
            background: #43a047;
        }

        .pesan {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .sukses {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .gagal {
            background: #ffebee;
            color: #c62828;
        }

        .login {
            text-align: center;
            margin-top: 22px;
            color: #777;
            font-size: 14px;
        }

        .login a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        .login a:hover {
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

<div class="register-container">

    <div class="logo">
        <h1>VegeBanua Mart</h1>
        <p>Belanja sayur dan kebutuhan segar dengan mudah</p>
    </div>

    <h2>Daftar Akun</h2>

    <?php if ($pesan != ""): ?>

        <div class="pesan <?= $berhasil ? 'sukses' : 'gagal'; ?>">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php endif; ?>

    <?php if (!$berhasil): ?>

        <form method="POST">

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="nama"
                    placeholder="Masukkan nama lengkap"
                    required
                >
            </div>

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
                Daftar Sekarang
            </button>

        </form>

    <?php else: ?>

        <a href="login.php" style="
            display: block;
            text-align: center;
            background: #4CAF50;
            color: white;
            padding: 13px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        ">
            Login Sekarang
        </a>

    <?php endif; ?>

    <?php if (!$berhasil): ?>

        <div class="login">
            Sudah punya akun?
            <a href="login.php">Login di sini</a>
        </div>

    <?php endif; ?>

    <div class="back-home">
        <a href="index.html">← Kembali ke Beranda</a>
    </div>

</div>

</body>
</html>

