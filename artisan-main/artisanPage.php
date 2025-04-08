<?php
    session_start();
    @include 'connection.php';

    if (!isset($_SESSION['artisanId'])) {
        header("Location: ../login.php");
        exit();
    }

    $artisanId = $_SESSION['artisanId'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['form_type']) && $_POST['form_type'] === "edit_details") {
            @include 'connection.php';

            $name = $_POST['name'];
            $age = $_POST['age'];
            $address = $_POST['address'];

            // Check if artisan details exist
            $query = "SELECT COUNT(*) as count, profile_image FROM artisanprofile WHERE artisanId = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $artisanId);
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
                $query = "INSERT INTO artisanprofile (artisanId, name, age, address, profile_image) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "issss", $artisanId, $name, $age, $address, $profile_image);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                // Update existing record
                $query = "UPDATE artisanprofile SET name=?, age=?, address=?, profile_image=? WHERE artisanId=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sissi", $name, $age, $address, $profile_image, $artisanId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            header('Location: artisanPage.php?openModal=editDeets');
            exit();

        } elseif (isset($_POST['form_type']) && $_POST['form_type'] === "add_product") {
            $product_name = $_POST['product_name'];
            $category = $_POST['category'];
            $description = $_POST['description'];

        
            // Handle product image upload
            if (!empty($_FILES['product_image']['name'])) {
                $product_image = $_FILES['product_image']['name'];
                $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
                $product_image_folder = 'uploaded_img/' . basename($product_image);
        
                if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
                    $product_image = basename($product_image);
                } else {
                    $product_image = "default_product.png";
                }
            } else {
                $product_image = "default_product.png";
            }
        
            // Insert product
            $query = "INSERT INTO product (artisanId, nameOfProduct, category, productDescription, productImage) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "issss", $artisanId, $product_name, $category, $description, $product_image);
        
            if (mysqli_stmt_execute($stmt)) {
                header("Location: artisanPage.php?success=ProductAdded&openModal=addProductModal");
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        
            mysqli_stmt_close($stmt);
            exit();
        }elseif (isset($_POST['form_type']) && $_POST['form_type'] === "add_story") {


                if (!isset($_POST['title_story'], $_POST['category'], $_POST['place'], $_POST['description'])) {
                    die("Missing required fields.");
                }
            
                $title = trim($_POST['title_story']);
                $category = trim($_POST['category']);
                $place = trim($_POST['place']);
                $description = trim($_POST['description']);
            
                // Validate inputs (optional but recommended)
                if (empty($title) || empty($category) || empty($place) || empty($description)) {
                    die("All fields are required.");
                }
            
                // Handle image upload securely
                $default_image = "default_story.png";
                $artisan_image = $default_image;

                if (!empty($_FILES['artisan_image']['name'])) {
                    // Generate a unique filename
                    $artisan_image = uniqid('story_') . '.' . pathinfo($_FILES['artisan_image']['name'], PATHINFO_EXTENSION);
                    $artisan_image_folder = 'artisan_image/' . $artisan_image;

                    // Attempt to move the uploaded file
                    if (!move_uploaded_file($_FILES['artisan_image']['tmp_name'], $artisan_image_folder)) {
                        $artisan_image = $default_image; // Revert to default if upload fails
                    }
                }
            
                // Insert story into database
                $query = "INSERT INTO artisanstories (artisanId, title, category, place, story, artisanPhoto) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
            
                if (!$stmt) {
                    die("Statement preparation failed: " . mysqli_error($conn));
                }
            
                mysqli_stmt_bind_param($stmt, "isssss", $artisanId, $title, $category, $place, $description, $artisan_image);
            
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: artisanPage.php?success=StoryAdded&openModal=addStory");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            
                mysqli_stmt_close($stmt);
            }elseif (isset($_POST['form_type']) && $_POST['form_type'] === "edit_product") { //Edit Products and Update
                $productId = intval($_POST['productId']);
                $product_name = $_POST['product_name'];
                $category = $_POST['category'];
                $description = $_POST['description'];
            
                // Handle product image upload
                if (!empty($_FILES['product_image']['name'])) {
                    $product_image = $_FILES['product_image']['name'];
                    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
                    $product_image_folder = 'uploaded_img/' . basename($product_image);
            
                    if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
                        $product_image = basename($product_image);
                    } else {
                        $product_image = "default_product.png"; // Use default if upload fails
                    }
                } else {
                    // Keep existing image if no new file is uploaded
                    $query = "SELECT productImage FROM product WHERE productId = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "i", $productId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    $product_image = $row['productImage'] ?? "default_product.png";
                    mysqli_stmt_close($stmt);
                }
            
                // Update product details in database
                $query = "UPDATE product SET nameOfProduct=?, category=?, productDescription=?, productImage=? WHERE productId=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssssi", $product_name, $category, $description, $product_image, $productId);
            
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: artisanPage.php?success=ProductUpdated&openModal=editProductModal");
                } else {
                    echo "Error updating product: " . mysqli_error($conn);
                }
            
                mysqli_stmt_close($stmt);
                exit();
            }elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['storyId'])) {
                $storyId = intval($_POST['storyId']);
                // Handle story deletion if a request is made
                $query = "DELETE FROM artisanstories WHERE storyId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $storyId);
            
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: artisanPage.php?success=StoryDeleted");
                    exit();
                } else {
                    echo "Error deleting story: " . mysqli_error($conn);
                }
            
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            }elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productId'])) {
                $productId = intval($_POST['productId']);

                $query = "DELETE FROM product WHERE productId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $productId);
            
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: artisanPage.php?success=ProductDeleted");
                    exit();
                } else {
                    echo "Error deleting story: " . mysqli_error($conn);
                }
            
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                exit;
            }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
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
                $query = "SELECT * FROM artisanprofile WHERE artisanId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $artisanId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $profile = mysqli_fetch_assoc($result) ?? [
                    'profile_image' => 'default.png',
                    'name' => 'No Name Yet',
                    'age' => 'No Age Yet',
                    'address' => 'No Address Yet'
                ];
                mysqli_stmt_close($stmt);
                ?>
                    <img src="profile_img/<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile Image">
             
                    <div class="profile-details">
                    <p><strong>NAME:</strong> <span class="profile-value"><?php echo htmlspecialchars($profile['name']); ?></span></p>
                    <p><strong>AGE:</strong> <span class="profile-value"><?php echo htmlspecialchars($profile['age']); ?></span></p>
                    <p><strong>ADDRESS:</strong> <span class="profile-value"><?php echo htmlspecialchars($profile['address']); ?></span></p>

                    <div class="profile-buttons">
                            <button class="btn" onclick="openModal('editDeets')">Edit details</button>
                            <button class="btn" onclick="openModal('addProductModal')">Add Product</button>
                            <button class="btn" onclick="openModal('addStory')">Add Story</button>
                        </div>
                    </div>
            </div>

            <h2>Products</h2>
        </div>

        <div class="small-container">
            <div class="row">
                <?php
                $query = "SELECT * FROM product WHERE artisanId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $artisanId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($product = mysqli_fetch_assoc($result)) {
                ?>
                <div class="product-item">
                    <div class="product-card">
                        <img src="uploaded_img/<?php echo htmlspecialchars($product['productImage']); ?>" alt="<?php echo htmlspecialchars($product['nameOfProduct']); ?>" class="product-image">
                        <button class="product-name"><?php echo htmlspecialchars($product['nameOfProduct']); ?></button>
                    </div>

                    <div class="product-actions">
                        <button class="edit-btn" onclick="openEditProductModal(<?php echo $product['productId']; ?>)">Edit Product</button>
                        <button class="delete-btn"onclick="confirmDeleteProduct(<?php echo $product['productId']; ?>)">Delete Product</button>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<p>No products available.</p>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </div>

            <script>
                function openModal(modalId) {
                    document.getElementById(modalId).style.display = "block";
                }
                function closeModal(modalId) {
                    document.getElementById(modalId).style.display = "none";
                }
            </script>

            
            <h2>Stories</h2>

            <div class="row">
                <?php
                $query = "SELECT * FROM artisanstories WHERE artisanId = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $artisanId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($product = mysqli_fetch_assoc($result)) {
                ?>
                <div class="product-item">
                    <div class="product-card">
                        <img src="artisan_image/<?php echo htmlspecialchars($product['artisanPhoto']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image">
                        
                        <button class="product-name"><?php echo htmlspecialchars($product['title']); ?></button>
                    </div>

                    <div class="product-actions">
                        <button class="edit-btn" onclick="openEditProductModal(<?php echo $product['storyId']; ?>)">Edit Stories</button>
                        <button class="delete-btn"onclick="confirmDeleteStories(<?php echo $product['storyId']; ?>)">Delete Stories</button>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<p>No stories yet.</p>";
                }
                mysqli_stmt_close($stmt);
                ?>
            </div>
        </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
    </script>


    <!-- Edit Product Modal -->
    <div class="modalEDIT" id="edit_product">
        <div class="modal-contentEDIT">
            <span class="close" onclick="closeModal('edit_product')">&times;</span>

            <div class="left-boxEDIT">
                <h2 class="modal-titleEDIT">Edit Product</h2>
                <form action="artisanPage.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="edit_product">
                    <input type="hidden" name="productId" id="editProductId">

                    <div class="input-group23EDIT">
                        <label>Enter product name:</label>
                        <input type="text" name="product_name" id="editProductName" required>
                    </div>

                    <div class="input-group23EDIT">
                        <label>Select category of the craft:</label>
                        <select name="category" id="editProductCategory" required>
                            <option value="">-- Select Category --</option>
                            <option value="TextileandWeavingCrafts">Textile and Weaving Crafts</option>
                            <option value="PotteryandCeramics">Pottery and Ceramics</option>
                            <option value="WoodcraftandCarving">Woodcraft and Carving</option>
                            <option value="Metalcraft">Metalcraft</option>
                            <option value="BambooandRattanCrafts">Bamboo and Rattan Crafts</option>
                            <option value="Shell and Coral Crafts">Shell and Coral Crafts</option>
                            <option value="Paper and Fiber Crafts">Paper and Fiber Crafts</option>
                            <option value="Indigenous and Traditional Crafts">Indigenous and Traditional Crafts</option>
                        </select>
                    </div>

                    <div class="input-group23EDIT">
                        <label>Add Product Photo:</label>
                        <input type="file" id="editProductPhoto" class="wowers" name="product_image" accept="image/*" onchange="previewProductImage(event)">
                        <label for="editProductPhoto" class="upload-placeholder">
                            <img id="editImagePreview" src="" class="preview-img12">
                            <p>Click to upload photo</p>
                        </label>
                    </div>
            </div>

            <div class="right-boxEDIT">
                <div class="input-grouprightEDIT"><br><br><br><br><br>
                    <textarea name="description" id="editProductDescription" rows="4" placeholder="Provide description about the product..." required></textarea>
                </div>
                <button type="submit" class="save-btn">Update</button>
            </div>
            </form>
        </div>
    </div>

    
    <script>
        function openEditProductModal(productId) {
            // Get modal element
            let modal = document.getElementById('edit_product');

            // Ensure modal exists
            if (!modal) {
                console.error("Modal with ID 'edit_product' not found.");
                return;
            }

            // Set productId in hidden input
            document.getElementById('editProductId').value = productId;

            // Fetch product details using AJAX (optional)
            fetch(`getProductDetails.php?productId=${productId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editProductName').value = data.nameOfProduct;
                    document.getElementById('editProductCategory').value = data.category;
                    document.getElementById('editProductDescription').value = data.description;

                    // Update image preview if available
                    let imgPreview = document.getElementById('editImagePreview');
                    if (data.productImage) {
                        imgPreview.src = `uploaded_img/${data.productImage}`;
                    }
                })
                .catch(error => console.error("Error fetching product details:", error));

            // Show modal
            modal.style.display = "block";
        }

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

    <!-- Delete Product Modal -->
    <div class="modalDEL" id="delprod">
            <div class="modal-contentDEL">
                <span class="close" onclick="closeModal('delprod')">&times;</span>
                <div class="icon">
                    <span class="material-symbols-outlined">delete</span>
                </div>
                <p class="modal-message">Are you sure you want</p>
                <p class="modal-message">to delete this product?</p>
                <form action="artisanPage.php" method="POST" enctype="multipart/form-data" id="deleteForm1">
                    <input type="hidden" name="form_type" value="delete_product1">
                    <input type="hidden" id="deleteProductId" name="productId">
                    <button class="confirm-button">Yes</button>
                </form>
            </div>
        </div>

        <script>
            function confirmDeleteProduct(productId) {
                document.getElementById("deleteProductId").value = productId;
                document.getElementById("delprod").style.display = "flex";
            }

            // Optionally, handle form submission via AJAX
            document.getElementById('deleteForm1').onsubmit = function(event) {
                event.preventDefault();  // Prevent default form submission
                var productId = document.getElementById("deleteProductId").value;

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "artisanPage.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        location.reload(); // Refresh page after deletion
                    }
                };

                xhr.send("productId=" + productId + "&form_type=delete_product1");
            };
            
        </script>

    
    <!-- Delete Stories Modal -->
    <div class="modalDEL" id="delstor">
        <div class="modal-contentDEL">
            <span class="close" onclick="closeModal('delstor')">&times;</span>
            <div class="icon">
                <span class="material-symbols-outlined">delete</span>
            </div>
            <p class="modal-message">Are you sure you want</p>
            <p class="modal-message">to delete this stories?</p>
            <form action="artisanPage.php" method="POST" enctype="multipart/form-data" id="deleteForm">
                <input type="hidden" name="form_type" value="delete_story">
                <input type="hidden" id="deleteStorId" name="storyId">
                <button type="submit" class="confirm-button">Yes</button>
            </form>
        </div>
    </div>

    <script>
        function confirmDeleteStories(storyId) {
            document.getElementById("deleteStorId").value = storyId;
            document.getElementById("delstor").style.display = "flex";
        }

        // Optionally, handle form submission via AJAX
        document.getElementById('deleteForm').onsubmit = function(event) {
            event.preventDefault();  // Prevent default form submission
            var storyId = document.getElementById("deleteStorId").value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "artisanPage.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload(); // Refresh page after deletion
                }
            };

            xhr.send("storyId=" + storyId + "&form_type=delete_story");
        };
    </script>

    

    <!-- Add Product Modal -->
    <div class="modalEDIT" id="addProductModal">
        <div class="modal-contentEDIT">
            <span class="close" onclick="closeModal('addProductModal')">&times;</span>
            <div class="left-boxEDIT">
                <h2 class="modal-titleEDIT">Adding New Product</h2>
                <form action="artisanPage.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="add_product">
                    <div class="input-group23EDIT">
                        <label>Enter product name:</label>
                        <input type="text" name="product_name" id="editProductName" required>
                    </div>

                    <div class="input-group23EDIT">
                        <label>Select category of the craft:</label>
                        <select name="category" id="editProductCategory" required>
                            <option value="">-- Select Category --</option>
                            <option value="TextileandWeavingCrafts">Textile and Weaving Crafts</option>
                            <option value="PotteryandCeramics">Pottery and Ceramics</option>
                            <option value="WoodcraftandCarving">Woodcraft and Carving</option>
                            <option value="Metalcraft">Metalcraft</option>
                            <option value="BambooandRattanCrafts">Bamboo and Rattan Crafts</option>
                            <option value="Shell and Coral Crafts">Shell and Coral Crafts</option>
                            <option value="Paper and Fiber Crafts">Paper and Fiber Crafts</option>
                            <option value="Indigenous and Traditional Crafts">Indigenous and Traditional Crafts</option>
                        </select>
                    </div>

                    <div class="input-group23EDIT">
                        <label>Add Product Photo:</label>
                        <input type="file" id="editProductPhoto" class="wowers" name="product_image" accept="image/*">
                        <label for="editProductPhoto" class="upload-placeholder">
                            <img id="editImagePreview" src="" class="preview-img12">
                            <p>Click to upload photo</p>
                        </label>
                    </div>
            </div>

            <!-- Move right-boxEDIT outside the left-boxEDIT -->
            <div class="right-boxEDIT">
                <div class="input-grouprightEDIT"><br><br><br><br><br>
                    <textarea name="description" id="editProductDescription" rows="4" placeholder="Provide description about the product..." required></textarea>
                </div>
                <button type="submit" class="save-btn">Add Product</button>
            </div>
        </form>
        </div>
    </div>


    <script> 
        document.addEventListener("DOMContentLoaded", function () {
        const preview = document.getElementById("profilePreview");
        const text = preview.nextElementSibling;

        // Hide text if an image is already set
        if (preview.src && !preview.src.includes("default-placeholder.png")) {
            text.style.display = "none";
        }
    });

    function previewProfileImage(event) {
        const input = event.target;
        const preview = document.getElementById("profilePreview");
        const text = input.nextElementSibling.querySelector("p");

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                text.style.display = "none"; // Hide the text
                preview.style.display = "block"; // Ensure the image is visible
            };
            reader.readAsDataURL(input.files[0]);
        }
}
    </script>

<!-- Edit Details Modal -->
<div class="modal2" id="editDeets">
    <div class="modal-content2">
        <span class="close" onclick="closeModal('editDeets')">&times;</span>

        <div class="leftbbox">
        <form action="artisanPage.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form_type" value="edit_details">

                <p class="unaA">Basic Information</p>
                <h2 class="modalttitle">ARTISAN</h2> <br><br>
                
                <label for="name">NAME</label>
                <input type="text" class="leftInput" id="name" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required><br><br>
                
                <label for="age">AGE</label>
                <input type="number" class="leftInput" id="age" name="age" value="<?php echo htmlspecialchars($profile['age']); ?>" required><br><br>
                
                <label for="address">ADDRESS</label>
                <input type="text" class="leftInput" id="address" name="address" value="<?php echo htmlspecialchars($profile['address']); ?>" required><br><br>
                
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


<!-- JavaScript for Image Preview -->
<script> 
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



<!-- Add Story Modal -->
<div class="modalEDIT" id="addStory">
    <div class="modal-contentEDIT">
        <span class="close" onclick="closeModal('addStory')">&times;</span>

        <div class="left-boxEDIT">

            <h2 class="modal-titleEDIT">Adding a new story</h2>

            <form action="artisanPage.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="form_type" value="add_story">
                <div class="input-group23EDIT">
                    <label>Title of the Story:</label>
                    <input type="text" name="title_story" required>
                </div>
                
                <div class="input-group23EDIT">
                    <label>Select craft category:</label>
                    <select name="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="TextileandWeavingCrafts">Textile and Weaving Crafts</option>
                        <option value="PotteryandCeramics">Pottery and Ceramics</option>
                        <option value="WoodcraftandCarving">Woodcraft and Carving</option>
                        <option value="Metalcraft">Metalcraft</option>
                        <option value="BambooandRattanCrafts">Bamboo and Rattan Crafts</option>
                        <option value="ShellandCoralCrafts">Shell and Coral Crafts</option>
                        <option value="PaperandFiberCrafts">Paper and Fiber Crafts</option>
                        <option value="IndigenousandTraditionalCrafts">Indigenous and Traditional Crafts</option>
                    </select>
                </div>

                <div class="input-group23EDIT">
                    <label>Select Place:</label>
                    <select name="place" required>
                        <option value="">-- Select Place --</option>
                        <option value="Adams">Adams</option>
                        <option value="Bacarra">Bacarra</option>
                        <option value="Badoc">Badoc</option>
                        <option value="Bangui">Bangui</option>
                        <option value="Banna ">Banna </option>
                        <option value="BatacCity">Batac City</option>
                        <option value="Burgos">Burgos</option>
                        <option value="Carasi">Carasi</option>
                        <option value="Currimao">Currimao</option>
                        <option value="Dingras">Dingras</option>
                        <option value="Dumalneg">Dumalneg</option>
                        <option value="LaoagCity">Laoag City</option>
                        <option value="Marcos">Marcos</option>
                        <option value="NuevaEra">Nueva Era</option>
                        <option value="Pagudpud">Pagudpud</option>
                        <option value="Paoay">Paoay</option>
                        <option value="Pasuquin">Pasuquin</option>
                        <option value="Piddig">Piddig</option>
                        <option value="Pinili">Pinili</option>
                        <option value="SanNicolas">San Nicolas</option>
                        <option value="Sarrat">Sarrat</option>
                        <option value="Solsona">Solsona</option>
                        <option value="Vintar">Vintar</option>
                    </select>
                </div>
                
                <div class="input-group23EDIT">
                    <label for="product_image">Add Artisan Photo:</label>
                        <input type="file" id="product_image" name="artisan_image" class="box" accept="image/*" onchange="previewProductImage(event)">
                        <label for="product_image" class="upload-placeholder22">
                        <img id="productPreview" src="artisan_image/default_product.png" alt="Click To Add Photo" class="preview-img22">
                        <p>Click to add photo</p>
                        </label>
                </div>


                <script> 
                    function previewProductImage(event) {
                    const input = event.target;
                    const preview = document.getElementById("productPreview");
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

        </div>

        <div class="right-box">
            
            <br><br><br><br>
            <div class="input-groupright">
                <textarea name="description" rows="4" placeholder="Write story here..." required></textarea>
            </div>
            
            <button type="submit" class="save-btn">Save</button>
        </form>
    </div>

        </div>
    
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
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
