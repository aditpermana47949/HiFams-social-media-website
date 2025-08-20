<?php
session_start();
require "functions.php";
require "key.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["convo"])) {
    $current_datetime = date('Y-m-d H:i:s');
    $status_n = 1;
    $receiver = $_POST["receiver_id"];
    $sender = $_POST["sender_id"];
    $feedback = isset($_POST["feedback"]) ? mysqli_real_escape_string($conn, $_POST["feedback"]) : '';
    $convo = mysqli_real_escape_string($conn, htmlspecialchars($_POST["convo"]));

    // Kueri SQL untuk menyisipkan data ke tabel `chat`
    $qc = "INSERT INTO chat (datetime, sender_id, receiver_id, convo, status, attachment) 
           VALUES ('$current_datetime', $sender, $receiver, '$convo', $status_n, '$feedback')";

    if (mysqli_query($conn, $qc)) {
        $receiver_id = $receiver;
        $encrypted_id = encryptData($receiver_id, $key);
        header("Location: chat.php?q=$encrypted_id");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn); // Untuk debugging
    }
}

?>
