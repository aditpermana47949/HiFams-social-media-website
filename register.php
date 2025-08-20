<?php
require "functions.php";
session_start();

// Aktifkan error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST["submit"])) {
    // Koneksi ke database

    // Periksa koneksi
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Ambil dan sanitasi input
    $email = mysqli_real_escape_string($conn, htmlspecialchars($_POST["email"]));
    $username_input = $_POST["username"];
    $username_low = strtolower($username_input);
    $username = mysqli_real_escape_string($conn, htmlspecialchars($username_low));
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Invalid email.');
            document.location.href = 'register.php';
            </script>";
        return; // Hentikan eksekusi
    }

    // Validasi username
    if (!usernamecheck($username)) {
        echo "<script>
            alert('Username already taken.');
            window.location='register.php';
            </script>";
        return; // Hentikan eksekusi
    }

    // Query untuk memasukkan data ke database
    $q = "INSERT INTO user (username, email, password) VALUES ('$username', '$email', '$password')";
    if (mysqli_query($conn, $q)) {
        echo "<script>
            alert('Register success.');
            document.location.href = 'login.php';
            </script>";
    } else {
        // Tampilkan error jika query gagal
        echo "<script>
            alert('Registration failed: " . mysqli_error($conn) . "');
            document.location.href = 'register.php';
            </script>";
    }

    // Tutup koneksi database
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register.css">
</head>
<body>
    <form action="" method="POST">
        <label for="email">Email (just for us if case we need to contact you)</label>
        <input type="email" name="email" id="email" required>
        <br>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" style="text-transform: lowercase;" required>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit" name="submit">Daftar</button>
    </form>
</body>
</html>
