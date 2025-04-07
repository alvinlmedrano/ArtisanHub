<?php
session_start();
@include 'connection.php';

$alertMessage = "";
$usernameError = "";
$openModal = ""; // Variable to store the modal ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["userType"];
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validate input
    if (empty($username) || empty($password)) {
        $alertMessage = "All fields are required!";
    } else {
        $table = ($userType === "artisan") ? "artisanaccount" : "guestaccount";
        $openModal = ($userType === "artisan") ? "artisanModal" : "guestModal"; // Store which modal to open
        
        // Check if username already exists
        $checkQuery = "SELECT * FROM $table WHERE username = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $usernameError = "Username is already taken!";
        } else {
            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO $table (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ss", $username, $hashedPassword);
                if ($stmt->execute()) {
                    $alertMessage = "Registration Successful!";
                    
                    // Redirect to artisanpage.php after successful registration
                    header("Location: artisan-main/artisanPage.php");
                    exit();
                } else {
                    $alertMessage = "Error: Something went wrong!";
                }
                $stmt->close();
            } else {
                $alertMessage = "Database error. Try again later.";
            }
        }
        $checkStmt->close();
    }
}

$conn->close();
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
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>

    <section>

        <!-- HEADER -->
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

    <br><br><br><br><br><br><br><br>
    <div class="container">
        <div class="card artisan" onclick="openModal('artisanModal')">
            <p>Create account for</p>
            <h2>ARTISAN</h2>
        </div>
        <div class="card guest" onclick="openModal('guestModal')">
            <p>Create account for</p>
            <h2>GUEST</h2>
        </div>
    </div>


    <!-- Artisan Modal -->
    <div class="modal" id="artisanModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('artisanModal')">&times;</span>
            <p class="artiwel">Creating an account as an</p>
            <p class="artisan-title">ARTISAN</p>
            <br><br>
            <form action="signup.php" method="POST">
                <input type="hidden" name="userType" value="artisan">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="text" name="password" placeholder="Password" required>
                </div>
                <?php if (!empty($usernameError)) { ?>
                    <p class="error-message"><?php echo $usernameError; ?></p>
                <?php } ?>
                <br>
                <button type="submit" class="create-btn">CREATE NOW</button>
            </form>
        </div>
    </div>

    <!-- Guest Modal -->
    <div class="modal" id="guestModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('guestModal')">&times;</span>
            <p class="artiwel">Creating an account as a</p>
            <p class="artisan-title">GUEST</p>
            <br><br>
            <form action="signup.php" method="POST">
                <input type="hidden" name="userType" value="guest">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="text" name="password" placeholder="Password" required>
                </div>
                <?php if (!empty($usernameError)) { ?>
                    <p class="error-message"><?php echo $usernameError; ?></p>
                <?php } ?>
                <br>
                <button type="submit" class="create-btn">CREATE NOW</button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Automatically open the modal if there's a username error
        window.onload = function() {
            var openModalId = "<?php echo $openModal; ?>";
            if (openModalId) {
                openModal(openModalId);
            }
        };
    </script>

    <br><br><br><br><br><br><br>

    <!-- FOOTER -->
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
