<?php
    require_once("connect.php");
    require_once("functions.php");
    session_start();
    showAlert();

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "You need to be logged to change your password.";
        header("Location: login.php");
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_pass = $_POST["new_password"];
        $confirm_pass = $_POST["confirm_password"];

        if (empty($new_pass) || empty($confirm_pass)) {
            $_SESSION['error'] = "Fill all the required fields.";
        } elseif ($new_pass !== $confirm_pass) {
            $_SESSION['error'] = "Passwords aren't the same.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_pass, $_SESSION['user_id']);

            if ($stmt->execute()) {                
                $update_count = $conn->prepare("UPDATE users SET login_count = 1 WHERE id = ?");
                $update_count->bind_param("i", $_SESSION['user_id']);
                $update_count->execute();
                $update_count->close();

                $_SESSION['login_count'] = 1;
                $_SESSION['success'] = "Succesfully changed password!";
                header("Location: dashboard.php");
                die();
            } else {
                $_SESSION['error'] = "Couldn't update password.";
            }

            $stmt->close();
        }
    }
?>

<?php 
    require_once("header.php"); 
?>
<title>DeezNutz - Change Password</title>
</head>
    <body data-bs-theme="dark">
        <main class="container d-flex justify-content-center align-items-center vh-100">

            <div class="col-sm-6">
                <form method="POST" class="form-control p-4">
                    <div class="row-sm-12 align-items-center text-center p-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor" class="bi bi-suit-spade-fill" viewBox="0 0 16 16">
                            <path d="M7.184 11.246A3.5 3.5 0 0 1 1 9c0-1.602 1.14-2.633 2.66-4.008C4.986 3.792 6.602 2.33 8 0c1.398 2.33 3.014 3.792 4.34 4.992C13.86 6.367 15 7.398 15 9a3.5 3.5 0 0 1-6.184 2.246 20 20 0 0 0 1.582 2.907c.231.35-.02.847-.438.847H6.04c-.419 0-.67-.497-.438-.847a20 20 0 0 0 1.582-2.907"/>
                        </svg>
                        <h1 class="text-center">DeezNutz</h1>
                    </div>
                    <h2 class="text-center mb-4">Change Password</h2>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New password</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm new password</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success w-100">Change Password</button>
                    </div>
                </form>
            </div>
        </main>
    </body>
</html>
