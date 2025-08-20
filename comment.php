<?php
    session_start();
    require "functions.php";
    require "key.php";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_SESSION["id"];
        $p_id = $_POST["p_id"];
        $response = mysqli_real_escape_string($conn, htmlspecialchars($_POST["response"]));
        $datetime = date('Y-m-d H:i:s');
        $q = "INSERT INTO comments (user_id, posts_id, comments, datetime) VALUES ($id, $p_id, '$response', '$datetime')";
        if (mysqli_query($conn, $q)) {
            if (isset($_POST["s"])) {
                $encrypted_id = "vsvsv";
                header("Location: profile.php?q=" . $encrypted_id);
                exit();
            }
            header("Location: home.php");
            exit;
        }
    }

?>