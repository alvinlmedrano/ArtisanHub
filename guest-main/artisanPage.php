<?php
session_start();
@include 'connection.php';

if (!isset($_SESSION['guestId'])) {
    header("Location: ../login.php");
    exit();
}

$guestId = $_SESSION['guestId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type']) && $_POST['form_type'] === "edit_details") {
        @include 'connection.php';

        $name = $_POST['name'];
        $interest = $_POST['interest'];

        // Check if artisan details exist
        $query = "SELECT COUNT(*) as count, profile_image FROM guestprofile WHERE guestId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $guestId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        $count = $data['count'];
        $currentProfileImage = $data['profile_image'] ?? 'default.png'; // Get current image
        mysqli_stmt_close($stmt);

        // Handle profile image upload
        if (!empty($_FILES['profile_image']['name'])) {
            $profile_image = $_FILES['profile_image']['name'];
            $profile_image_tmp_name = $_FILES['profile_image']['tmp_name'];
            $profile_image_folder = 'profile_img/' . basename($profile_image);
        
            if (move_uploaded_file($profile_image_tmp_name, $profile_image_folder)) {
                $profile_image = basename($profile_image);
            } else {
                $profile_image = $currentProfileImage; // Keep the current image if upload fails
            }
        } else {
            $profile_image = $currentProfileImage; // Keep the current image if no new file is uploaded
        }


        if ($count == 0) {
            // Insert new record if no existing details
            $query = "INSERT INTO guestprofile (guestId, name, interest, profile_image) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "isss", $guestId, $name, $interest, $profile_image);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            // Update existing record
            $query = "UPDATE guestprofile SET name=?, interest=?, profile_image=? WHERE guestId=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $name,$interest, $profile_image, $guestId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        header('Location: artisanPage.php?openModal=editDeets');
        exit();

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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=delete" />
    <link rel="stylesheet" href="css/artisanpage.css">
</head>
<body>

    <section>
        <!-- HEADER -->
    <div id="1" class="header"></div>
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
    
    <div class="profile-container">
            <div class="profile-header">
                <h2>MY PROFILE</h2>
                <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Log out</a>
            </div>

            <div class="profile-content">
                <?php
                @include 'connection.php';
                $query = "SELECT * FROM guestprofile WHERE guestId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $guestId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $profile = mysqli_fetch_assoc($result) ?? [
                    'profile_image' => 'default.png',
                    'name' => 'No Name Yet',
                    'interest' => 'No interest Yet'
        
                ];
                mysqli_stmt_close($stmt);
                ?>
                    <img src="profile_img/<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile Image">
             
                    <div class="profile-details">
                    <p><strong>NAME:</strong> <span class="profile-value"><?php echo htmlspecialchars($profile['name']); ?></span></p>
                    <p><strong>INTEREST:</strong> <span class="profile-value"><?php echo htmlspecialchars($profile['interest']); ?></span></p>

                    <div class="profile-buttons">
                            <button class="btn" onclick="openModal('editDeets')">Edit details</button>
                        </div>
                    </div>
            </div>
        </div>

<!-- Edit Details Modal -->
<div class="modal2" id="editDeets">
    <div class="modal-content2">
        <span class="close" onclick="closeModal('editDeets')">&times;</span>

        <div class="leftbbox">
            <form action="artisanPage.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="edit_details">

                    <p class="unaA">Basic Information</p>
                    <h2 class="modalttitle">GUEST</h2> <br><br>
                    
                    <label for="name">NAME</label>
                    <input type="text" class="leftInput" id="name" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required><br><br>
                    
                    <label for="interest">INTEREST</label><br>
                    <textarea name="interest" id="interest" rows="4" value="<?php echo htmlspecialchars($profile['interest']); ?>" required></textarea>
                    <div class="weyt">
                        <button type="submit" class="savebbtn">SAVE</button>
                    </div>
        </div>

        <div class="rightbbox">
            <br>
            <div class="inputggroup">
                <label for="profile">PROFILE</label>
                <input type="file" id="profilePhoto" class="wowers" name="profile_image" accept="image/*" onchange="previewImage(event)">
                <label for="productPhoto" class="uploadpplaceholder">
                <img id="profilePreview" src="profile_img/<?php echo htmlspecialchars($profile['profile_image']); ?>" class="preview-img45">
                <p>Click to add photo</p>
                </label>
            </div>
        </div>
        </form>
    </div>
</div>

<script> 
    // Function to open the modal
    function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        // Function to close the modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

    function previewProfileImage(event) {
        const input = event.target;
        const preview = document.getElementById("profilePreview");
        const text = input.nextElementSibling.querySelector("p");

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                text.style.display = "none"; // Hide the text
                preview.style.display = "block"; // Ensure the image is visible
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>


<br><br><br><br><br><br><br><br><br><br>

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
