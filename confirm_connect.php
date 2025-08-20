<?php
    session_start();
    require "functions.php";
    require "key.php";
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["q"])) {
        $id = $_SESSION["id"];
        $decrypted_id = $_GET["q"];
        $user2_id = decryptData($decrypted_id, $key);
        $q = "INSERT INTO friendship (user1_id, user2_id) VALUES ($id, $user2_id)";
        if (mysqli_query($conn, $q)) {
            header("Location: home.php");
            exit;
        } else {
            echo "<script>
            alert('Error.');
            document.location.href = 'index.php';
            </script>";
        }
    }
?>