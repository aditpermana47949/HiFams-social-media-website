<?php
session_start();
require "functions.php";
require "key.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["q"])) {
    $decrypted_id = $_GET["q"];
    $id = decryptData($decrypted_id, $key);

    $uuid = $_SESSION["id"];

    $q = "SELECT * FROM user WHERE id = $id";
    $r = mysqli_query($conn, $q);
    if (!$r) {
        die("Error fetching user: " . mysqli_error($conn));
    }
    $o = $r->fetch_assoc();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
        <style>
            .commentSection {
                display: none;
            }
        </style>
        <link rel="stylesheet" href="profile.css">
    </head>
    <body>
        <header>
            <div><a href="index.php">HiFams</a></div>
            <div>
                <?php
                $encrypted_id = encryptData($uuid, $key);
                ?>
                <a href="profile.php?q=<?php echo $encrypted_id; ?>">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </header>
        <main>
            <div id="profile_box">
                <h2>@<?php echo htmlspecialchars($o["username"]); ?></h2>
                <?php
                $ql = "SELECT * FROM user_detail WHERE user_id = $id";
                $rl = mysqli_query($conn, $ql);
                $ol = $rl -> fetch_assoc();
                ?>
                <p><?php echo htmlspecialchars(isset($ol["name"]) ? $ol["name"] : ''); ?></p>
                <p><?php echo htmlspecialchars(isset($ol["surname"]) ? $ol["surname"] : ''); ?></p>
                <p><?php echo htmlspecialchars(isset($ol["gender"]) ? $ol["gender"] : ''); ?></p>
                <p><?php echo htmlspecialchars(isset($ol["nationality"]) ? $ol["nationality"] : ''); ?></p>
                <p><?php echo htmlspecialchars(isset($ol["bio"]) ? $ol["bio"] : ''); ?></p>
                <br><br>
                <?php
                if ($id == $uuid) {
                    $encrypted_id_a = encryptData($id, $key);
                    ?><a href="edit_detail.php?q=<?php echo $encrypted_id_a; ?>">Edit your profile</a>
                    <?php
                } elseif (isFriend($uuid, $id) == true) {
                    $iiid = $id;
                    $encrypted_id_aas = encryptData($iiid, $key);
                    ?>
                    <a href="chat.php?q=<?php echo $encrypted_id_aas; ?>">Whisper</a>
                    <?php
                } elseif (isFriend($uuid, $id) == false) {
                    ?>
                    <a href="addafam.php?q=<?php echo urlencode($id); ?>">Connect</a>
                    <?php
                }
                ?>
            </div>
            <div id="content">
                <?php
                if (isFriend($uuid, $id) == true || $uuid == $id) {
                ?>
                <div id="timeline">
                    <h2>Voices</h2>
                    <?php
                    $qa = "SELECT * FROM posts WHERE user_id = $id ORDER BY datetime DESC";
                    if ($ra = mysqli_query($conn, $qa)) {
                        while ($oa = $ra->fetch_assoc()) {
                            $p_id = $oa["id"];

                            $qi = "SELECT COUNT(*) as likecount FROM likes WHERE posts_id = $p_id";
                            $ri = mysqli_query($conn, $qi);
                            $oi = $ri->fetch_assoc();
                            $likecount = $oi["likecount"];

                            $qk = "SELECT COUNT(*) as commentcount FROM comments WHERE posts_id = $p_id";
                            $rk = mysqli_query($conn, $qk);
                            $ok = $rk->fetch_assoc();
                            $commentcount = $ok["commentcount"];
                            ?>
                            <div class="post">
                                <p>@<?php echo htmlspecialchars($o["username"]); ?></p>
                                <p><?php echo htmlspecialchars($oa["datetime"]); ?></p>
                                <p><?php echo $oa["content"]; ?></p>
                                <?php 
                                if ($id == $_SESSION["id"]) {
                                    ?>
                                    <p>
                                        <a href="yourpost.php?q=<?php echo $oa["id"]; ?>"><?php echo $likecount; ?> Like</a>
                                        <a href="yourpost.php?q=<?php echo $oa["id"]; ?>"><?php echo $commentcount; ?> Responses</a>
                                    </p>
                                    <?php
                                } else {
                                    $uid = $_SESSION["id"];
                                    $qj = "SELECT * FROM likes WHERE posts_id = $p_id AND user_id = $uid";
                                    $rj = mysqli_query($conn, $qj);
                                    if (mysqli_num_rows($rj) > 0) {
                                        $like_id = base64_encode($oa["id"]);
                                        ?>
                                        <a href="unlike.php?q=<?php echo $like_id; ?>&s=<?php echo $id; ?>"><?php echo $likecount; ?> Like</a>
                                        <?php
                                    } else {
                                        $like_id = base64_encode($oa["id"]);
                                        ?>
                                        <a href="like.php?q=<?php echo $like_id; ?>&s=<?php echo $id; ?>"><?php echo $likecount; ?> Like</a>
                                        <?php
                                    }
                                    ?>
                                    <a onclick="showComments(<?php echo $oa["id"]; ?>)" style="cursor: pointer;"><?php echo $commentcount; ?> Responses</a>
                                    <div id="commentSection_<?php echo $oa["id"]; ?>" class="commentSection">
                                        <form action="comment.php" method="POST">
                                            <input type="hidden" name="p_id" value="<?php echo $oa["id"]; ?>">
                                            <input type="hidden" name="s" value="<?php echo $id; ?>">
                                            <textarea name="response" rows="4" cols="50" placeholder="Response"></textarea>
                                            <br>
                                            <button type="submit" name="submit">Kirim</button>
                                        </form>
                                    </div>
                                    <script>
                                        function showComments(postId) {
                                            var sections = document.querySelectorAll('.commentSection');
                                            sections.forEach(function(section) {
                                                section.style.display = 'none';
                                            });
                                            var commentSection = document.getElementById('commentSection_' + postId);
                                            if (commentSection) {
                                                commentSection.style.display = 'block';
                                            }
                                        }
                                    </script>
                                    <?php
                                }
                                ?>
                                
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Error fetching posts!</p>";
                    }
                    ?>
                </div>
                <?php
                } elseif (isFriend($uuid, $id) == false) {
                    ?>
                    <div id="timeline">
                        <h2>Voices</h2>
                        <?php
                        $qa = "SELECT * FROM posts WHERE user_id = $id ORDER BY datetime DESC";
                        if ($ra = mysqli_query($conn, $qa)) {
                            while ($oa = $ra->fetch_assoc()) {
                                $p_id = $oa["id"];
                
                                $qi = "SELECT COUNT(*) as likecount FROM likes WHERE posts_id = $p_id";
                                $ri = mysqli_query($conn, $qi);
                                $oi = $ri->fetch_assoc();
                                $likecount = $oi["likecount"];
                
                                $qk = "SELECT COUNT(*) as commentcount FROM comments WHERE posts_id = $p_id";
                                $rk = mysqli_query($conn, $qk);
                                $ok = $rk->fetch_assoc();
                                $commentcount = $ok["commentcount"];
                                ?>
                                <div class="post">
                                    <p>@<?php echo htmlspecialchars($o["username"]); ?></p>
                                    <p><?php echo htmlspecialchars($oa["datetime"]); ?></p>
                                    <p><?php echo htmlspecialchars($oa["content"]); ?></p>
                                    <?php 
                                        $uid = $_SESSION["id"];
                                        $qj = "SELECT * FROM likes WHERE posts_id = $p_id AND user_id = $uid";
                                        $rj = mysqli_query($conn, $qj);
                                        $liked = mysqli_num_rows($rj) > 0;
                                        ?>
                                        <a><?php echo $likecount; ?> Likes</a>
                                        <a><?php echo $commentcount; ?> Responses</a>
                                        <?php if ($liked) { ?>
                                            <span>Liked</span>
                                        <?php } else { ?>
                                            <span>Not Liked</span>
                                        <?php } ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>Error fetching posts!</p>";
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
                <div id="friendlist">
                    <?php
                    if ($id == $_SESSION["id"]) {
                        ?> <h3>Fam List</h3>
                        <?php
                    } else {
                        ?>
                        <h3><?php echo htmlspecialchars($o["username"]); ?>'s Fam Lists</h3>
                        <?php
                    }
                    $qa = "SELECT * FROM friendship f1 
                                    WHERE f1.user1_id = $id 
                                    AND EXISTS (
                                        SELECT 1 FROM friendship f2 
                                        WHERE f2.user1_id = f1.user2_id 
                                        AND f2.user2_id = $id
                                    )";
                    // $qa = "SELECT * FROM friendship WHERE user1_id = $id";
                    $ra = mysqli_query($conn, $qa);
                    if (mysqli_num_rows($ra) > 0) {
                        while ($oa = $ra->fetch_assoc()) {
                            $sa = $oa["user2_id"];
                            $qb = "SELECT * FROM user WHERE id = $sa";
                            $rb = mysqli_query($conn, $qb);
                            $ob = $rb->fetch_assoc();
                            $username_f = $ob["username"];
                            $encrypted_id = encryptData($sa, $key);
                            ?>
                            <a href="profile.php?q=<?php echo htmlspecialchars($encrypted_id); ?>"><?php echo htmlspecialchars($username_f); ?></a><br>
                            <?php
                        }
                    } else {
                        echo "<p>No friends found!</p>";
                    }
                    ?>
                </div>
            </div>
        </main>
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
    exit;
}
?>
