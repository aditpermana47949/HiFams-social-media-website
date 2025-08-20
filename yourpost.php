<?php
    session_start();
    require "functions.php";
    require "key.php";
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["q"])) {
        $p_id = $_GET["q"];
        $id = $_SESSION["id"];
        $q = "SELECT * FROM likes WHERE posts_id = $p_id";
        $r = mysqli_query($conn, $q);
        $qb = "SELECT * FROM posts WHERE id = $p_id";
        $rb = mysqli_query($conn, $qb);
        $ob = $rb -> fetch_assoc();
        $qc = "SELECT * FROM user WHERE id = $id";
        $rc = mysqli_query($conn, $qc);
        $oc = $rc -> fetch_assoc();
        $qd = "SELECT * FROM comments WHERE posts_id = $p_id";
        $rd = mysqli_query($conn, $qd);
        // Menghitung jumlah like
        $qi = "SELECT COUNT(*) as likecount FROM likes WHERE posts_id = $p_id";
        $ri = mysqli_query($conn, $qi);
        $oi = $ri->fetch_assoc();
        $likecount = $oi["likecount"];

        // Menghitung jumlah komentar
        $qk = "SELECT COUNT(*) as commentcount FROM comments WHERE posts_id = $p_id";
        $rk = mysqli_query($conn, $qk);
        $ok = $rk->fetch_assoc();
        $commentcount = $ok["commentcount"];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Your Post</title>
            <link rel="stylesheet" href="yourpost.css"> <!-- Link ke file CSS -->
        </head>
        <body>
            <header>
                <div><a href="index.php">HiFams</a></div>
                <div>
                    <?php
                    $idddaaa = $id;
                    $encrypted_id_aaaap = encryptData($idddaaa, $key);
                    ?>
                    <a href="profile.php?q=<?php echo $encrypted_id_aaaap; ?>">Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </header>
            <div id="post">
                <?php
                $idada = $id;
                $encrypted_id_addd = encryptData($idada, $key);
                ?>
                <a href="profile.php?q=<?php echo $encrypted_id_addd; ?>">@<?php echo htmlspecialchars($oc["username"]); ?></a>
                <p><?php echo htmlspecialchars($ob["datetime"]); ?></p>
                <p><?php echo $ob["content"]; ?></p>
            </div>
            <div id="container">
                <div id="seecomment">
                    <p>Responses (<?php echo $commentcount; ?>)</p>
                    <div id="box">
                        <?php
                        while ($od = $rd -> fetch_assoc()) {
                            $person_a = $od["user_id"];
                            $qe = "SELECT * FROM user WHERE id = $person_a";
                            $re = mysqli_query($conn, $qe);
                            $oe = $re -> fetch_assoc();
                            $id = $od["user_id"];
                            $encrypted_id = encryptData($id, $key);
                            ?>
                            <div id="boxcomm">
                            <a href="profile.php?q=<?php echo $encrypted_id; ?>">@<?php echo $oe["username"]; ?></a>
                            <p><?php echo $od["datetime"]; ?></p>
                            <p><?php echo $od["comments"]; ?></p>
                            <?php
                            $idb = $od["user_id"];
                            $encrypted_id_b = encryptData($idb, $key);
                            ?>
                            <a href="chat.php?q=<?php echo $encrypted_id_b; ?>&s=<?php echo $p_id; ?>&t=<?php echo $od["id"]; ?>">Send feedback</a>
                            <br>
                            </div>
                            <br>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <br>
                <div id="seelike">
                    <p>Likes (<?php echo $likecount; ?>)</p>
                    <div id="box">
                        <?php
                        while ($o = $r -> fetch_assoc()) {
                            $person = $o["user_id"];
                            $qa = "SELECT * FROM user WHERE id = $person";
                            $ra = mysqli_query($conn, $qa);
                            $oa = $ra -> fetch_assoc();
                            $idd = $o["user_id"];
                            $encrypted_id_a = encryptData($idd, $key);
                            ?>
                            <a href="profile.php?q=<?php echo $encrypted_id_a; ?>">@<?php echo $oa["username"]; ?></a>
                            <br>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <br>
            </div>
            <footer>
                <div>
                    <a href="logout.php">Logout</a>
                </div>
            </footer>
        </body>
        </html>
        <?php
        
    } else {
        header("Location: index.php");
        exit();
    }
?>