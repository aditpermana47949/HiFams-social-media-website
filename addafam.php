<?php
    session_start();
    require "functions.php";
    require "key.php";
    $id = $_SESSION["id"];
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["q"])) {
        $decrypted_id = $_GET["q"];
        $fam = decryptData($decrypted_id, $key);
        $qa = "SELECT * FROM friendship WHERE user1_id = $id AND user2_id = $fam";
        $ra = mysqli_query($conn, $qa);
        if (mysqli_num_rows($ra) < 1){
            $q = "INSERT INTO friendship (user1_id, user2_id) VALUES ($id, $fam)";
            if (mysqli_query($conn, $q)) {
                header("Location: index.php");
                exit;
            }
        } else {
            header("Location: index.php");
            exit;
        }
    } else {
        header("Location: index.php");
        exit;
    }
?>