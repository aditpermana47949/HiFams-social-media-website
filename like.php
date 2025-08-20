<?php
    session_start();
    require "conn.php";
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_SESSION["id"];
        $p_id = base64_decode($_GET["q"]);
        $datetime = date('Y-m-d H:i:s');
        $q = "INSERT INTO likes (user_id, posts_id, datetime) VALUES ($id, $p_id, '$datetime')";
        if (mysqli_query($conn, $q)) {
            if (isset($_GET["s"])) {
                header("Location: profile.php?q=" . urlencode($_GET["s"]));
                exit();
            }
            header("Location: home.php");
            exit;
        }
    }
?>