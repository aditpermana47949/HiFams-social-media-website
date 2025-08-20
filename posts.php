<?php
    require "conn.php";
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $content = mysqli_real_escape_string($conn, htmlspecialchars($_POST["content"]));
        $id = $_SESSION["id"];
        $datetime = date('Y-m-d H:i:s');
        $q = "INSERT INTO posts (user_id, content, datetime) VALUES ($id, '$content', '$datetime')";
        if (mysqli_query($conn, $q)) {
            header("Location: home.php?q=1");
            exit;
        }
    }
?>