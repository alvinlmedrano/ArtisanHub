<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Register </title>
    <script src="js/jQuery3.4.1.js"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
    <link rel="stylesheet" href="css/artisanpage.css">
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
    
    <div class="profile-container">
        <h2>MY PROFILE</h2>
        <div class="profile-content">
            <img src="profile-image.jpg" alt="Profile Picture" class="profile-img">
            <div class="profile-details">
                <p class="sizesize"><strong>NAME:</strong> Magdalena Gamayo</p>
                <p class="sizesize"><strong>AGE:</strong> 99</p>
                <p class="sizesize"><strong>ADDRESS:</strong> Pinili, Ilocos Norte</p>
                
                <div class="profile-buttons">
                    <button class="btn" onclick="openModal('editDeets')">Edit details</button>
                    <button class="btn" onclick="openModal('addProductModal')">Add Product</button>
                    <button class="btn" onclick="openModal('addStory')">Add Story</button>
                </div>
            </div>
        </div>

        <br><br>
        <h2>Products</h2>
    </div>

    <div class="small-container">
    <div class="row">
        <?php
        @include 'connection.php';
        $select = mysqli_query($conn, "SELECT * FROM FruitInventory");

        if (mysqli_num_rows($select) > 0) {
            while ($product = mysqli_fetch_assoc($select)) {
        ?>
        <div class="container">
            <div class="card">
                <div class="img-container">
                    <img src="uploaded_img/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                </div>
                <h5 class="product_name"><?php echo $product['name']; ?></h5>
            </div>
        </div>
        <?php 
            }
        }
        ?>
    </div>
</div>

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addProductModal')">&times;</span>
    
            <div class="left-box">
    
                <h2 class="modal-title">Adding a new product</h2>
    
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <label>Enter product name:</label>
                        <input type="text" name="product_name" required>
                    </div>
                    
                    <div class="input-group">
                        <label>Select category of the craft:</label>
                        <select name="category" required>
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
                    
                    <div class="input-group">
                        <label for="productPhoto" class="upload-placeholder">
                            <img id="imagePreview" src="img/placeholder.png" alt="Upload Image" class="preview-img">
                            <span>Click to upload product photo</span>
                        </label>
                        <input type="file" id="productPhoto" accept="image/*" onchange="previewImage(event)" hidden>
                    </div>
                    
                    
                    <div class="preview-container">
                        <img id="imagePreview" src="" alt="Product Preview" class="preview-img">
                    </div>
    
                    <script> 
                        function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
                    </script>
    
            </div>
    
            <div class="right-box">
                
                <br><br><br><br>
                <div class="input-group">
                    <textarea name="description" rows="4" placeholder="Provide description about the product..." required></textarea>
                </div>
                
                <button type="submit" class="save-btn">Save</button>
            </form>
        </div>
    
            </div>
        
    </div>

<!-- Edit Details Modal -->
<div class="modal2" id="editDeets">
    <div class="modal-content2">
        <span class="close" onclick="closeModal('editDeets')">&times;</span>
        
        <div class="leftbbox">
            <form>
                <p class="unaA">Basic Information</p>
                <h2 class="modalttitle">ARTISAN</h2> <br><br>

                <label for="name">NAME</label>
                <input type="text" class="leftInput" id="name" name="name"><br><br>
        
                <label for="age">AGE</label>
                <input type="number" class="leftInput" id="age" name="age"><br><br>
        
                <label for="address">ADDRESS</label>
                <input type="text" class="leftInput" id="address" name="address"><br><br>
                
                <div class="weyt">
                    <button type="submit" class="savebbtn">SAVE</button>
                </div>
                
                
        </div>

        <div class="rightbbox">
            
            <br>
            
            <div class="inputggroup">

                <label for="profile">PROFILE</label>
                
                <label for="productPhoto" class="uploadpplaceholder">
                    <img id="imagePreview" src="img/placeholder.png" alt="Upload Image" class="preview-img">
                    <span>Click to upload product photo</span>
                </label>
                <input type="file" id="productPhoto" accept="image/*" onchange="previewImage(event)" hidden>
            </div>

            <script> 
                function previewImage(event) {
                    const input = event.target;
                    const preview = document.getElementById('imagePreview');

                    if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                            preview.src = e.target.result;
                        };

                        reader.readAsDataURL(input.files[0]);
                    }}
            </script>
            
            
        </form>
    </div>

        </div>
    
</div>

<!-- Add Story Modal -->
<div class="modal" id="addStory">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addStory')">&times;</span>

        <div class="left-box">

            <h2 class="modal-title">Adding a new story</h2>

            <form action="#" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Title of the Story:</label>
                    <input type="text" name="title_story" required>
                </div>
                
                <div class="input-group">
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

                <div class="input-group">
                    <label>Select Place:</label>
                    <select name="category" required>
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
                
                <div class="input-group">
                    <label for="productPhoto" class="upload-placeholder">
                        <img id="imagePreview" src="img/placeholder.png" alt="Upload Image" class="preview-img">
                        <span>Click to upload artisan photo</span>
                    </label>
                    <input type="file" id="productPhoto" accept="image/*" onchange="previewImage(event)" hidden>
                </div>
                
                
                <div class="preview-container">
                    <img id="imagePreview" src="" alt="Product Preview" class="preview-img">
                </div>

                <script> 
                    function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

                </script>

        </div>

        <div class="right-box">
            
            <br><br><br><br>
            <div class="input-group">
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
