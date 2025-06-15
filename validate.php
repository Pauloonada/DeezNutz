<?php
    require_once("functions.php");
    require_once("connect.php");
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_SESSION['user_id'])){
            $_SESSION['error'] = "You are already logged in.";
            header("Location: login.php");
            die();
        }
        if($_POST['action'] == "Register") { // REGISTRATION
            // Registration logic
            $username = $_POST['user'];
            $email = $_POST['email'];
            $is_admin = isset($_POST['admin_checkbox']) ? 1 : 0;
            $password = firstPassword($username);
            $passwordhash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $username, $email, $passwordhash, $is_admin);
            if ($stmt->execute()) {
                $last_id = $conn->insert_id; // Stores the latest user ID in the session

                $query = $conn->prepare("SELECT username, email, login_count FROM users WHERE id = ?");
                $query->bind_param("i", $last_id);
                $query->execute();
                $query->bind_result($uname, $uemail, $login_count);
                $query->fetch();

                $_SESSION['user_id'] = $last_id;
                $_SESSION['username'] = $uname;
                $_SESSION['user_email'] = $uemail;
                $_SESSION['is_admin'] = $is_admin;
                $_SESSION['login_count'] = $login_count;
                $_SESSION['success'] = "User registered successfully!";

                $query->close();

                header("Location: change_password.php");
                die();
            } else {
                $_SESSION['error'] = "Couldn't register user: " . $stmt->error;

                header("Location: register.php");
                die();
            }

            // Closes the statement
            $stmt->close();
        }

        elseif ($_POST['action'] == "Login") { // LOGIN
            // Login logic
            $user = $_POST['user'];
            $pass = $_POST['pass'];

            // Fuck SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();

            // Stores the result
            $res = $stmt->get_result();

            // Check if it returns a row
            if($res->num_rows > 0){
                $res = $res->fetch_assoc();

                if($res['is_locked']){
                    $lastAttemptTime = strtotime($res['last_attempt']);

                    // Unlocks after 30 minutes
                    if(time() - $lastAttemptTime > 30 * 60){
                        // Unlocking
                        $resetLock = $conn->prepare("UPDATE users SET is_locked = 0, login_attempts = 0, last_attempt = NULL WHERE id = ?");
                        $resetLock->bind_param("i", $user['id']);
                        $resetLock->execute();

                        $res['is_locked'] = false;
                        $res['login_attempts'] = 0;
                    }
                    
                    else{
                        $_SESSION['error'] = "Your account is locked due to multiple failed login attempts. Please try again later.";
                        header("Location: login.php");
                        die();
                    }
                }

                if(password_verify($pass, $res['password'])){ // Checks the hashed passwords
                    // Reseting login attempts
                    $resetAttempts = $conn->prepare("UPDATE users SET login_attempts = 0, last_attempt = NULL WHERE id = ?");
                    $resetAttempts->bind_param("i", $res['id']);
                    $resetAttempts->execute();

                    $_SESSION['user_id'] = $res['id'];
                    $_SESSION['username'] = $res['username'];
                    $_SESSION['user_email'] = $res['email'];
                    $_SESSION['login_count'] = $res['login_count'];                    

                    if($res['login_count'] == 0){
                        header("Location: change_password.php");
                    }

                    else{
                        $update_count = $conn->prepare("UPDATE users SET login_count = login_count + 1 WHERE id = ?");
                        $update_count->bind_param("i", $res['id']);
                        $update_count->execute();
                        $update_count->close();

                        header("Location: dashboard.php");
                    }
                    die();
                }
                else {
                    // Wrong password, so we gotta fuck'em up ;-;
                    $nowDateTime = date('Y-m-d H:i:s');
                    $login_attempts = $res['login_attempts'];
                    $last_attempt = $res['last_attempt'];

                    // If last attempt was more than 30 mins ago, clear the counter. We do that so the user doesn't get blocked by typing a wrong password only one time after the cooldown.
                    if ($last_attempt === null || strtotime($last_attempt) < (time() - 30*60)) {
                        $login_attempts = 1;
                    } else {
                        $login_attempts++;
                    }

                    if($login_attempts >= 3){
                        $lockUser = $conn->prepare("UPDATE users SET is_locked = 1, login_attempts = ?, last_attempt = ? WHERE id = ?");
                        $lockUser->bind_param("isi", $login_attempts, $nowDateTime, $res['id']);
                        $lockUser->execute();

                        $_SESSION['error'] = "Too many failed login attempts. Your account is locked for 30 minutes.";
                    }

                    else{
                        $updateAttempts = $conn->prepare("UPDATE users SET login_attempts = ?, last_attempt = ? WHERE id = ?");
                        $updateAttempts->bind_param("isi", $login_attempts, $nowDateTime, $res['id']);
                        $updateAttempts->execute();

                        $_SESSION['error'] = "Invalid password.";
                    }
                    header("Location: login.php");
                    die();
                }
            } else {
                $_SESSION['error'] = "User not found.";
                header("Location: login.php");
                die();
            } 

            // Close the statement
            $stmt->close();
        }
    }

    else{
        header("Location: login.php");
        $_SESSION['error'] = "Invalid request.";
        die();
    }