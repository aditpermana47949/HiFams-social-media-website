<?php
require "conn.php";

if (isset($_GET['sender_id']) && isset($_GET['receiver_id'])) {
    $sender = $_GET['sender_id'];
    $receiver = $_GET['receiver_id'];
    
    // Handle additional GET parameters
    $s = isset($_GET['s']) ? $_GET['s'] : '';
    $t = isset($_GET['t']) ? $_GET['t'] : '';

    $q_b = "SELECT * FROM chat WHERE sender_id = $sender AND receiver_id = $receiver 
            UNION 
            SELECT * FROM chat WHERE sender_id = $receiver AND receiver_id = $sender 
            ORDER BY datetime ASC";
    $r_b = mysqli_query($conn, $q_b);

    while ($o_b = $r_b->fetch_assoc()) {
        if ($o_b["sender_id"] == $sender) {
            echo "<p>{$o_b['datetime']}</p>";
            $feedback = isset($o_b["attachment"]) ? $o_b["attachment"] : '';
            echo "<p>{$feedback}</p>";
            echo "<p>Sender:</p>";
            echo "<p>{$o_b['convo']}</p><br>";
        } elseif ($o_b["sender_id"] == $receiver) {
            echo "<p>{$o_b['datetime']}</p>";
            $feedback = isset($o_b["attachment"]) ? $o_b["attachment"] : '';
            echo "<p>{$feedback}</p>";
            echo "<p>Receiver:</p>";
            echo "<p>{$o_b['convo']}</p><br>";
        }
    }
}
?>
