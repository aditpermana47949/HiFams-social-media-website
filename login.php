<?php
    require "conn.php";
    session_start();
    if (isset($_POST["submit"])) {
        $username = $_POST["username"];
        $q = "SELECT * FROM user WHERE username = '$username'";
        $r = mysqli_query($conn, $q);
        $o = $r -> fetch_assoc();
        $password = $o["password"];
        if (password_verify($_POST["password"], $password)) {
            $_SESSION["id"] = $o["id"];
            header("Location: home.php");
            exit;
        } else {
            echo "<script>
                alert('Password or Username invalid.');
                document.location.href = 'login.php';
                </script>";
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <div class="container">
    <form action="" method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit" name="submit">Login</button>
    </form>
    <br>
    <a href="register.php" class="create-account">Create account</a>
    </div>
</body>
</html>