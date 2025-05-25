<?php 
    session_start();
    require_once("functions.php");
    checkLogin();
    showAlert();
    require_once("header.php"); 
?>
    <title>DeezNutz - Dashboard</title>
    </header>
    <body data-bs-theme="dark">
        <main class="container d-flex justify-content-center align-items-center vh-100">
            <div class="col-sm-6">
                <div class="row-sm-12 align-items-center text-center p-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor" class="bi bi-suit-spade-fill" viewBox="0 0 16 16">
                        <path d="M7.184 11.246A3.5 3.5 0 0 1 1 9c0-1.602 1.14-2.633 2.66-4.008C4.986 3.792 6.602 2.33 8 0c1.398 2.33 3.014 3.792 4.34 4.992C13.86 6.367 15 7.398 15 9a3.5 3.5 0 0 1-6.184 2.246 20 20 0 0 0 1.582 2.907c.231.35-.02.847-.438.847H6.04c-.419 0-.67-.497-.438-.847a20 20 0 0 0 1.582-2.907"/>
                    </svg>
                    <h1 class="text-center">DeezNutz</h1>
                </div>
                <h2 class="text-center mb-4">Dashboard</h2>
                <div class="col-sm-12 p-3">
                    <h3 class="text-center">Welcome, <?= $_SESSION['username']; ?>!</h3>
                    <p class="text-center">Your login count is: <?= $_SESSION['login_count']; ?></p>
                    <p class="text-center">Your email is: <?= $_SESSION['user_email']; ?></p>
                    <p class="text-center">
                        Your password is: <span id="password-text">••••••••</span> 
                        <button id="reveal-btn" class="btn btn-sm btn-outline-light ms-2" onclick="">Show</button>
                    </p>
            </div>
        </main>
        <a href="logout.php" class="btn btn-danger position-fixed bottom-0 end-0 m-4 shadow">
            <i class="bi bi-door-open"></i> Logout
        </a>
        <script src="./scripts/showPassword.js"></script>
    </body>
</html>