<?php
    require "conn.php";
    function usernamecheck($username) {
        global $conn;
        $q = "SELECT * FROM user WHERE username = '$username'";
        $r = mysqli_query($conn, $q);
        return mysqli_num_rows($r) === 0; // Jika username tidak ada, return true
    }
    
    function encryptData($data, $key) {
        $cipher = "aes-256-cbc";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($data, $cipher, $key, $options=0, $iv);
    
        if ($ciphertext === false) {
            throw new Exception('Encryption failed.');
        }
    
        return base64_encode($iv.$ciphertext);
    }
    
    function decryptData($encoded_data, $key) {
        $cipher = "aes-256-cbc";
        $data = base64_decode($encoded_data);
    
        if ($data === false) {
            throw new Exception('Base64 decoding failed.');
        }
    
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivlen);
        $ciphertext = substr($data, $ivlen);
        $plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
    
        if ($plaintext === false) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            throw new Exception('Decryption failed.');
        }
    
        return $plaintext;
    }

    function isFriend($userA, $userB) {
        global $conn;

        $userA = mysqli_real_escape_string($conn, $userA);
        $userB = mysqli_real_escape_string($conn, $userB);
    
        $q = "SELECT 1 FROM friendship f1 
              WHERE f1.user1_id = $userA 
              AND EXISTS (
                  SELECT 1 FROM friendship f2 
                  WHERE f2.user1_id = $userB 
                  AND f2.user2_id = $userA
              )";
        $r = mysqli_query($conn, $q);
    
        // Memeriksa hasil query
        if ($r && mysqli_num_rows($r) > 0) {
            return true;
        } else {
            return false;
        }
    }
    
?>