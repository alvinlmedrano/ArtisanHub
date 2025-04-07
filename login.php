<?php
session_start();
require_once 'connection.php'; // Use require_once to ensure the file loads properly

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        // Check in artisanaccount first
        $query = "SELECT artisanId, username, password FROM artisanaccount WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) { // Ensure database has hashed passwords
                $_SESSION['artisanId'] = $user['artisanId'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['userType'] = 'artisan';

                // Debugging: Remove after confirming login works
                // var_dump($_SESSION); exit();

                header("Location: artisan-main/artisanPage.php");
                exit();
            }
        }

        // If not found in artisanaccount, check guestaccount
        $query = "SELECT guestId, username, password FROM guestaccount WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $guest = $result->fetch_assoc();
            if (password_verify($password, $guest['password'])) { // Ensure passwords are hashed
                $_SESSION['guestId'] = $guest['guestId'];
                $_SESSION['username'] = $guest['username'];
                $_SESSION['userType'] = 'guest';

                // Debugging: Remove after confirming login works
                // var_dump($_SESSION); exit();

                header("Location: guest-main/artisanPage.php");
                exit();
            }
        }

        $error = "Incorrect username or password"; // General error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Register </title>
    <script src="js/jQuery3.4.1.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

    <section>

        <div id="1"></div>
        <script>
            load("header.html");
            function load(url){
                req = new XMLHttpRequest();
                req.open("GET", url, false);
                req.send(null);
                document.getElementById(1).innerHTML = req.responseText;
            }
        </script>

        <script src="js/header.js"></script>
        
        <div class="container">
            <div class="left-section">
                <p>No account yet? <a href="signup.php" style="text-decoration: none; font-weight: bold;">Click Here</a></p>
                <h1>CREATE ACCOUNT</h1>
                <h1>NOW!</h1>
            </div>
            <div class="right-section">
                <h2>WELCOME BACK!</h2><br>
                <form action="" method="POST">
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

                    <button class="login-btn" type="submit">Login</button>
                </form>
            </div>
        </div>

        <div id="4"></div>
        <script>
            load("footer.html");
            function load(url)
            {
                req = new XMLHttpRequest();
                req.open("GET", url, false);
                req.send(null);
                document.getElementById(4).innerHTML = req.responseText;
            }
        </script>

    </section>

</body>
</html>
