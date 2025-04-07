<?php
@include 'connection.php';
$select = mysqli_query($conn, "SELECT * FROM product");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CONTENT | E-COMMERCE WEBSITE</title>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative&family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="css/product.css">
</head>
<body>

<section>
    <div id="1"></div>
    <script>
        load("header.html");
        function load(url) {
            req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(1).innerHTML = req.responseText;
        }
    </script>

    <script src="js/header.js"></script>

    <div class="small-container">
    <div class="row">
        <?php
        if (mysqli_num_rows($select) > 0) {
            while ($product = mysqli_fetch_assoc($select)) {
        ?>
        <div class="container" onclick="window.location.href='product-details.php?productId=<?php echo $product['productId']; ?>'">

            <div class="card">
                <div class="img-container">
                    <img src="../artisan-main/uploaded_img/<?php echo $product['productImage']; ?>" alt="<?php echo $product['nameOfProduct']; ?>">
                </div>
                <h5 class="product_name"><?php echo $product['nameOfProduct']; ?></h5>
            </div>
        </div>
        <?php 
            }
        }
        ?>
    </div>
</div>

<script>
    function openModal(url) {
        window.location.href = url;
    }
</script>



    <!-- FOOTER -->
    <div id="4"></div>
    <script>
        load("footer.html");
        function load(url) {
            req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(4).innerHTML = req.responseText;
        }
    </script>

    <script>
        function openModal(image, name, category, description) {
            document.getElementById("modalImage").src = "uploaded_img/" + image;
            document.getElementById("modalTitle").textContent = name;
            document.getElementById("modalCategory").innerHTML = "<strong>Category:</strong> " + category;
            document.getElementById("modalDescription").textContent = description;
            document.getElementById("seeProductModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("seeProductModal").style.display = "none";
        }
    </script>
</body>
</html>
