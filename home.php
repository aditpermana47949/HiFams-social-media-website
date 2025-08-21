<?php
session_start();
require "functions.php";
require "key.php";

$id = $_SESSION["id"];

$q = "SELECT * FROM friendship f1 
      WHERE f1.user1_id = $id 
      AND EXISTS (
          SELECT 1 FROM friendship f2 
          WHERE f2.user1_id = f1.user2_id 
          AND f2.user2_id = $id
      )";
$r = mysqli_query($conn, $q);

$qd = "SELECT * FROM user WHERE id = $id";
$rd = mysqli_query($conn, $qd);
$od = $rd->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HiFams</title>
    <style>
        .commentSection {
            display: none;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="home.css">
    <script>
        function showComments(postId) {
            var sections = document.getElementsByClassName('commentSection');
            for (var i = 0; i < sections.length; i++) {
                sections[i].style.display = 'none';
            }
            document.getElementById('commentSection-' + postId).style.display = 'block';
        }
    </script>
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
    <div id="start_post">
        <form action="posts.php?q=<?php echo htmlspecialchars($id); ?>" method="POST">
            <textarea name="content" rows="10" cols="30" placeholder="Tell everyone about anything"></textarea>
            <button type="submit" name="submit">Post</button>
        </form>
    </div>
    <br>

    <div class="container">
        <div id="posts">
            <h2>Newest Fams Voices</h2>
            <?php
            if (isset($_GET["q"])) {
                $qc = "SELECT * FROM posts WHERE user_id = $id ORDER BY datetime DESC LIMIT 1";
                $rc = mysqli_query($conn, $qc);
                if ($rc) {
                    $oc = $rc->fetch_assoc();
                    if ($oc) { 
                        ?>
                        <div>
                            <a href="profile.php?q=<?php echo $oc["user_id"]; ?>">@<?php echo htmlspecialchars($od["username"]); ?></a>
                            <p><?php echo htmlspecialchars($oc["datetime"]); ?></p>
                            <p><?php echo $oc["content"]; ?></p>
                        </div>
                        <?php
                    } else {
                        echo "<p>No posts found!</p>";
                    }
                } else {
                    echo "<p>Error fetching posts!</p>"; 
                }
            }

            while ($o = $r->fetch_assoc()) {
                $s = $o["user2_id"];
                $qa = "SELECT * FROM posts WHERE user_id = $s ORDER BY datetime DESC LIMIT 1";
                $ra = mysqli_query($conn, $qa);
                if ($ra) {
                    $oa = $ra->fetch_assoc();
                    if ($oa) { 
                        $qb = "SELECT * FROM user WHERE id = $s";
                        $rb = mysqli_query($conn, $qb);
                        if ($rb) {
                            $ob = $rb->fetch_assoc();
                            if ($ob) { 
                                ?>
                                <div>
                                    <?php
                                    $iid = $oa["user_id"];
                                    $encrypted_id_a = encryptData($iid, $key);
                                    ?>
                                    <a href="profile.php?q=<?php echo $encrypted_id_a; ?>">@<?php echo htmlspecialchars($ob["username"]); ?></a>
                                    <p><?php echo htmlspecialchars($oa["datetime"]); ?></p>
                                    <p><?php echo ($oa["content"]); ?></p>
                                    <?php
                                    $p_id = $oa["id"];
                                    $qi = "SELECT COUNT(*) as likecount FROM likes WHERE posts_id = $p_id";
                                    $ri = mysqli_query($conn, $qi);
                                    $oi = $ri->fetch_assoc();
                                    $likecount = $oi["likecount"];
                                    $qj = "SELECT * FROM likes WHERE posts_id = $p_id AND user_id = $id";
                                    $rj = mysqli_query($conn, $qj);
                                    $qk = "SELECT COUNT(*) as commentcount FROM comments WHERE posts_id = $p_id";
                                    $rk = mysqli_query($conn, $qk);
                                    $ok = $rk->fetch_assoc();
                                    $commentcount = $ok["commentcount"];
                                    if (mysqli_num_rows($rj) > 0) {
                                        $like_id = base64_encode($oa["id"]);
                                        ?>
                                        <a href="unlike.php?q=<?php echo $like_id; ?>"><?php echo $likecount; ?> Likes</a>
                                        <?php
                                    } else {
                                        $like_id = base64_encode($oa["id"]);
                                        ?>
                                        <a href="like.php?q=<?php echo $like_id; ?>"><?php echo $likecount; ?> Likes</a>
                                        <?php
                                    }
                                    ?>
                                    <a onclick="showComments(<?php echo $oa["id"]; ?>)" style="cursor: pointer;"><?php echo $commentcount; ?> Responses</a>
                                    <div class="commentSection" id="commentSection-<?php echo $oa["id"]; ?>">
                                        <form action="comment.php" method="POST">
                                            <input type="hidden" name="p_id" value="<?php echo $oa["id"]; ?>">
                                            <textarea name="response" rows="4" cols="50" placeholder="Response"></textarea>
                                            <br>
                                            <button type="submit" name="submit">Kirim</button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo "<p>User not found!</p>"; 
                            }
                        } else {
                            echo "<p>Error fetching user!</p>";
                        }
                    } else {
                       
                    }
                } else {
                    echo "<p>Error fetching posts for user!</p>";
                }
            }
            ?>
        </div>
        <br>

        <div id="friends">
            <?php
            if (isset($_POST["search"])) {
                $findafam_input = $_POST["findafam"];
                $findafam_low = strtolower($findafam_input);
                $findafam = mysqli_real_escape_string($conn, $findafam_low);
                $qe = "SELECT * FROM user WHERE username = '$findafam'";
                $re = mysqli_query($conn, $qe);
                $qm = "SELECT * FROM user WHERE id = $id";
                $rm = mysqli_query($conn, $qm);
                $om = $rm->fetch_assoc();
                if ($findafam == $om["username"]) {
                    $iiid = $id;
                    $encrypted_id_aa = encryptData($iiid, $key);
                    ?>
                    <a href="profile.php?q=<?php echo $encrypted_id_aa; ?>">@<?php echo $findafam; ?></a>
                    <p>Hey, this is you!</p>
                    <?php
                } else {
                    if (mysqli_num_rows($re) > 0) {
                        $user = mysqli_fetch_assoc($re);
                        $u_id = $user["id"];
                        $userB = $u_id;
                        $userA = $_SESSION["id"];
                        ?>
                        <p>@<?php echo htmlspecialchars($user["username"]); ?></p>
                        <?php
                        $ql = "SELECT * FROM friendship f1 
                                WHERE f1.user1_id = $userA 
                                AND EXISTS (
                                    SELECT 1 FROM friendship f2 
                                    WHERE f2.user1_id = $userB 
                                    AND f2.user2_id = $userA
                                )";
                        $rl = mysqli_query($conn, $ql);
                        if (mysqli_num_rows($rl) > 0) {
                            $iiiid = $id;
                            $encrypted_id_aaa = encryptData($iiiid, $key);
                            ?>
                            <a href="chat.php?q=<?php echo $encrypted_id_aaa; ?>">Whisper</a>
                            <?php
                        } else {
                            $idc = $user["id"];
                            $encrypted_id_c = encryptData($idc, $key);
                            ?>
                            <a href="addafam.php?q=<?php echo $encrypted_id_c; ?>">Connect</a>
                            <?php
                        }
                    } else {
                        ?>
                        <p>No user found!</p>
                        <?php
                    }
                }
                
            }
            ?>
            <form action="" method="POST">
                <input type="text" name="findafam" style="text-transform: lowercase;" required>
                <button type="submit" name="search">Find a fam</button>
            </form>
            <?php
                
                $qf = "SELECT * FROM friendship WHERE user2_id = $id";
                $rf = mysqli_query($conn, $qf);

                while ($of = $rf->fetch_assoc()) {
                    $user2_id = $of["user1_id"];
                    
                    $qg = "SELECT * FROM friendship WHERE user1_id = $id AND user2_id = $user2_id";
                    $rg = mysqli_query($conn, $qg);

                    if (mysqli_num_rows($rg) > 0) {
                        continue;
                    } else {
                        $qh = "SELECT * FROM user WHERE id = $user2_id";
                        $rh = mysqli_query($conn, $qh);
                        $oh = $rh->fetch_assoc();
                        $ide = $user2_id;
                        $encrypted_id_e = encryptData($ide, $key);

                        ?>
                        <p>Connect request</p>
                        <p>@<?php echo $oh["username"]; ?></p>
                        <a href="confirm_connect.php?q=<?php echo $encrypted_id_e; ?>">Confirm</a>
                        <?php
                    }
                }
                $qn = "SELECT * FROM chat WHERE receiver_id = $id AND status = 1";
                    $rn = mysqli_query($conn, $qn);
                    if (mysqli_num_rows($rn)) {
                        ?>
                        <p>Whisper received from:</p>
                        <?php
                        $displayed_senders = array();
                    
                        while ($on = $rn->fetch_assoc()) {
                            $rrid = $on["sender_id"];
                    
                            if (!in_array($rrid, $displayed_senders)) {
                                $displayed_senders[] = $rrid;
                                
                                $qo = "SELECT * FROM user WHERE id = $rrid";
                                $ro = mysqli_query($conn, $qo);
                                $oo = $ro->fetch_assoc();
                                $idb = $rrid;
                                $encrypted_id_b = encryptData($idb, $key);
                                ?>
                                <a href="chat.php?q=<?php echo $encrypted_id_b; ?>"><?php echo $oo["username"]; ?></a>
                                <br>
                                <?php
                            }
                        }
                    }
            ?>
        </div>
    </div>
    <br>
    <footer>
            <div>
                <a href="logout.php">Logout</a>
            </div>
    </footer>
</body>
</html>
