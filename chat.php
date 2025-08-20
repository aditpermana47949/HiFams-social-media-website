<?php
session_start();
require "functions.php";
require "key.php";
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["q"])) {
    $decrypted_id = $_GET["q"];
    $receiver = decryptData($decrypted_id, $key);
    $id = $_SESSION["id"];
    $user_id = $_SESSION["id"];
    $q = "SELECT * FROM user WHERE id = $user_id";
    $r = mysqli_query($conn, $q);
    $o = $r->fetch_assoc();
    $qa = "SELECT * FROM user WHERE id = $receiver";
    $ra = mysqli_query($conn, $qa);
    $oa = $ra->fetch_assoc();
    $sender = $user_id;
    $status = 0;
    $qs = "UPDATE chat SET status = $status WHERE sender_id = $receiver AND receiver_id = $sender";
    $rs = mysqli_query($conn, $qs);

    // Handle additional GET parameters
    $s = isset($_GET['s']) ? $_GET['s'] : '';
    $t = isset($_GET['t']) ? $_GET['t'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        .chat-box {
            width: 400px;
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: scroll;
        }
        .chat-input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let isScrolling = false;

        function loadChat() {
            const chatBox = $('#chat-box');
            const scrollTop = chatBox.scrollTop();
            const scrollHeight = chatBox[0].scrollHeight;
            
            $.ajax({
                url: "load_chat.php",
                type: "GET",
                data: {
                    sender_id: <?php echo $sender; ?>,
                    receiver_id: <?php echo $receiver; ?>,
                },
                success: function(data) {
                    chatBox.html(data);
                    if (!isScrolling) {
                        chatBox.scrollTop(chatBox[0].scrollHeight - scrollHeight + scrollTop);
                    }
                }
            });
        }

        $(document).ready(function(){
            loadChat(); // Load chat pertama kali
            setInterval(loadChat, 1000); // Refresh chat box setiap 1 detik

            $('#chat-box').on('scroll', function() {
                const chatBox = $(this);
                const scrollTop = chatBox.scrollTop();
                const scrollHeight = chatBox[0].scrollHeight;
                const clientHeight = chatBox[0].clientHeight;

                // Jika scroll ke atas
                isScrolling = scrollHeight - clientHeight > scrollTop + 5;
            });
        });
    </script>
    <link rel="stylesheet" type="text/css" href="chat.css">
</head>
<body>
    <header>
        <div><a href="index.php">HiFams</a></div>
        <div>
            <?php
            $encrypted_id = encryptData($id, $key);
            ?>
            <a href="profile.php?q=<?php echo $encrypted_id; ?>">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>
    <div id="container">
        <div id="chat_box">
            <p>Whisper with <a href="profile.php?q=<?php echo $decrypted_id; ?>">@<?php echo $oa["username"]; ?></a></p>
            <div class="chat-box" id="chat-box">
                <!-- Chat content will be loaded here -->
            </div>
            <br>
            <form action="chat_proc.php" method="POST">
                <?php
                if (isset($_GET["s"]) && isset($_GET["t"])) {
                    $p_id = $_GET["s"];
                    $qc = "SELECT * FROM posts WHERE id = $p_id";
                    $rc = mysqli_query($conn, $qc);
                    $oc = $rc -> fetch_assoc();
                    $c_id = $_GET["t"];
                    $qd = "SELECT * FROM comments WHERE id = $c_id";
                    $rd = mysqli_query($conn, $qd);
                    $od = $rd -> fetch_assoc();
                    ?>
                    <p>@<?php echo $oa["username"]; ?>'s response: <?php echo $od["comments"]; ?></p>
                    <p>on your voice: <?php echo $oc["content"]; ?></p>
                    <?php
                    $feedback = "@{$oa['username']}'s response: {$od['comments']} | \n";
                    $feedback .= "on your voice: {$oc['content']}";
                    ?>
                    <input type="hidden" name="feedback" value="<?php echo htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php
                }
                ?>
                <input type="text" name="convo" id="convo" placeholder="Send your whisper" required autofocus>
                <br>
                <input type="hidden" value="<?php echo $receiver; ?>" name="receiver_id">
                <input type="hidden" value="<?php echo $sender; ?>" name="sender_id">
                <br>
                <button type="submit" name="submit">Send</button>
            </form>
        </div>
        <div id="friendlist">
            <p>Your fams</p>
            <?php
            $qa = "SELECT * FROM friendship f1 
                    WHERE f1.user1_id = $id 
                    AND EXISTS (
                        SELECT 1 FROM friendship f2 
                        WHERE f2.user1_id = f1.user2_id 
                        AND f2.user2_id = $id
                    )";
            $ra = mysqli_query($conn, $qa);
            if (mysqli_num_rows($ra) > 0) {
                while ($oa = $ra->fetch_assoc()) {
                    $sa = $oa["user2_id"];
                    $qb = "SELECT * FROM user WHERE id = $sa";
                    $rb = mysqli_query($conn, $qb);
                    $ob = $rb->fetch_assoc();
                    $username_f = $ob["username"];
                    $iiid = $sa;
                    $encrypted_id_aaa = encryptData($iiid, $key);
                    ?>
                    <a href="chat.php?q=<?php echo $encrypted_id_aaa; ?>">@<?php echo htmlspecialchars($username_f); ?></a><br>
                    <?php
                }
            } else {
                echo "<p>No friends found!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
}
?>
